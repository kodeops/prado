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

    public function collectible(string $collectible_alias)
    {
        return new Media\Collectible($this->collection_alias, $collectible_alias);
    } 
}