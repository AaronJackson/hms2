<?php

namespace App\Http\Resources\Forms;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \HMS\Entities\Forms\FormResponse
 */
class FormResponseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->getId(),
            'responseJson' => $this->getResponseJson(),
            'comment' => $this->getComment(),
            'hidden' => $this->getHidden(),
        ];
    }
}
