<?php
namespace kodeops\Prado\Media;

use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\PradoRequest;

class Artifact
{
    protected $organisation_unit_alias;
    protected $artifact_alias;

    public function __construct(string $organisation_unit_alias, string $artifact_alias)
    {
        $this->pradoRequest = new PradoRequest();
        $this->organisation_unit_alias = $organisation_unit_alias;
        $this->artifact_alias = $artifact_alias;
    }

    public function get(array $params)
    {
        $params['organisation_unit'] = $this->organisation_unit_alias;
        return $this->pradoRequest->post("api/1/artifacts", $params);
    }

    public function tags(array $tags)
    {
        $params = ['tags' => implode(',', $tags)];
        return $this->pradoRequest->post("api/1/artifact/{$this->artifact_alias}/tags", $params);
    }

    public function metadata(array $metadata)
    {
        $params = ['metadata' => json_encode($metadata)];
        return $this->pradoRequest->post("api/1/artifact/{$this->artifact_alias}/metadata", $params);
    }
}