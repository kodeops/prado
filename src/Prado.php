<?php
namespace kodeops\Prado;

use kodeops\Prado\Media;
use kodeops\Prado\Pin;
use kodeops\Prado\Exceptions\PradoException;

class Prado
{
    public static function collection(string $collection_alias)
    {
        return new Media\OrganisationalUnit($collection_alias);
    }    

    public static function project(string $project_alias)
    {
        return new Media\OrganisationalUnit($project_alias);
    }    

    public static function artifact(string $artifact_alias)
    {
        return new Media\Artifact($artifact_alias);
    }

    public static function pin(string $origin)
    {
        switch ($origin)
        {
            case 'token':
                return new Pin\Token();
            break;

            case 'marketplace_url':
            break;

            case 'url':
            break;

            case 'ipfs':
            break;

            default:
                throw new PradoException("Invalid pin origin: {$origin}");
            break;
        }
    }

    /* Alias to support old calls */
    public static function nft(string $token_id)
    {
        return self::pin('token')->tokenId($token_id);
    }

    /* Alias to support old calls */
    public static function token(string $token_id)
    {
        return self::pin('token')->tokenId($token_id);
    }
}
