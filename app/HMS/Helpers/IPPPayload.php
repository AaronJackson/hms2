<?php

namespace HMS\Helpers;

use HMS\Entities\Printers\IPPTypes;

// https://datatracker.ietf.org/doc/html/rfc8010#section-3.1.1
class IPPPayload
{
    protected $versionNumber;
    protected $operationId;   // (request) or status code (response)
    protected $requestId;
    protected $attributeGroups[];
    protected $data;

    public function __construct()
    {

    }

    public function setVersionNumber($versionNumber, $encoded = false)
    {
        if ($ended) {

        } else {
            $this->versionNumber = $versionNumber;
        }
    }

    public function getVersionNumber($encoded = false)
    {
        if ($encoded) {

        }

        return $this->versionNumber;
    }

    public function encode()
    {

    }

    public function decode($payload)
    {

    }

}
