<?php

namespace App\Models;

use App\Core\Model;

class Pet extends Model
{
  protected static $table = 'pets';
  protected static $primaryKey = 'pet_id';

  public $pet_id;
  public $user_id;
  public $name;
  public $type;
  public $breed;
  public $age;
  public $age_unit;
  public $size;
  public $weight;
  public $color;
  public $gender;
  public $avatar_url;
  public $medical_notes;
  public $behavioral_notes;
  public $created_at;
  public $updated_at;

  // Relationships
  public $owner;

  public static function getMyPets($userId)
  {
    return self::findBy('user_id', $userId);
  }

  public function getTypeName($type)
  {
    $typeNames = [
      'dog' => 'Chó',
      'cat' => 'Mèo',
      'bird' => 'Chim',
      'rabbit' => 'Thỏ',
      'hamster' => 'Chuột hamster'
    ];

    return $typeNames[$type] ?? 'Khác';
  }

  public function getAgeUnitName($unit)
  {
    $unitNames = [
      'years' => 'Năm',
      'months' => 'Tháng',
      'weeks' => 'Tuần',
      'days' => 'Ngày'
    ];

    return $unitNames[$unit] ?? 'Khác';
  }

  public function getSizeName($size)
  {
    $sizeNames = [
      'small' => 'Nhỏ',
      'medium' => 'Trung bình',
      'large' => 'Lớn',
      'extra_large' => 'Rất lớn'
    ];
    return $sizeNames[$size] ?? 'Khác';
  }

  public function getGenderName($gender)
  {
    $genderNames = [
      'male' => 'Đực',
      'female' => 'Cái',
      'unknown' => 'Không xác định',
    ];

    return $genderNames[$gender] ?? 'Khác';
  }
}
