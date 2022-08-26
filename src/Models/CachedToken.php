<?php
namespace kodeops\Prado\Models;

use Illuminate\Database\Eloquent\Model;

class CachedPin extends Model
{
    protected $table = 'prado_pins';
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