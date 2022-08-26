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
}
