<?php

namespace App\Models;

use App\Core\Model;

class ServiceImage extends Model
{
  // Define the table for this model
  protected static $table = 'service_images';

  protected static $primaryKey = 'image_id';

  public $image_id;
  public $service_id;
  public $image_url;
  public $created_at;
  public $updated_at;

  /**
   * Find images by service ID
   */
  public static function findByServiceId($serviceId)
  {
    return static::findBy('service_id', $serviceId);
  }

  /**
   * Delete all images for a service
   */
  public static function deleteByServiceId($serviceId)
  {
    $images = static::findByServiceId($serviceId);

    foreach ($images as $image) {
      // Delete physical file
      if (file_exists($image->image_url)) {
        unlink($image->image_url);
      }
      // Delete record
      static::delete($image->image_id);
    }

    return true;
  }

  /**
   * Get image URL with base URL
   */
  public function getFullImageUrl()
  {
    return base_url($this->image_url);
  }

  /**
   * Get image filename only
   */
  public function getImageFilename()
  {
    return basename($this->image_url);
  }
}
