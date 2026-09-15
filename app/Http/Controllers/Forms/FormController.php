<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use HMS\Repositories\Forms\FormRepository;
use HMS\Entities\Forms\Form;

class FormController extends Controller
{
    protected $formRepository;

    public function __construct(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forms = $this->formRepository->paginateAll();

        return view('forms.index')->with('forms', $forms);
    }

    public function view(Form $form)
    {
        return view('forms.view')->with('form', $form);
    }
}
