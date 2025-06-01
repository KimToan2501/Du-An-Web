<?php

namespace App\Models;

use App\Core\Model;

/**
 * Class ServiceType - Model for managing users
 * 
 * @property int $service_type_id
 * @property string $name
 * @property string $description
 * @property string $created_at Thời gian tạo
 * @property string $updated_at Thời gian cập nhật
 */
class ServiceType extends Model
{
  // Define the table for this model
  protected static $table = 'service_types';

  protected static $primaryKey = 'service_type_id';

  public $service_type_id;
  public $name;
  public $description;
  public $created_at;
  public $updated_at;

  public static function findByName(string $name)
  {
    return self::findOneBy('name', $name);
  }
}
