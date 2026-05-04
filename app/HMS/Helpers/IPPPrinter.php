<?php

namespace HMS\Helpers;

use HMS\Entities\Printers\Printer;
use HMS\Entities\Printers\PrinterJob;
use GravityMedia\Ghostscript\Ghostscript;
use Symfony\Component\Process\Process;

class IPPPrinter
{
    protected $body;
    protected $ippPayload;

    protected $printer;

    /**
     * Creates an instance of the IPPPrinter helper.
     *
     */
    public function __construct($body, $printer)
    {
        $this->body = $body;
        $this->printer = $printer;

        $this->ippPayload = new \obray\ipp\transport\IPPPayload();
        $this->ippPayload->decode($this->body);

    }

    public function isGetPrinterAttributes()
    {
        if (strlen($this->body) < 8) {
            return false;
        }

        return substr($this->body, 2, 2) == chr(0x00) . chr(0x0b);
    }

    public function updatePrinterUri($old, $new)
    {
        return $this->body = str_replace(
            'printer-uri' . chr(0) . chr(strlen($old)) . $old,
            'printer-uri' . chr(0) . chr(strlen($new)) . $new,
            $this->body);
    }

    public function validatePclJob()
    {
        // The first few bytes of a PCL job should be `<ESC><0xFF>2345X@PJL`
        return true;
        return str_starts_with($this->body, chr(0x1B) . '%-12345X@PJL');
    }

    public function validatePdfJob()
    {
        return true;
        return str_starts_with($this->body, '%!PS-Adobe-3.0');
    }

    protected function parsePclFile($filename)
    {
        $attr = [];

        $fd = fopen($filename, 'r');
        while (($line = fgets($fd)) !== false) {
            if (str_starts_with($line, '@PJL JOB NAME')) {
                preg_match('/@PJL JOB NAME = \"([^\"]+)\"/', $line, $parts);
                if (sizeof($parts) == 2) {
                    $attr['NAME'] = $parts[1];
                }
            }

            if (str_starts_with($line, '@PJL SET')) {
                preg_match('/@PJL SET ([^ ]*)=(.*)/', $line, $parts);
                if (sizeof($parts) == 3) {
                    $attr[$parts[1]] = $parts[2];
                }
            }

            if (str_starts_with($line, '@PJL ENTER LANGAUGE')) {
                break;
            }
        }

        fclose($fd);

        return $attr;
    }

    protected function parsePdfFile($filename)
    {
        $attr = [];

        $fd = fopen($filename, 'r');
        while (($line = fgets($fd)) !== false) {
            if (str_starts_with($line, '%%Title:')) {
                preg_match('/%%Title: \(([^\"]+)\)/', $line, $parts);
                if (sizeof($parts) == 2) {
                    $attr['Name'] = $parts[1];
                }
            }

            if (str_starts_with($line, '%%BeginFeature:')) {
                preg_match('/%%BeginFeature: \*([^ ]*) (.*)/', $line, $parts);
                if (sizeof($parts) == 3) {
                    $attr[$parts[1]] = $parts[2];
                }
            }

            if (str_starts_with($line, '%%EndSetup')) {
                break;
            }
        }

        fclose($fd);

        return $attr;
    }

    public function hasDocument()
    {
        if ($this->ippPayload->document)
            file_put_contents('/tmp/ippPayload', print_r($this->ippPayload, true));
        return (!! $this->ippPayload->document);
    }

    public function bodyToJob()
    {
        if ($this->hasDocument()) {
            $printerJob = new PrinterJob($this->printer);

            $temporaryPostscript = tempnam('/tmp', 'ippgs_');
            file_put_contents($temporaryPostscript, $this->ippPayload->document);

            $pclAttributes = $this->parsePdfFile($temporaryPostscript);
            if (array_key_exists('Name', $pclAttributes)) {
                $printerJob->setJobName($pclAttributes['Name']);
            }

            file_put_contents('/tmp/attrib', print_r($pclAttributes, true));

            $ghostscript = new Ghostscript([
                'quiet' => false
            ]);

            $inkCoverage = $ghostscript->createInkcovDevice();
            $process = $inkCoverage->createProcess($temporaryPostscript);
            $process->start();
            $process->wait();

            unlink($temporaryPostscript);

            $output = $process->getOutput();
            $cmyk = preg_grep('/CMYK OK$/', explode("\n", $output));

            $bwPages = 0;
            $colourPages = 0;
            foreach ($cmyk as $page) {
                if (array_key_exists('RENDERMODE', $pclAttributes) && $pclAttributes['RENDERMODE'] == 'GRAYSCALE') {
                    $bwPages++;
                    continue;
                }

                preg_match('/ ([0-9\.]{7})  ([0-9\.]{7})  ([0-9\.]{7})  ([0-9\.]{7}) /', $page, $cmykCoverage);
                if (sizeof($cmykCoverage) != 5) {
                    throw new Exception('GhostScript CMYK Formatting issue');
                }

                $cyanCoverage = (float)$cmykCoverage[1];
                $magentaCoverage = (float)$cmykCoverage[2];
                $yellowCoverage = (float)$cmykCoverage[3];
                $blackCoverage = (float)$cmykCoverage[4];

                if ($cyanCoverage == $magentaCoverage && $magentaCoverage == $yellowCoverage && $yellowCoverage == $blackCoverage) {
                    $bwPages++;
                } elseif ($cyanCoverage > 0 || $magentaCoverage > 0 || $yellowCoverage) {
                    $colourPages++;
                } elseif ($blackCoverage > 0) {
                    $bwPages++;
                }
            }

            if (array_key_exists('PageSize', $pclAttributes) &&
                ($pclAttributes['PageSize'] == 'A3' || $pclAttributes['PageSize'] == 'Ledger')) {
                $printerJob->setPagesA3Black($bwPages);
                $printerJob->setPagesA3Colour($colourPages);
            } else {
                $printerJob->setPagesA4Black($bwPages);
                $printerJob->setPagesA4Colour($colourPages);
            }

            return $printerJob;
        }

        return null;
    }

    public function getBody()
    {
        return $this->body;
    }
}
