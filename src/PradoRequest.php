<?php
namespace kodeops\Prado;

use Illuminate\Support\Facades\Http;
use kodeops\Prado\Exceptions\PradoException;
use kodeops\Prado\Exceptions\PradoRequestException;

class PradoRequest
{
    protected $api_token;
    protected $base_url;
    protected $timeout;

    public function __construct()
    {
        if (is_null(env('PRADO_API_TOKEN'))) {
            throw new PradoException("Missing PRADO_API_TOKEN");
        }
        $this->api_token = env('PRADO_API_TOKEN');

        if (is_null(env('PRADO_ENDPOINT'))) {
            throw new PradoException("Missing PRADO_ENDPOINT");
        }
        $this->base_url = env('PRADO_ENDPOINT');
    }

    public function timeout(int $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function get(string $endpoint, array $params)
    {
        $url = "{$this->base_url}/{$endpoint}";
        if ($params) {
            $url .= "?" . http_build_query($params);
        }
        return $this->processResponse(Http::timeout($this->timeout ?? 30)->withToken($this->api_token)->get($url));
    }

    public function post(string $endpoint, array $params)
    {
        $url = "{$this->base_url}/{$endpoint}";
        return $this->processResponse(Http::timeout($this->timeout ?? 30)->withToken($this->api_token)->post($url, $params));
    }

    private function processResponse($response)
    {
        $rro = rro($response->json());
        if (! $rro) {
            throw new PradoRequestException("Invalid response from prado.");
        }

        return $rro;
    }
}