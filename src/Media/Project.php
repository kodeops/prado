<?php
namespace kodeops\Prado\Media;

use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\Media;
use kodeops\Prado\PradoRequest;

class Project
{
    protected $project_alias;

    public function __construct(string $project_alias)
    {
        $this->pradoRequest = new PradoRequest();
        $this->project_alias = $project_alias;
    }

    public function artifact(string $artifact_alias)
    {
        return new Media\Artifact($this->project_alias, $artifact_alias);
    } 
}