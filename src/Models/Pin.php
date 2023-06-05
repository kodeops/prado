<?php
namespace kodeops\Prado\Models;

use kodeops\Prado\Models\Artifact;
use kodeops\Prado\Models\Token;

class Pin
{
    protected $pin;
    protected $artifact;
    protected $token;

    public function __construct(array $pin)
    {
        $this->pin = $pin;
        $this->artifact = isset($pin['artifact']) ? new Artifact($pin['artifact']) : null;
        $this->token = isset($pin['token']) ? new Token($pin['token']) : null;
    }

    /* Model Properties */

    public function pin()
    {
        return $this;
    }

    public function alias()
    {
        return $this->pin['alias'];
    }

    public function url()
    {
        return $this->pin['url'];
    }

    public function profile()
    {
        return $this->pin['profile'];
    }

    public function cdn()
    {
        return $this->pin['cdn'];
    }

    public function thumbnails()
    {
        return $this->pin['thumbnails'];
    }

    public function thumbnail(string $size)
    {
        return $this->thumbnails()[$size] ?? null;
    }

    public function width()
    {
        return $this->pin['width'];
    }

    public function height()
    {
        return $this->pin['height'];
    }

    public function size()
    {
        return $this->pin['size'];
    }

    public function mode()
    {
        return $this->pin['mode'];
    }

    public function source()
    {
        return $this->pin['source'];
    }

    public function createdAt()
    {
        return $this->pin['created_at'];
    }

    /* Model Relationships */

    public function token()
    {
        return $this->token;
    }

    public function artifact()
    {
        return $this->artifact;
    }
}