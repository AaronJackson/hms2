<?php

namespace HMS\Repositories\Printers;

use HMS\Entities\User;
use HMS\Entities\Printers\PrinterJob;

interface PrinterJobRepository
{
    /**
     * Save Printer to the DB.
     *
     * @param Printer $printer
     */
    public function save(PrinterJob $pinterJob);

    public function paginateAll($perPage = 15, $pageName = 'page');

    public function paginateByUser(User $user, $perPage = 15, $pageNname = 'page');
}
