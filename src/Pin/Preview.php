<?php
namespace kodeops\Prado\Pin;

use kodeops\Prado\Models\CachedPin;
use Illuminate\Support\Facades\Cache;
use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\PradoRequest;

class Preview
{
    /* Available properties */
    protected $token_id;
    protected $blockchain;
    protected $contract;
    protected $author;

    protected $failsafe;
    protected $timeout;
    protected $pradoRequest;

    public function __construct()
    {
        $this->pradoRequest = new PradoRequest();
        $this->timeout = 180;
    }

    public function tokenId(string $token_id)
    {
        $this->token_id = $token_id;
        return $this;
    }

    public function blockchain($blockchain)
    {
        $this->blockchain = $blockchain;
        return $this;
    }

    public function contract(string $contract)
    {
        $this->contract = $contract;
        return $this;
    }

    public function timeout(int $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function failsafe(bool $failsafe)
    {
        $this->failsafe = $failsafe;
        return $this;
    }

    public function author(string $author)
    {
        $this->author = $author;
        return $this;
    }

    private function resolveNft()
    {
        $params = [
            'blockchain' => $this->blockchain,
            'contract' => $this->contract,
            'token_id' => $this->token_id,
        ];

        $request = $this->pradoRequest
            ->timeout($this->timeout)
            ->get('api/1/token/preview', $params);
        if ($request->isError()) {
            if ($this->failsafe) {
                return 'https://pradocdn.s3-eu-central-1.amazonaws.com/placeholder.jpg';
            }

            throw new PradoException("Error {$request->response('code')} processing request for token {$this->token_id} in contract {$this->contract} at {$this->blockchain} blockchain. Response: {$request->response('message')}");
        }

        $token = $request->response('data');

        return $token['preview'];
    }
}