<?php
namespace kodeops\Prado\Media;

use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\Media;
use kodeops\Prado\PradoRequest;

class OrganisationalUnit
{
    protected $organisational_unit_alias;

    public function __construct(string $organisational_unit_alias)
    {
        $this->pradoRequest = new PradoRequest();
        $this->organisational_unit_alias = $organisational_unit_alias;
    }

    public function artifacts(array $params = [])
    {
        return (new Media\Artifact)->get($this->organisational_unit_alias, $params);
    } 
}