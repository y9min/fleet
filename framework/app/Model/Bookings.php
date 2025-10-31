<?php
/*
@copyright
Fleet Manager v7.1.2
Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>
 */
namespace App\Model;
use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model\BaseUuidModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kodeine\Metable\Metable;

class Bookings extends BaseUuidModel {
	use HasFactory;
	use Metable;
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = "bookings";
	protected $metaTable = 'bookings_meta';
	protected $fillable = [
		'customer_id', 'vehicle_id', 'user_id', 'pickup', 'dropoff', 'pickup_addr', 'dest_addr', 'travellers', 'status', 'comment', 'dropoff_time', 'driver_id', 'note', 'cancellation', 'completed_at',
	];

	protected $casts = [
		'status' => BookingStatus::class,
		'pickup' => 'datetime',
		'dropoff' => 'datetime',
	];

	protected function getMetaKeyName() {
		return 'booking_id'; // The parent foreign key
	}

	public function vehicle() {
		return $this->hasOne("App\Model\VehicleModel", "id", "vehicle_id")->withTrashed();
	}

	public function customer() {
		return $this->hasOne("App\Model\User", "id", "customer_id")->withTrashed();
	}

	public function driver() {
		return $this->hasOne("App\Model\User", "id", "driver_id")->withTrashed();
	}
	
	public function user() {
		return $this->hasOne("App\Model\User", "id", "user_id")->withTrashed();
	}

	public function vehicletype() {
		return $this->hasOne("App\Model\VehicleTypeModel", "id", "type_id")->withTrashed();
	}

	/**
	 * Scope: overlapping active bookings for a vehicle within [start, end]
	 */
	public function scopeOverlappingActive($query, string $vehicleId, $start, $end, array $activeStatuses = []) {
		$statuses = !empty($activeStatuses)
			? $activeStatuses
			: [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::InProgress];
		return $query
			->whereIn('status', array_map(fn(BookingStatus $s) => $s->value, $statuses))
			->where('vehicle_id', $vehicleId)
			->whereNull('deleted_at')
			->where(function ($q) use ($start, $end) {
				$q->whereBetween('pickup', [$start, $end])
					->orWhereBetween('dropoff', [$start, $end])
					->orWhere(function ($qq) use ($start, $end) {
						$qq->where('pickup', '<', $start)
							->where('dropoff', '>', $end);
					});
			});
	}

	// multivehicle test
	// function test1() {
	//     return $this->hasMany("App\Model\VehicleModel", "id", "v_id")->withTrashed();
	// }
	// function test() {
	//     return $this->belongsTo("App\Model\VehicleModel", "v_id", "id")->withTrashed();
	// }
}
