<?php

namespace App\Core;

use PDO;

/**
 * Trait Relationship
 * 
 * Cung cấp các phương thức để xử lý quan hệ giữa các model
 */
trait Relationship
{
  /**
   * Lấy tên khóa chính của model
   * 
   * @return string|array
   */
  public static function getPrimaryKey()
  {
    return static::$primaryKey ?? 'id';
  }

  /**
   * Lấy tên bảng của model
   * 
   * @return string
   */
  public static function getTable()
  {
    return static::$table ?? '';
  }

  /**
   * Định nghĩa quan hệ một-một
   * 
   * @param class-string<ModelInterface> $relatedModel Tên lớp model liên quan
   * @param string|null $foreignKey Khóa ngoại trong bảng liên quan (mặc định là {table}_id)
   * @param string|null $localKey Khóa chính của bảng hiện tại (mặc định là id)
   * @return object|null Đối tượng model liên quan hoặc null nếu không tìm thấy
   */
  public function hasOne(string $relatedModel, ?string $foreignKey = null, ?string $localKey = null)
  {
    // Xác định khóa chính của model hiện tại
    $localKey = $localKey ?? static::getPrimaryKey();

    // Lấy giá trị khóa chính của đối tượng hiện tại
    $localKeyValue = $this->$localKey;

    // Nếu không có giá trị khóa chính, trả về null
    if (!$localKeyValue) {
      return null;
    }

    // Nếu không chỉ định khóa ngoại, tạo tên mặc định dựa trên tên bảng hiện tại
    if (!$foreignKey) {
      $tableName = static::getTable();
      // Loại bỏ tiền tố nếu có
      if (strpos($tableName, '_') !== false) {
        $parts = explode('_', $tableName);
        $tableName = end($parts);
      }
      // Chuyển thành số ít nếu kết thúc bằng 's'
      if (substr($tableName, -1) === 's') {
        $tableName = substr($tableName, 0, -1);
      }
      $foreignKey = $tableName . '_id';
    }

    // Tìm bản ghi liên quan
    return $relatedModel::findOneBy($foreignKey, $localKeyValue);
  }

  /**
   * Định nghĩa quan hệ một-một ngược (belongsTo)
   * 
   * @param class-string<ModelInterface> $relatedModel Tên lớp model liên quan
   * @param string|null $foreignKey Khóa ngoại trong bảng hiện tại (mặc định là {relatedTable}_id)
   * @param string|null $ownerKey Khóa chính của bảng liên quan (mặc định là id)
   * @return object|null Đối tượng model liên quan hoặc null nếu không tìm thấy
   */
  public function belongsTo(string $relatedModel, ?string $foreignKey = null, ?string $ownerKey = null)
  {
    // Xác định khóa chính của model liên quan
    $ownerKey = $ownerKey ?? 'id';

    // Nếu không chỉ định khóa ngoại, tạo tên mặc định dựa trên tên bảng liên quan
    if (!$foreignKey) {
      // Sử dụng phương thức để lấy tên bảng từ model liên quan
      $tableName = $relatedModel::getTable();

      // Loại bỏ tiền tố nếu có
      if (strpos($tableName, '_') !== false) {
        $parts = explode('_', $tableName);
        $tableName = end($parts);
      }
      // Chuyển thành số ít nếu kết thúc bằng 's'
      if (substr($tableName, -1) === 's') {
        $tableName = substr($tableName, 0, -1);
      }
      $foreignKey = $tableName . '_id';
    }

    // Lấy giá trị khóa ngoại của đối tượng hiện tại
    $foreignKeyValue = $this->$foreignKey;

    // Nếu không có giá trị khóa ngoại, trả về null
    if (!$foreignKeyValue) {
      return null;
    }

    // Tìm bản ghi liên quan
    return $relatedModel::findOneBy($ownerKey, $foreignKeyValue);
  }

  /**
   * Định nghĩa quan hệ một-nhiều
   * 
   * @param class-string<ModelInterface> $relatedModel Tên lớp model liên quan
   * @param string|null $foreignKey Khóa ngoại trong bảng liên quan (mặc định là {table}_id)
   * @param string|null $localKey Khóa chính của bảng hiện tại (mặc định là id)
   * @return array Mảng các đối tượng model liên quan
   */
  public function hasMany(string $relatedModel, ?string $foreignKey = null, ?string $localKey = null)
  {
    // Xác định khóa chính của model hiện tại
    $localKey = $localKey ?? static::getPrimaryKey();

    // Lấy giá trị khóa chính của đối tượng hiện tại
    $localKeyValue = $this->$localKey;

    // Nếu không có giá trị khóa chính, trả về mảng rỗng
    if (!$localKeyValue) {
      return [];
    }

    // Nếu không chỉ định khóa ngoại, tạo tên mặc định dựa trên tên bảng hiện tại
    if (!$foreignKey) {
      $tableName = static::getTable();
      // Loại bỏ tiền tố nếu có
      if (strpos($tableName, '_') !== false) {
        $parts = explode('_', $tableName);
        $tableName = end($parts);
      }
      // Chuyển thành số ít nếu kết thúc bằng 's'
      if (substr($tableName, -1) === 's') {
        $tableName = substr($tableName, 0, -1);
      }
      $foreignKey = $tableName . '_id';
    }

    // Tìm các bản ghi liên quan
    return $relatedModel::findBy($foreignKey, $localKeyValue);
  }

  /**
   * Định nghĩa quan hệ nhiều-nhiều
   * 
   * @param class-string<ModelInterface> $relatedModel Tên lớp model liên quan
   * @param string|null $pivotTable Tên bảng trung gian
   * @param string|null $foreignPivotKey Khóa ngoại trong bảng trung gian tham chiếu đến bảng hiện tại
   * @param string|null $relatedPivotKey Khóa ngoại trong bảng trung gian tham chiếu đến bảng liên quan
   * @param string|null $localKey Khóa chính của bảng hiện tại (mặc định là id)
   * @param string|null $relatedKey Khóa chính của bảng liên quan (mặc định là id)
   * @return array Mảng các đối tượng model liên quan
   */
  public function belongsToMany(string $relatedModel, ?string $pivotTable = null, ?string $foreignPivotKey = null, ?string $relatedPivotKey = null, ?string $localKey = null, ?string $relatedKey = null)
  {
    // Xác định khóa chính của model hiện tại và model liên quan
    $localKey = $localKey ?? static::getPrimaryKey();
    $relatedKey = $relatedKey ?? 'id';

    // Lấy giá trị khóa chính của đối tượng hiện tại
    $localKeyValue = $this->$localKey;

    // Nếu không có giá trị khóa chính, trả về mảng rỗng
    if (!$localKeyValue) {
      return [];
    }

    // Nếu không chỉ định bảng trung gian, tạo tên mặc định
    if (!$pivotTable) {
      $tables = [static::getTable(), $relatedModel::getTable()];
      sort($tables); // Sắp xếp để đảm bảo tên nhất quán
      $pivotTable = implode('_', $tables);
    }

    // Nếu không chỉ định khóa ngoại, tạo tên mặc định
    if (!$foreignPivotKey) {
      $tableName = static::getTable();
      // Chuyển thành số ít nếu kết thúc bằng 's'
      if (substr($tableName, -1) === 's') {
        $tableName = substr($tableName, 0, -1);
      }
      $foreignPivotKey = $tableName . '_id';
    }

    if (!$relatedPivotKey) {
      $tableName = $relatedModel::getTable();
      // Chuyển thành số ít nếu kết thúc bằng 's'
      if (substr($tableName, -1) === 's') {
        $tableName = substr($tableName, 0, -1);
      }
      $relatedPivotKey = $tableName . '_id';
    }

    // Xây dựng truy vấn JOIN để lấy dữ liệu từ bảng liên quan thông qua bảng trung gian
    $query = "SELECT r.* FROM " . $relatedModel::getTable() . " r
              INNER JOIN {$pivotTable} p ON r.{$relatedKey} = p.{$relatedPivotKey}
              WHERE p.{$foreignPivotKey} = :localKeyValue";

    $stmt = PDO()->prepare($query);
    $stmt->bindValue(':localKeyValue', $localKeyValue);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_CLASS, $relatedModel);
  }

  /**
   * Thực hiện truy vấn JOIN với bảng khác
   * 
   * @param string $relatedTable Tên bảng liên quan
   * @param string $foreignKey Khóa ngoại để JOIN
   * @param string $localKey Khóa chính của bảng hiện tại
   * @param array $columns Các cột cần lấy (mặc định là tất cả)
   * @return array Mảng kết quả
   */
  public static function join($relatedTable, $foreignKey, $localKey = null, $columns = ['*'])
  {
    $localKey = $localKey ?? static::getPrimaryKey();
    $columnsStr = is_array($columns) ? implode(', ', $columns) : $columns;

    $query = "SELECT {$columnsStr} FROM " . static::getTable() . " t1
              JOIN {$relatedTable} t2 ON t1.{$localKey} = t2.{$foreignKey}";

    $stmt = PDO()->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
  }

  /**
   * Thực hiện truy vấn LEFT JOIN với bảng khác
   * 
   * @param string $relatedTable Tên bảng liên quan
   * @param string $foreignKey Khóa ngoại để JOIN
   * @param string $localKey Khóa chính của bảng hiện tại
   * @param array $columns Các cột cần lấy (mặc định là tất cả)
   * @return array Mảng kết quả
   */
  public static function leftJoin($relatedTable, $foreignKey, $localKey = null, $columns = ['*'])
  {
    $localKey = $localKey ?? static::getPrimaryKey();
    $columnsStr = is_array($columns) ? implode(', ', $columns) : $columns;

    $query = "SELECT {$columnsStr} FROM " . static::getTable() . " t1
              LEFT JOIN {$relatedTable} t2 ON t1.{$localKey} = t2.{$foreignKey}";

    $stmt = PDO()->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
  }
}
