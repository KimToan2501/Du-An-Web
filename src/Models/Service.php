<?php

namespace App\Models;

use App\Core\Model;

class Service extends Model
{
  protected static $table = 'services';
  protected static $primaryKey = 'service_id';

  public $service_id;
  public $name;
  public $description;
  public $price;
  public $discount_percent;
  public $duration;
  public $service_type_id;
  public $created_at;
  public $updated_at;

  public static function findByName(string $name)
  {
    return self::findOneBy('name', $name);
  }

  public function price_new()
  {
    $amount = $this->price * $this->discount_percent / 100;
    return $this->price - $amount;
  }

  public function service_type($id)
  {
    return ServiceType::findOneBy('service_type_id', $id);
  }

  public function service_images($id)
  {
    return ServiceImage::findBy('service_id', $id);
  }

  public function get_first_image($id)
  {
    $images = ServiceImage::findBy('service_id', $id);

    if (empty($images)) {
      return null;
    }

    return $images[0]->image_url;
  }
}
