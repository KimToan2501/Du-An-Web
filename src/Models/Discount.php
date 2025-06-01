<?php
namespace App\Models;

use App\Core\Model;

class Discount extends Model
{
    protected static $table = 'discounts';
    protected static $primaryKey = 'discount_id';

    public $discount_id;
    public $name;
    public $code;
    public $start_date;
    public $end_date;
    public $percent;
    public $created_at;
    public $updated_at;

    public static function findByCode(string $code)
    {
        return self::findOneBy('code', $code);
    }
}