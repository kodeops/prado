<?php
namespace kodeops\Prado\Models;

use kodeops\Prado\Models\Pin;
use kodeops\Prado\Models\Token;

class Artifact
{
    protected $pin;
    protected $artifact;
    protected $token;

    public function __construct(array $artifact)
    {
        $this->artifact = $artifact;
        $this->pin = isset($artifact['pin']) ? new Artifact($artifact['pin']) : null;
        $this->token = isset($artifact['token']) ? new Artifact($artifact['token']) : null;
    }

    /* Model Properties */

    public function alias()
    {
        return $this->artifact['alias'];
    }

    public function slug()
    {
        return $this->artifact['slug'];
    }

    public function url()
    {
        return $this->artifact['url'];
    }

    public function description()
    {
        return $this->artifact['description'];
    }

    public function metadata()
    {
        return $this->artifact['metadata'];
    }

    public function tags()
    {
        return $this->artifact['metadata'];
    }

    public function createdAt()
    {
        return $this->artifact['created_at'];
    }

    public function updatedAt()
    {
        return $this->artifact['updated_at'];
    }

    /* Model Relationships */

    public function pin()
    {
        return $this->pin;
    }

    public function token()
    {
        return $this->token;
    }
}