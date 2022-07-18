<?php
namespace kodeops\Prado;

use kodeops\Prado\Media;

class Prado
{
    /* Alias to support old calls */
    public static function nft(string $token_id)
    {
        return self::token($token_id);
    }

    public static function token(string $token_id)
    {
        return new Media\Token($token_id);
    }

    public static function collection(string $collection_alias)
    {
        return new Media\Collection($collection_alias);
    }    
}
