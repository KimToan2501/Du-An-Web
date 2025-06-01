<?php

namespace App\Core;

interface ModelInterface
{
    /**
     * Lấy tên bảng của model
     * 
     * @return string
     */
    public static function getTable();
    
    /**
     * Lấy tên khóa chính của model
     * 
     * @return string|array
     */
    public static function getPrimaryKey();
    
    /**
     * Tìm bản ghi theo một trường cụ thể
     * 
     * @param string $field Tên trường
     * @param mixed $value Giá trị
     * @return array
     */
    public static function findBy($field, $value);
    
    /**
     * Tìm một bản ghi theo một trường cụ thể
     * 
     * @param string $field Tên trường
     * @param mixed $value Giá trị
     * @return static|null
     */
    public static function findOneBy($field, $value);
}