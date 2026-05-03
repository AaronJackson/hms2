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

    protected $colour;

    protected $pageSize;

    protected $pageCount;

    protected $deletedAt;

    protected $updatedAt;

    protected $createdAt;
}
