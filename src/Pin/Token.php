<?php
namespace kodeops\Prado\Pin;

use kodeops\Prado\Models\CachedPin;
use Illuminate\Support\Facades\Cache;
use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\PradoRequest;
use kodeops\Prado\Prado;
use kodeops\Prado\Models\Pin;

class Token
{
    protected $token_id;
    protected $blockchain;
    protected $contract;
    protected $width;
    protected $height;
    protected $mode;
    protected $author;
    protected $organisational_unit;
    protected $marketplace_url;
    protected $queue;

    protected $cache_driver;
    protected $cache_key;

    protected $method;
    protected $failsafe;
    protected $timeout;
    protected $pradoRequest;

    public function __construct(string $token_id = null)
    {
        $this->pradoRequest = new PradoRequest();
        $this->token_id = $token_id;
        $this->timeout = 180;

        // Default settings
        $this->queue = '1';
        $this->mode = 'maintain_aspect_ratio';
        $this->setupCacheDriver();
    }

    /* Public setters */

    public function tokenId(string $token_id)
    {
        $this->token_id = $token_id;
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

    public function marketplaceUrl(string $marketplace_url)
    {
        $this->marketplace_url = $marketplace_url;
        return $this;
    }

    public function queue(bool $queue)
    {
        $this->queue = $queue ? '1' : '0';
        return $this;
    }

    public function collection(string $collection)
    {
        return $this->orgunit($collection);
    }

    public function project(string $project)
    {
        return $this->orgunit($project);
    }

    public function process()
    {
        return new Pin($this->resolveNft());
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

    /* Private Methods */

    private function orgunit(string $organisational_unit)
    {
        $this->organisational_unit = $organisational_unit;
        return $this;
    }

    private function resolveNft()
    {
        $params = [
            'mode' => $this->mode,
            'queue' => $this->queue,
        ];

        if (! is_null($this->token_id)) {
            $api_endpoint = 'api/1/pin/token';
            $params['token_id'] = $this->token_id;
            $params['blockchain'] = $this->blockchain;
            $params['contract'] = $this->contract;
        } else if ($this->marketplace_url) {
            $api_endpoint = 'api/1/pin/marketplace';
            $params['marketplace_url'] = $this->marketplace_url;
        }

        if (! isset($api_endpoint)) {
            throw new PradoException("Invalid pinning request params");
        }

        if (! $this->author) {
            $this->author = env('APP_NAME');   
        }

        if ($this->author) {
            $params['author'] = $this->author;
        }

        if ($this->width) {
            $params['width'] = $this->width;
        }

        if ($this->height) {
            $params['height'] = $this->height;
        }

        if ($this->organisational_unit) {
            $params['organisational_unit'] = $this->organisational_unit;
        }

        $this->cache_key = sha1(json_encode($params));

        $pinIsCached = $this->checkIfPinIsCached($params);
        if ($pinIsCached) {
            return $pinIsCached;
        }

        $request = $this->pradoRequest
            ->timeout($this->timeout)
            ->get($api_endpoint, $params);
        if ($request->isError()) {
            if ($this->failsafe) {
                return Prado::PLACEHOLDER;
            }

            throw new PradoException("Error {$request->response('code')} processing request for token {$this->token_id} in contract {$this->contract} at {$this->blockchain} blockchain. Response: {$request->response('message')}");
        }

        $token = $request->response('data');

        $this->cachePin($token);

        return $token;
    }

    private function cachePin(array $pin)
    {
        switch ($this->cache_driver) {
            case 'mysql':
                $pin_params = [
                    'hash' => $this->cache_key,
                    'blockchain' => $this->blockchain,
                    'contract' => $this->contract,
                    'token_id' => $this->token_id,
                    'metadata' => $pin,
                ];

                $cachedPin = CachedPin::where('pin', $pin['alias'])->first();
                if ($cachedPin) {
                    // Update pin
                    $cachedPin->update($pin_params);
                } else {
                    $pin_params['pin'] = $pin['alias'];
                    CachedPin::create($pin_params);
                }
            break;

            default:
                Cache::put("prado.{$this->cache_key}", $pin);
            break;
        }
    }

    private function checkIfPinIsCached(array $params)
    {
        switch ($this->cache_driver) {
            case 'mysql':
                $cache_exists = CachedPin::where('hash', $this->cache_key)
                    ->where('metadata->thumbnails->large', '!=', Prado::PLACEHOLDER)
                    ->where('metadata->thumbnails->small', '!=', Prado::PLACEHOLDER)
                    ->first();
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
        }
    }

    private function supportedCacheDrivers()
    {
        return ['mysql'];
    }
}
