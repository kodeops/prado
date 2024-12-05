<?php
namespace kodeops\Prado\Models;

class Token
{
    protected $token;

    public function __construct(array $token)
    {
        $this->token = $token;
    }

    /* Model Properties */

    public function tokenId()
    {
        return $this->token['token_id'];
    }

    public function blockchain()
    {
        return $this->token['blockchain'];
    }

    public function contract()
    {
        return $this->token['contract'];
    }

    public function name()
    {
        return $this->token['name'];
    }

    public function description()
    {
        return $this->token['description'];
    }

    public function mime()
    {
        return $this->token['mime'];
    }

    public function creators() : array
    {
        return $this->token['creators'];
    }

    public function creatorsNames() : array
    {
        return $this->token['creators_names'];
    }

    public function creatorsAttr() : array
    {
        return $this->token['creators_attr'];
    }

    public function creatorsUrls() : array
    {
        return $this->token['creators_urls'];
    }

    public function gatewayUrl()
    {
        return $this->token['gateway_url'];
    }

    public function marketplaceUrl()
    {
        return $this->token['marketplace']['token_url'] ?? null;
    }

    public function marketplaceName()
    {
        return $this->token['marketplace']['name'] ?? null;
    }

    public function marketplaceTwitter()
    {
        return $this->token['marketplace']['twitter'] ?? null;
    }

    public function toArray()
    {
        return [
            'token_id' => $this->tokenId(),
            'blockchain' => $this->blockchain(),
            'contract' => $this->contract(),
            'name' => $this->name(),
            'description' => $this->description(),
            'mime' => $this->mime(),
            'creators' => $this->creators(),
            'creatorsNames' => $this->creatorsNames(),
            'creatorsAttr' => $this->creatorsAttr(),
            'creatorsUrls' => $this->creatorsUrls(),
            'gatewayUrl' => $this->gatewayUrl(),
            'marketplaceUrl' => $this->marketplaceUrl(),
            'marketplaceName' => $this->marketplaceName(),
            'marketplaceTwitter' => $this->marketplaceTwitter(),
        ];
    }
}