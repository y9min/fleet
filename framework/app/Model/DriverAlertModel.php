<?php

namespace App\Model;

use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverAlertModel extends BaseUuidModel 
{
  
    use SoftDeletes;
    protected $table = 'driver_alert';
    protected $fillable = ['name'];
}
