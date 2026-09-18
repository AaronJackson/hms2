<?php

namespace HMS\Repositories\Forms\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\User;
use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Repositories\Forms\FormRepository;
use HMS\Repositories\Forms\FormResponseRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineFormResponseRepository extends EntityRepository implements FormResponseRepository
{
    use PaginatesFromRequest;

    public function paginateForForm(Form $form, $includeArchived = false, $perPage = 15, $pageName = 'page')
    {
        $queryBuilder = $this->createQueryBuilder('responses');

        $hiddenQuery = '';
        if (! $includeArchived) {
            $hiddenQuery .= " and responses.hidden = false";
        }

        $queryBuilder->where('responses.form = :form_id' . $hiddenQuery);
        $queryBuilder->setParameter('form_id', $form->getId());

        $queryBuilder->orderBy('responses.createdAt', 'DESC');
        $query = $queryBuilder->getQuery();

        return $this->paginate($query, $perPage, $pageName);
    }

    public function countUserResponsesForForm(Form $form, User $user)
    {
        $qb = parent::createQueryBuilder('responses')
            ->select('COUNT(responses.id)')
            ->where('responses.responder = :user');

        $qb->setParameter('user', $user);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function save(FormResponse $formResponse)
    {
        $this->_em->persist($formResponse);
        $this->_em->flush();
    }
}
