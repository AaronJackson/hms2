<?php

namespace App\Http\Controllers\Api\Forms;

use App\Events\Forms\FormResponseReceived;
use App\Http\Controllers\Controller;
use HMS\Repositories\Forms\FormRepository;
use HMS\Repositories\Forms\FormResponseRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Tools\ToolRepository;
use HMS\Repositories\TeamRepository;
use HMS\Entities\Forms\Form;
use HMS\Entities\Forms\FormResponse;
use HMS\Entities\User;
use App\Http\Resources\Forms\FormResponseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class FormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param FormRepository $formRepository
     */
    public function __construct(
        protected FormRepository $formRepository,
        protected FormResponseRepository $formResponseRepository,
        protected RoleRepository $roleRepository,
        protected ToolRepository $toolRepository
    ) {
        $this->formRepository = $formRepository;
        $this->formResponseRepository = $formResponseRepository;
        $this->roleRepository = $roleRepository;
        $this->toolRepository = $toolRepository;

        $this->middleware('feature:forms');

        $this->middleware('can:forms.respond')->only(['getModel', 'putResponse']);
        $this->middleware('can:forms.viewResponses')->only(['putComment', 'hideResponse']);
    }

    public function getModel(Form $form)
    {
        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.respond',
            'forms.respond'
        ])) {
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

            if ($key === "choices" && $item === "hms:tools:induction") {
                $item = array_map(function ($tool) {
                    return $tool->getDisplayName();
                }, $this->toolRepository->findAll());
            }
        });

        return response()->json($model);
    }

    public function putResponse(Form $form, Request $request)
    {
        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.respond',
            'forms.respond'
        ])) {
            throw new AuthorizationException('You do not have permission to view this form.');
        }

        if ($form->getMaxResponses() > 0 && $this->formResponseRepository->countUserResponsesForForm($form, Auth::user()) > $form->getMaxResponses()) {
            flash('You have responded to this form more than the permitted number of times.');
            return redirect()->back();
        }


        // We need to find all possible keys for a form.
        $formDefinition = $form->getJsonDefinition();
        $validationRules = [];
        array_walk_recursive($formDefinition, function (&$value, $key) use (&$validationRules) {
            if ($key === 'name') {
                $validationRules[$value] = 'sometimes|required';
                $validationRules[$value . '-Comment'] = 'sometimes|required';
            }
        });

        $validatedData = $request->validate($validationRules);

        $formResponse = new FormResponse();
        $formResponse->setForm($form);
        $formResponse->setResponder(Auth::user());
        $formResponse->setResponseJson($validatedData);
        $formResponse->setHidden(false);

        $this->formResponseRepository->save($formResponse);

        if ($form->getNotificationKey() && array_key_exists($form->getNotificationKey(), $validatedData)) {
            $event = new FormResponseReceived($form, $formResponse);
            $target = $validatedData[$form->getNotificationKey()];

            // There are potentially other targets at some point...
            if (str_starts_with($target, 'team.')) {
                $team = $this->roleRepository->findOneByName($target);
                if ($team) {
                    $event->setRole($team);
                }
            }
            event($event);
        }
    }

    public function putComment(FormResponse $formResponse, Request $request)
    {
        $form = $this->formRepository->findOneById($formResponse->getForm()->getId());

        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.viewResponses',
            'forms.viewResponses'
        ])) {
            throw new AuthorizationException('You do not have permission to view this form.');
        }

        $validatedData = $request->validate([
            'comment' => 'present|max:255',
        ]);

        $formResponse->setComment($validatedData['comment']);
        $this->formResponseRepository->save($formResponse);
    }

    public function hideResponse(FormResponse $formResponse, Request $request)
    {
        $form = $this->formRepository->findOneById($formResponse->getForm()->getId());

        if (Gate::none([
            'forms.' . $form->getPermissionName() . '.viewResponses',
            'forms.viewResponses'
        ])) {
            throw new AuthorizationException('You do not have permission to view this form.');
        }

        $formResponse->setHidden(true);
        $this->formResponseRepository->save($formResponse);
    }
}
