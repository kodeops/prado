<?php
namespace kodeops\Prado\Media;

use kodeops\Prado\Models\CachedToken;
use Illuminate\Support\Facades\Cache;
use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\PradoRequest;

class Token
{
    protected $token_id;
    protected $blockchain;
    protected $contract;
    protected $width;
    protected $height;
    protected $mode;
    protected $author;
    protected $quality;

    protected $cache_driver;
    protected $cache_key;

    protected $method;
    protected $failsafe;
    protected $timeout;
    protected $pradoRequest;

    public function __construct(string $token_id)
    {
        $this->pradoRequest = new PradoRequest();
        $this->token_id = $token_id;
        $this->timeout = 180;

        // Default settings
        $this->mode = 'maintain_aspect_ratio';
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

    /* Alias to support old calls */
    public function token()
    {
        return $this->metadata();
    }

    public function metadata()
    {
        return $this->resolveNft();
    }

    public function url()
    {
        switch ($this->method) {
            case 'nft':
                return $this->resolveNft()['url'];
            break;
        }
    }

    public function blockchain($blockchain)
    {
        $this->method = 'nft';
        $this->blockchain = $blockchain;
        return $this;
    }

    public function contract(string $contract)
    {
        $this->contract = $contract;
        return $this;
    }

    public function width(int $width)
    {
        $this->width = $width;
        return $this;
    }

    public function height(int $height)
    {
        $this->height = $height;
        return $this;
    }

    public function quality(int $quality)
    {
        $this->quality = $quality;
        return $this;
    }

    public function mode(string $mode)
    {
        $this->mode = $mode;
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
            'mode' => $this->mode,
        ];

        if ($this->author) {
            $params['author'] = $this->author;
        }

        if ($this->width) {
            $params['width'] = $this->width;
        }

        if ($this->height) {
            $params['height'] = $this->height;
        }

        if ($this->quality) {
            $params['quality'] = $this->quality;
        }

        $tokenIsCached = $this->checkIfTokenIsCached($params);
        if ($tokenIsCached) {
            return $tokenIsCached;
        }

        $request = $this->pradoRequest
            ->timeout($this->timeout)
            ->get('api/1/pin/token', $params);

        if ($request->isError()) {
            if ($this->failsafe) {
                return 'https://pradocdn.s3-eu-central-1.amazonaws.com/placeholder.jpg';
            }

            throw new PradoException("Error {$request->response('code')} processing request for token {$this->token_id} in contract {$this->contract} at {$this->blockchain} blockchain. Response: {$request->response('message')}");
        }

        $token = $request->response('data');

        $this->cacheToken($token);
        
        return $token;
    }

    private function cacheToken(array $data)
    {
        switch ($this->cache_driver) {
            case 'mysql':
                CachedToken::create([
                    'pin' => $data['alias'],
                    'hash' => $this->cache_key,
                    'blockchain' => $this->blockchain,
                    'contract' => $this->contract,
                    'token_id' => $this->token_id,
                    'metadata' => $data,
                ]);
            break;

            default:
                Cache::put("prado.{$this->cache_key}", $data);
            break;
        }
    }

    private function checkIfTokenIsCached(array $params)
    {
        $this->cache_key = sha1(json_encode($params));

        switch ($this->cache_driver) {
            case 'mysql':
                $cache_exists = CachedToken::where('hash', $this->cache_key)->first();
                if ($cache_exists) {
                    return $cache_exists->metadata;
                }
            break;

            default:
                return Cache::get("prado.{$this->cache_key}");
            break;
        }

        return false;
    }

    private function setupCacheDriver()
    {
        if (is_null(env('PRADO_CACHE_DRIVER'))) {
            $this->cache_driver = env('CACHE_DRIVER');
        } else {
            if (! in_array(env('PRADO_CACHE_DRIVER'), $this->supportedCacheDrivers())) {
                throw new PradoException("Invalid PRADO_CACHE_DRIVER");
            }

            $this->cache_driver = env('PRADO_CACHE_DRIVER');

            switch ($this->cache_driver) {
                case 'mysql':
                break;
            }
        }
        $this->endpoint = env('PRADO_ENDPOINT');
    }

    private function supportedCacheDrivers()
    {
        return ['mysql'];
    }
}