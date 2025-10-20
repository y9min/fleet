<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Base model for all models that use UUID primary keys
 * 
 * This model provides centralized UUID configuration for all models
 * that have UUID primary keys in the database. All models should
 * extend this instead of Model directly to ensure proper UUID handling.
 */
class BaseUuidModel extends Model
{
    use HasUuids;
    
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
