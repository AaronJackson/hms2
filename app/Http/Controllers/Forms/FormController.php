<?php

namespace App\Http\Controllers\Forms;

use App\Http\Controllers\Controller;
use HMS\Repositories\Forms\FormRepository;
use HMS\Repositories\Forms\FormResponseRepository;
use HMS\Entities\Forms\Form;

class FormController extends Controller
{
    protected $formRepository;

    protected $formResponseRepository;

    public function __construct(
        FormRepository $formRepository,
        FormResponseRepository $formResponseRepository
    )
    {
        $this->formRepository = $formRepository;
        $this->formResponseRepository = $formResponseRepository;
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

    public function responses(Form $form)
    {
        return view('forms.responses')->with('form', $form);
    }
}
