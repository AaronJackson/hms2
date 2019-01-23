<?php

namespace HMS\Repositories\Doctrine;

use HMS\Entities\Role;
use HMS\Entities\Email;
use Doctrine\ORM\EntityRepository;
use HMS\Repositories\EmailRepository;

// TODO: findByUserPaginate(????);
class DoctrineEmailRepository extends EntityRepository implements EmailRepository
{
    /**
     * @param $id
     *
     * @return null|Email
     */
    public function findOneById($id)
    {
        return parent::findOneById($id);
    }

    /**
     * @param Role $role
     *
     * @return array
     */
    public function findByRole(Role $role)
    {
        return parent::findByRole($role);
    }

    /**
     * Save Email to the DB.
     *
     * @param Email $email
     */
    public function save(Email $email)
    {
        $this->_em->persist($email);
        $this->_em->flush();
    }
}
