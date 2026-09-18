<?php

namespace App\Events\Forms;

use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Queue\SerializesModels;

class FormResponseReceived
{
    use SerializesModels;

    /**
     * @var Form
     */
    public $form;

    /**
     * @var FormResponse
     */
    public $formResponse;

    /**
     * @var Role
     */
    public $role;

    /**
     * Create a new event instance.
     *
     * @param Form $form
     * @param FormResponse $formResponse
     *
     * @return void
     */
    public function __construct(Form $form, FormResponse $formResponse)
    {
        $this->form = $form;
        $this->formResponse = $formResponse;
    }

    public function setRole(Role $role)
    {
        $this->role = $role;
    }
}
