<?php
namespace kodeops\Prado\Pin;

use kodeops\Prado\Models\CachedPin;
use Illuminate\Support\Facades\Cache;
use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\PradoRequest;
use kodeops\Prado\Prado;

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
        $this->method = 'token_metadata';
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

    public function data(string $key = null)
    {
        $params = [
            'blockchain' => $this->blockchain,
            'contract' => $this->contract,
            'token_id' => $this->token_id,
            'method' => $this->method,
        ];

        $request = $this->pradoRequest
            ->timeout($this->timeout)
            ->get('api/1/token/preview', $params);
        if ($request->isError()) {
            if ($this->failsafe) {
                return Prado::PLACEHOLDER;
            }

            throw new PradoException("Error {$request->response('code')} processing request for token {$this->token_id} in contract {$this->contract} at {$this->blockchain} blockchain. Response: {$request->response('message')}");
        }

        $preview = $request->response('data')['preview'];

        if ($key) {
            if (! isset($preview[$key])) {
                throw new PradoException("Key {$key} not found}");
            }

            return $preview[$key];
        }

        return $preview;
    }
}