<?php

namespace App\Models;

use App\Core\Model;

class BookingDetail extends Model
{
  protected static $table = 'booking_details';
  protected static $primaryKey = 'id';
  
  public $id;
  public $booking_id;
  public $service_id;
  public $quantity;
  public $unit_price;
  public $discount_percent;
  public $total_price;
  public $duration;
  public $notes;
  public $created_at;
  public $updated_at;
}
