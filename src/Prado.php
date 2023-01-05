<?php
namespace kodeops\Prado;

use kodeops\Prado\Media;
use kodeops\Prado\Pin;
use kodeops\Prado\Exceptions\PradoException;

class Prado
{
    const PLACEHOLDER = "https://pradocdn.s3-eu-central-1.amazonaws.com/placeholder.jpg";
    
    public static function collection(string $collection_alias)
    {
        return new Media\OrganisationalUnit($collection_alias);
    }    

    public static function project(string $project_alias)
    {
        return new Media\OrganisationalUnit($project_alias);
    }

    public static function organisationalUnit(string $organisational_unit_alias)
    {
        return new Media\OrganisationalUnit($organisational_unit_alias);
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

    public static function preview()
    {
        return new Pin\Preview;
    }

    public static function thumbnail()
    {
        return new Pin\Thumbnail;
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
