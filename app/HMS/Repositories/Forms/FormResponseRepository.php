<?php

namespace HMS\Repositories\Forms;

use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;

interface FormResponseRepository
{
    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');


    public function save(FormResponse $formResponse);

}
