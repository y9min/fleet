<?php

namespace App\Model;

use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleBreakdown extends BaseUuidModel 
{
  
    use SoftDeletes;
    protected $table = 'vehicle_breakdown';
    protected $fillable = ['name'];
}
