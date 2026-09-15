<?php

namespace App\Http\Controllers\Api\Forms;

use App\Http\Controllers\Controller;
use HMS\Repositories\Forms\FormRepository;
use HMS\Repositories\Forms\FormResponseRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Tools\ToolRepository;
use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Entities\User;
use App\Http\Resources\Forms\FormResponseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    /**
     * @var FormRepository
     */
    protected $formRepository;

    /**
     * @var FormResponseRepository
     */
    protected $formResponseRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var TooleRepository
     */
    protected $toolRepository;

    /**
     * Create a new controller instance.
     *
     * @param FormRepository $formRepository
     */
    public function __construct(
        FormRepository $formRepository,
        FormResponseRepository $formResponseRepository,
        RoleRepository $roleRepository,
        ToolRepository $toolRepository
    ) {
        $this->formRepository = $formRepository;
        $this->formResponseRepository = $formResponseRepository;
        $this->roleRepository = $roleRepository;
        $this->toolRepository = $toolRepository;

        $this->middleware('can:forms.respond')->only(['getModel', 'putResponse']);
    }

    public function getModel(Form $form)
    {
        if (! $form->getPublished() && Gate::denies('forms.edit')) {
            throw new AuthorizationException('You do not have permission to view this form.');
        }

        $model = $form->getJsonDefinition();

        // Helpers to autofill dropdowns, radio and checkbox groups.
        array_walk_recursive($model, function (&$item, $key) {
            if ($key === "choices" && $item === "hms:teams") {
                $item = array_map(function ($role) {
                    return [
                        'value' => $role->getName(),
                        'text' => $role->getDisplayName()
                    ];
                }, $this->roleRepository->findAllTeams());
            }

            if ($key === "choices" && $item === "hms:tools") {
                $item = array_map(function ($tool) {
                    return $tool->getDisplayName();
                }, $this->toolRepository->findAll());
            }
        });

        return response()->json($model);
    }

    public function putResponse(Form $form, Request $request)
    {
        if (! $form->getPublished()) {
            throw new AuthorizationException('This form is not accepting responses at this time.');
        }

        if (! $form->getMaxResponses() > 0) {
            // Count the number of current responses and reject if too many.
        }

        $formResponse = new FormResponse();
        $formResponse->setForm($form);
        $formResponse->setResponder(Auth::user());
        $formResponse->setResponseJson($request->getContent());
        $formResponse->setHidden(false);

        $this->formResponseRepository->save($formResponse);
    }

    public function getResponses(Request $request)
    {
        $formResponses = $this->formResponseRepository->paginateAll();

        return response()->json(FormResponseResource::collection($formResponses));
    }
}
