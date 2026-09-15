<?php

namespace HMS\Repositories\Forms\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Repositories\Forms\FormRepository;
use HMS\Repositories\Forms\FormResponseRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineFormResponseRepository extends EntityRepository implements FormResponseRepository
{
    use PaginatesFromRequest;

    public function save(FormResponse $formResponse)
    {
        $this->_em->persist($formResponse);
        $this->_em->flush();
    }
}
