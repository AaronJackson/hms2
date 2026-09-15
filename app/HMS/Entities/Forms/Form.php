<?php

namespace HMS\Entities\Forms;

use Carbon\Carbon;

class Form
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $jsonDefinition;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var null|Carbon
     */
    protected $archivedDate;

    /*
     * @var int
     */
    protected $maxResponses;

    /*
     * @var null|string
     */
    protected $notificationKey;

    /**
     * Create a new form
     *
     * @param string $name
     * @param string $jsonDefinition
     */
    public function __construct(
        string $name,
        string $jsonDefinition
    ) {
        $this->name = $name;
        $this->jsonDefinition = $jsonDefinition;
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the name of the form.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the form name.
     *
     * @param string $name
     *
     * @return object
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the survey.js JSON definition.
     *
     * @return string
     */
    public function getJsonDefinition()
    {
        return $this->jsonDefinition;
    }

    /**
     * Sets the survey.js JSON definition.
     *
     * @param string $jsonDefinition
     *
     * @return self
     */
    public function setJsonDefinition($jsonDefinition): self
    {
        $this->jsonDefinition = $jsonDefinition;

        return $this;
    }

    /**
     * Gets the form's visibility / published status.
     *
     * @return bool
     */
    public function getPublished(): bool
    {
        return $this->published;
    }

    /**
     * Sets the form's visibility / published status.
     *
     * @param string $published
     *
     * @return self
     */
    public function setPublished($published): self
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get the archived date.
     *
     * @return null|Carbon
     */
    public function getArchivedDate()
    {
        return $this->archivedDate;
    }

    /**
     * Sets the date the form was archived.
     *
     * @param string $published
     *
     * @return self
     */
    public function setArchivedDate($archivedDate): self
    {
        $this->archivedDate = $archivedDate;

        return $this;
    }

    /**
     * Get the max number of responses per member.
     *
     * @return int
     */
    public function getMaxResponses()
    {
        return $this->maxResponses;
    }

    /**
     * Sets the max number of responses per member (0 is unlimited)
     *
     * @param int $maxResponses
     *
     * @return self
     */
    public function setMaxResponses($maxResponses): self
    {
        $this->maxResponses = $maxResponses;

        return $this;
    }

    /**
     * Gets the form response key used for notification routing.
     *
     * @return null|string
     */
    public function getNotificationKey()
    {
        return $this->notificationKey;
    }

    /**
     * Sets the form response key used for notification routing.
     *
     * @param string $notificationKey
     *
     * @return self
     */
    public function setNotificationkey($notificationKey): self
    {
        $this->notificationKey = $notificationKey;

        return $this;
    }
}
