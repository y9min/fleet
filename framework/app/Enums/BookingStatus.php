<?php

namespace App\Enums;

/**
 * Mirrors Postgres booking_status_enum
 */
enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
}


