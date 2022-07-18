<?php
namespace kodeops\Prado\Media;

use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\PradoRequest;

class Collectible
{
    protected $collection_alias;
    protected $collectible_alias;

    public function __construct(string $collection_alias, string $collectible_alias)
    {
        $this->pradoRequest = new PradoRequest();
        $this->collection_alias = $collection_alias;
        $this->collectible_alias = $collectible_alias;
    }

    public function get(array $params)
    {
        return $this->pradoRequest->post("api/1/collection/{$this->collection_alias}/collectibles", $params);
    }

    public function tags(array $tags)
    {
        $params = ['tags' => implode(',', $tags)];
        return $this->pradoRequest->post("api/1/collection/{$this->collection_alias}/collectible/{$this->collectible_alias}/tags", $params);
    }

    public function metadata(array $metadata)
    {
        $params = ['metadata' => json_encode($metadata)];
        return $this->pradoRequest->post("api/1/collection/{$this->collection_alias}/collectible/{$this->collectible_alias}/metadata", $params);
    }
}