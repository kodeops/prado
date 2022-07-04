<?php
namespace kodeops\Prado;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use kodeops\Prado\Exceptions\PradoException;

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

        $this->token_id = $token_id;
        $this->failsafe = $failsafe;

        // Default settings
        $this->mode = 'maintain_aspect_ratio';
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

    public function failsafe($failsafe)
    {
        $this->failsafe = $failsafe;
        return $this;
    }

    public function contract($contract)
    {
        $this->contract = $contract;
        return $this;
    }

    public function width($width)
    {
        $this->width = $width;
        return $this;
    }

    public function height($height)
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

    public function url()
    {
        switch ($this->method) {
            case 'nft':
                return $this->resolveNft();
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

        // Check cache existance
        $cache_key = 'prado.' . sha1(json_encode($params));
        $cache_exists = Cache::get($cache_key);
        if ($cache_exists) {
            return $cache_exists;
        }

        url = $this->endpoint . '/api/1/pin/token?' . http_build_query($params);

        $response = Http::withToken($this->api_token)->get($url);

        if ($response->status() == 404) {
            throw new PradoException("Endpoint not found! Please check that PRADO_ENDPOINT is set in the environment file.");
        }

        if ($response->failed()) {
            if ($this->failsafe) {
                return 'https://pradocdn.s3-eu-central-1.amazonaws.com/placeholder.jpg';
            }

            throw new PradoException("Error Processing Request");
        }

        $url = rro($response->json())->getData('url');

        Cache::put($cache_key, $url);

        return $url;
    }
}
