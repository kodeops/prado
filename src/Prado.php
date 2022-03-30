<?php
namespace kodeops\Prado;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use kodeops\Prado\Exceptions\PradoException;

class Prado
{
    protected $token_id;
    protected $blockchain;
    protected $contract;
    protected $width;
    protected $height;
    protected $mantain_aspect_ratio;

    protected $method;
    protected $failsafe;

    public function __construct($token_id, $failsafe = true)
    {
        $this->token_id = $token_id;
        $this->failsafe = $failsafe;

        // Default settings
        $this->mantain_aspect_ratio = true;
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
            'width' => $this->width,
            'height' => $this->height,
            'token_id' => $this->token_id,
            'mantain_aspect_ratio' => $this->mantain_aspect_ratio,
        ];

        // Check cache existance
        $cache_key = 'prado.' . sha1(json_encode($params));
        $cache_exists = Cache::get($cache_key);
        if ($cache_exists) {
            return $cache_exists;
        }

        $response = Http::get($this->endpoint() . '/api/1/token?' . http_build_query($params));

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

    private function endpoint()
    {
        if (is_null(env('PRADO_ENDPOINT'))) {
            throw new PradoException('PRADO_ENDPOINT not defined');
        }

        return env('PRADO_ENDPOINT');
    }
}
