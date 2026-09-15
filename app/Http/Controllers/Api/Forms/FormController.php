<?php

namespace App\Http\Controllers\Api\Forms;

use App\Http\Controllers\Controller;
use HMS\Repositories\Forms\FormRepository;
use HMS\Entities\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FormController extends Controller
{
    /**
     * @var FormRepository
     */
    protected $formRepository;

    /**
     * Create a new controller instance.
     *
     * @param FormRepository $formRepository
     */
    public function __construct(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    public function getModel(Form $form)
    {
        return response()->json($form->getJsonDefinition());
    }
}
