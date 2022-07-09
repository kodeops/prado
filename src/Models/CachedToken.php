<?php
namespace kodeops\Prado\Models;

use Illuminate\Database\Eloquent\Model;

class CachedToken extends Model
{
    protected $table = 'prado_tokens';
    protected $fillable = [
        'pin',
        'hash',
        'token_id',
        'blockchain',
        'contract',
        'metadata',
    ];
    protected $casts = [
        'metadata' => 'array',
    ];
}