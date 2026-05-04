<?php

namespace HMS\Repositories\Printers\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Printers\PrinterJob;
use HMS\Entities\User;
use HMS\Repositories\Printers\PrinterJobRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrinePrinterJobRepository extends EntityRepository implements PrinterJobRepository
{
    use PaginatesFromRequest;

    public function paginateByUser(User $user, $perPage = 15, $pageName = 'page')
    {
        $q = parent::createQueryBuilder('printer_jobs')
            ->where('printer_jobs.user = :user_id');

        $q->orderBy('printer_jobs.createdAt', 'DESC');
        $q = $q->setParameter('user_id', $user->getId())->getQuery();

        return $this->paginate($q, $perPage, $pageName);
    }

    /**
     * Save Printer to the DB.
     *
     * @param Printer $printer
     */
    public function save(PrinterJob $printerJob)
    {
        $this->_em->persist($printerJob);
        $this->_em->flush();
    }

}
