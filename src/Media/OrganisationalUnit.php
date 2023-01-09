<?php
namespace kodeops\Prado\Media;

use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\Media;
use kodeops\Prado\PradoRequest;
use kodeops\Prado\Models\Artifact;

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
        $artifacts = $this->pradoRequest->get("api/1/orgunit/{$this->organisational_unit_alias}/artifacts", $params);

        if ($artifacts->isError()) {
            return $artifacts;
        }
        
        return collect($artifacts->response('data', 'artifacts'))->map(function($artifact){
            return new Artifact($artifact);
        })->toArray();
    } 
}