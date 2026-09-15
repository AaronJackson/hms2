<?php

namespace HMS\Repositories\Forms\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Forms\Form;
use HMS\Repositories\Forms\FormRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineFormRepository extends EntityRepository implements FormRepository
{
    use PaginatesFromRequest;

}
