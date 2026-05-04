<?php

namespace HMS\Entities\Printers;

use Carbon\Carbon;
use HMS\Traits\Entities\Timestampable;

class PrinterJob
{
    use Timestampable;

    /**
     * @var int
     */
    protected $jobId;

    protected $printer;

    protected $jobName;

    protected $pagesA4Black;
    protected $pagesA4Colour;
    protected $pagesA3Black;
    protected $pagesA3Colour;

    protected $deletedAt;

    protected $updatedAt;

    protected $createdAt;

    public function __construct($printer)
    {
        $this->printer = $printer;
    }

    public function setJobName($jobName)
    {
        $this->jobName = $jobName;
    }

    public function setPagesA4Black($pages)
    {
        $this->pagesA4Black = $pages;
    }

    public function setPagesA4Colour($pages)
    {
        $this->pagesA4Colour = $pages;
    }

    public function setPagesA3Black($pages)
    {
        $this->pagesA3Black = $pages;
    }

    public function setPagesA3Colour($pages)
    {
        $this->pagesA3Colour = $pages;
    }

    public function __toString()
    {
        $string = 'Job';
        if ($this->jobName) {
            $string = 'Job [' . $this->jobName . ']';
        }
        $string .= ' to ' . $this->printer->getPrinterName() . ' (';

        if ($this->pagesA4Black) $string .= $this->pagesA4Black . ' x A4 Black/White';
        if ($this->pagesA4Colour) $string .= $this->pagesA4Colour . ' x A4 Colour';
        if ($this->pagesA3Black) $string .= $this->pagesA3Black . ' x A3 Black/White';
        if ($this->pagesA3Colour) $string .= $this->pagesA3Colour . ' x A3 Colour';

        $string .= ')';

        return $string;
    }

    public function getCost()
    {
        return ($this->pagesA4Black * $this->printer->getCostA4Black()) +
               ($this->pagesA4Colour * $this->printer->getCostA4Colour()) +
               ($this->pagesA3Black * $this->printer->getCostA3Black()) +
               ($this->pagesA3Colour * $this->printer->getCostA3Colour());
    }
}
