<?php
namespace kodeops\Prado;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\Models\CachedToken;

class Prado
{
    protected $api_token;
    protected $endpoint;

    protected $token_id;
    protected $blockchain;
    protected $contract;
    protected $width;
    protected $height;
    protected $mode;
    protected $author;
    protected $quality;

    protected $method;
    protected $failsafe;
    protected $timeout;
    
    protected $cache_driver;
    protected $cache_key;

    public function __construct($token_id, $failsafe = true)
    {
        if (is_null(env('PRADO_API_TOKEN'))) {
            throw new PradoException("Missing PRADO_API_TOKEN");
        }
        $this->api_token = env('PRADO_API_TOKEN');

        if (is_null(env('PRADO_ENDPOINT'))) {
            throw new PradoException("Missing PRADO_ENDPOINT");
        }
        $this->endpoint = env('PRADO_ENDPOINT');

        $this->setupCacheDriver();

        $this->token_id = $token_id;
        $this->failsafe = $failsafe;

        // Default settings
        $this->mode = 'maintain_aspect_ratio';
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

    public static function nft($token_id)
    {
        return new Prado($token_id);
    }

    public function blockchain($blockchain)
    {
        $this->method = 'nft';
        $this->blockchain = $blockchain;
        return $this;
    }

    public function failsafe(bool $failsafe)
    {
        $this->failsafe = $failsafe;
        return $this;
    }

    public function timeout(int $timeout)
    {
        $this->timeout = $timeout;
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

    public function token()
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

        $url = $this->endpoint . '/api/1/pin/token?' . http_build_query($params);

        $response = Http::timeout($this->timeout ?? 30)->withToken($this->api_token)->get($url);

        if ($response->status() == 404) {
            throw new PradoException("Endpoint not found! Please check that PRADO_ENDPOINT is set in the environment file.");
        }

        if ($response->failed()) {
            if ($this->failsafe) {
                return 'https://pradocdn.s3-eu-central-1.amazonaws.com/placeholder.jpg';
            }

            throw new PradoException("Error {$response->status()} processing request for token {$this->token_id} in contract {$this->contract} at {$this->blockchain} blockchain. Response: " . $response->body());
        }

        $data = $response->json();

        if (is_rro($data)) {
            if (rro($data)->isError()) {
                throw new PradoException("Error Processing Request: Invalid response format.");
            }
        }

        $data = $data['response']['data'];

        $this->cacheToken($data);
        
        return $data;
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
                Cache::put("prado.{$hash}", $data);
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
}
