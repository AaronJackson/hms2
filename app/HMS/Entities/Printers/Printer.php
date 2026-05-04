<?php

namespace HMS\Entities\Printers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;
use HMS\Entities\User;
use HMS\Traits\Entities\Timestampable;

class Printer
{
    use Timestampable;

    /**
     * @var int
     */
    protected $printerId;

    /**
     * @var string printerName The frendly name of the printer
     */
    protected $printerName;

    /**
     * @var null|string ippUri The ipps:// or ipp:// address of the printer
     */
    protected $ippUri;

    /**
     * @var int costA4Black The cost in pence per black and white A4 sheet
     */
    protected $costA4Black;

    /**
     * @var int costA4Colour The cost in pence per colour A4 sheet
     */
    protected $costA4Colour;

    /**
     * @var int costA3Black The cost in pence per black and white A3 sheet
     */
    protected $costA3Black;

    /**
     * @var int costA3Colour The cost in pence per colour A3 sheet
     */
    protected $costA3Colour;

    protected $deletedAt;

    protected $updatedAt;

    protected $createdAt;

    public function getPrinterId()
    {
        return $this->printerId;
    }

    public function getPrinterName()
    {
        return $this->printerName;
    }

    public function getIppUri()
    {
        return $this->ippUri;
    }

    public function getCostA4Black()
    {
        return $this->costA4Black;
    }

    public function getCostA4Colour()
    {
        return $this->costA4Colour;
    }

    public function getCostA3Black()
    {
        return $this->costA3Black;
    }

    public function getCostA3Colour()
    {
        return $this->costA3Colour;
    }

    public function getUserEndpoint(User $user)
    {
        $payload = [
            'u' => $user->getId(),
            'p' => $this->printerId
        ];

        $key = config('hms.printers_key', null);
        return JWT::encode($payload, $key, 'HS256');
    }

    public function getStatus()
    {
        $curlOptions = [
            ['key' => CURLOPT_SSL_VERIFYPEER, 'value' => false],
            ['key' => CURLOPT_SSL_VERIFYHOST, 'value' => false],
        ];

        try {
            $printer = new \obray\ipp\Printer($this->ippUri, '', '', $curlOptions);
            $attributes = $printer->getPrinterAttributes();
            return $attributes->statusCode;
        } catch (\obray\ipp\exceptions\NetworkError $e) {
            return 'network error';
        }
    }

    public function forward($body)
    {
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/ipp',
                'content' => $body,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];

        $context = stream_context_create($options);

        $fp = fopen($this->ippUri, 'r', false, $context);
        $response = '';
        while (!feof($fp)) {
            $response .= fread($fp, 1024);
        }
        fclose($fp);

        return $response;
    }
}
