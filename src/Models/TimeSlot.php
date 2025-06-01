<?php

namespace App\Models;

use App\Core\Model;

class TimeSlot extends Model
{
  protected static $table = 'time_slots';
  protected static $primaryKey = 'time_slot_id';

  public $time_slot_id;
  public $start_time;
  public $end_time;
  public $created_at;
  public $updated_at;
}
