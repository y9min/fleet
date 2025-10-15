<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

namespace App;

use App\Model\User;
use App\Model\VehicleModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Fine extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fines';
    protected $dates = ['deleted_at', 'date_logged', 'date_issued', 'due_date', 'escalation_date'];
    
    protected $fillable = [
        'fine_type',
        'price',
        'admin_fee',
        'total_amount',
        'discount_window_days',
        'discount_amount',
        'escalation_days',
        'escalation_multiplier',
        'vehicle_reg',
        'vehicle_id',
        'driver_id',
        'status',
        'date_logged',
        'date_issued',
        'due_date',
        'escalation_date',
        'evidence_file',
        'notes',
        'contravention_code',
        'reference_number'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'escalation_multiplier' => 'decimal:2',
        'date_logged' => 'datetime',
        'date_issued' => 'datetime',
        'due_date' => 'datetime',
        'escalation_date' => 'datetime',
    ];

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Accessors & Mutators
    public function getCurrentAmountAttribute()
    {
        // Check if fine has escalated
        if ($this->escalation_date && Carbon::now()->gte($this->escalation_date) && $this->status !== 'paid') {
            return $this->total_amount * $this->escalation_multiplier;
        }
        
        // Check if within discount window
        if ($this->discount_window_days && $this->discount_amount && 
            Carbon::now()->lte($this->date_logged->addDays($this->discount_window_days))) {
            return $this->discount_amount;
        }
        
        return $this->total_amount;
    }

    public function getIsEscalatedAttribute()
    {
        return $this->escalation_date && Carbon::now()->gte($this->escalation_date) && $this->status !== 'paid';
    }

    public function getIsInDiscountWindowAttribute()
    {
        return $this->discount_window_days && $this->discount_amount && 
               Carbon::now()->lte($this->date_logged->addDays($this->discount_window_days));
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'badge-warning',
            'notified' => 'badge-info',
            'paid' => 'badge-success',
            'disputed' => 'badge-danger',
            'escalated' => 'badge-dark'
        ];
        
        return $badges[$this->status] ?? 'badge-secondary';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now())
                    ->whereNotIn('status', ['paid', 'disputed']);
    }

    public function scopeEscalated($query)
    {
        return $query->where('escalation_date', '<=', Carbon::now())
                    ->where('status', '!=', 'paid');
    }

    // Get descriptive title for fine type
    public function getFineTypeTitleAttribute()
    {
        $fine_types = self::getFineTypes();
        
        foreach ($fine_types as $category => $types) {
            if (isset($types[$this->fine_type])) {
                $title = $types[$this->fine_type];
                // Shorten long titles for better table display
                if (strlen($title) > 50) {
                    return substr($title, 0, 47) . '...';
                }
                return $title;
            }
        }
        
        // Fallback to the code if not found
        return $this->fine_type;
    }

    // Static methods for fine types
    public static function getFineTypes()
    {
        return [
            'London PCN' => [
                '01' => 'Parked in a restricted street during prescribed hours',
                '02' => 'Parked or loading/unloading in a restricted street where waiting and loading/unloading restrictions are in force',
                '03' => 'Parked after the expiry of paid for time',
                '04' => 'Parked without payment of the parking charge',
                '05' => 'Parked in a loading place during restricted hours without loading',
                '06' => 'Parked in a suspended bay/space or part of bay/space',
                '07' => 'Re-parked within one hour of leaving a parking place in the same street',
                '08' => 'Parked in a parking place not designated for that class of vehicle',
                '09' => 'Parked with engine running where prohibited',
                '10' => 'Parked in a residents\' or shared use parking place without clearly displaying a valid permit',
                '11' => 'Parked in a residents\' or shared use parking place displaying an invalid permit',
                '12' => 'Parked in a residents\' or shared use parking place without payment of the parking charge',
                '16' => 'Parked in a permit space without displaying a valid permit',
            ],
            'Motoring Offence' => [
                'SP10' => 'Exceeding speed limit on a motorway',
                'SP20' => 'Exceeding speed limit on a road other than a motorway',
                'SP30' => 'Exceeding statutory speed limit on a public road',
                'SP40' => 'Exceeding speed limit on a road other than a motorway',
                'SP50' => 'Exceeding speed limit on a motorway',
                'CU80' => 'Using a mobile phone while driving',
                'MS10' => 'Leaving a vehicle in a dangerous position',
                'MS20' => 'Unlawful pillion riding',
                'MS30' => 'Play street offences',
                'MS40' => 'Driving elsewhere than on a road',
                'MS50' => 'Motor racing on the highway',
                'MS60' => 'Offences not covered by other codes',
                'MS70' => 'Driving with uncorrected defective eyesight',
                'MS80' => 'Driving a vehicle with defective brakes',
                'MS90' => 'Failure to give information as to identity of driver etc.',
            ],
            'Other' => [
                'BUS_LANE' => 'Bus lane contravention',
                'RED_ROUTE' => 'Red route contravention',
                'YELLOW_BOX' => 'Yellow box junction contravention',
                'CYCLE_LANE' => 'Cycle lane contravention',
                'PEDESTRIAN' => 'Pedestrian crossing contravention',
                'NO_ENTRY' => 'No entry contravention',
                'ONE_WAY' => 'One way street contravention',
                'UTURN' => 'U-turn contravention',
                'NO_TURNING' => 'No turning contravention',
            ]
        ];
    }

    // Auto-calculate methods
    public function calculateTotalAmount()
    {
        $this->total_amount = $this->price + $this->admin_fee;
        return $this->total_amount;
    }

    public function calculateDueDate()
    {
        if ($this->escalation_days) {
            $this->due_date = $this->date_logged->copy()->addDays($this->escalation_days);
        }
        return $this->due_date;
    }

    public function calculateEscalationDate()
    {
        if ($this->escalation_days) {
            $this->escalation_date = $this->date_logged->copy()->addDays($this->escalation_days);
        }
        return $this->escalation_date;
    }

    public function calculateDiscountAmount($discountPercentage = 50)
    {
        if ($this->total_amount) {
            $this->discount_amount = $this->total_amount * ($discountPercentage / 100);
        }
        return $this->discount_amount;
    }
}
