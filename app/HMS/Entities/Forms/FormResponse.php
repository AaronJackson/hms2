<?php

namespace HMS\Entities\Forms;

use HMS\Entities\User;
use Carbon\Carbon;

class FormResponse
{
    protected $id;

    protected $form;

    protected $responder;

    protected $responseJson;

    protected $comment;

    protected $hidden;

    protected $createdAt;

    protected $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    public function getResponder()
    {
        return $this->responder;
    }

    public function setResponder(User $responder) {
        $this->responder = $responder;

        return $this;
    }

    public function getResponseJson()
    {
        return $this->responseJson;
    }

    public function setResponseJson($responseJson)
    {
        $this->responseJson = $responseJson;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    public function getHidden()
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}
