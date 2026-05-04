<?php

namespace HMS\Repositories\Printers;

use HMS\Entities\Printers\Printer;

interface PrinterRepository
{
    /**
     * Save Printer to the DB.
     *
     * @param Printer $printer
     */
    public function save(Printer $pinter);

    /**
     * Remove a Printer from the DB.
     *
     * @param Printer $printer
     */
    public function remove(Printer $printer);

    public function paginateAll($perPage = 15, $pageName = 'page');
}
