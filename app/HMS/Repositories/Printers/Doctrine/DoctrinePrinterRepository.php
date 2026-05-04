<?php

namespace HMS\Repositories\Printers\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Printers\Printer;
use HMS\Repositories\Printers\PrinterRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrinePrinterRepository extends EntityRepository implements PrinterRepository
{
    use PaginatesFromRequest;

    /**
     * Save Printer to the DB.
     *
     * @param Printer $printer
     */
    public function save(Printer $printer)
    {
        $this->_em->persist($printer);
        $this->_em->flush();
    }

    /**
     * Remove a Printer from the DB.
     *
     * @param Printer $printer
     */
    public function remove(Printer $printer)
    {
        $this->_em->remove($printer);
        $this->_em->flush();
    }
}
