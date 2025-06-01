<?php

namespace App\Models;

use App\Core\Model;

class BookingStaffSchedule extends Model
{
  protected static $table = 'booking_staff_schedule';
  protected static $primaryKey = 'id';

  public $id;
  public $booking_id;
  public $staff_schedule_id;
  public $created_at;
  public $updated_at;

  public function getStaffSchedule($id)
  {
    $result = StaffSchedule::find($id);
    $timeSlot = $result->get_time_slot($result->time_slot_id);
    $result->staff = StaffSchedule::getStaff($result->account_id);
    $result->time_slot = $timeSlot;
    return $result;
  }
}
