<?php
namespace kodeops\Prado\Media;

use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\Media;
use kodeops\Prado\PradoRequest;

class Collection
{
    protected $collection_alias;

    public function __construct(string $collection_alias)
    {
        $this->pradoRequest = new PradoRequest();
        $this->collection_alias = $collection_alias;
    }

    public function artifact(string $artifact_alias)
    {
        return new Media\Artifact($this->collection_alias, $artifact_alias);
    } 
    public function artifacts(array $params = [])
    {
        $params['organisational_unit'] = $this->collection_alias;
        return (new Media\Artifact($this->collection_alias))->get($params);
    } 
}