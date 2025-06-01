<?php

namespace App\Models;

use App\Core\Model;

class BookingPet extends Model
{
  protected static $table = 'booking_pets';
  protected static $primaryKey = 'id';

  public $id;
  public $booking_id;
  public $pet_id;
  public $special_notes;
  public $created_at;
}
