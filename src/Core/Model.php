<?php

namespace App\Core;

use PDO;

abstract class Model implements ModelInterface
{
  // Child classes must define their table
  protected static $table;

  // Primary key (can be overridden in child classes)
  protected static $primaryKey = 'id';

  /**
   * Save the current model instance (insert or update)
   * 
   * @return bool True on success, false on failure
   */
  public function save()
  {
    $primaryKey = static::$primaryKey;
    $data = $this->toArray();

    // Remove null values and primary key if it's null/empty for insert
    $data = array_filter($data, function ($value) {
      return $value !== null;
    });

    // Check if this is an update (primary key exists and has value) or insert
    $isUpdate = isset($this->$primaryKey) && !empty($this->$primaryKey);

    if ($isUpdate) {
      // Update existing record
      $id = $this->$primaryKey;
      unset($data[$primaryKey]); // Remove primary key from update data

      if (empty($data)) {
        return true; // No data to update
      }

      $setClause = [];
      foreach (array_keys($data) as $column) {
        $setClause[] = "{$column} = :{$column}";
      }
      $setClause = implode(', ', $setClause);

      $query = "UPDATE " . static::$table . " SET {$setClause} WHERE " . $primaryKey . " = :id";
      $stmt = PDO()->prepare($query);

      $stmt->bindParam(':id', $id);
      foreach ($data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }

      return $stmt->execute();
    } else {
      // Insert new record
      // Remove primary key if it's empty/null for auto-increment
      if (isset($data[$primaryKey]) && empty($data[$primaryKey])) {
        unset($data[$primaryKey]);
      }

      if (empty($data)) {
        return false; // No data to insert
      }

      $columns = implode(', ', array_keys($data));
      $placeholders = ':' . implode(', :', array_keys($data));

      $query = "INSERT INTO " . static::$table . " ({$columns}) VALUES ({$placeholders})";
      $stmt = PDO()->prepare($query);

      foreach ($data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }

      if ($stmt->execute()) {
        // Set the primary key value for the current instance
        $insertId = PDO()->lastInsertId();
        if ($insertId) {
          $this->$primaryKey = $insertId;
        }
        return true;
      }

      return false;
    }
  }

  /**
   * Find all records
   * 
   * @param string $orderBy Column to order by
   * @param string $direction Order direction (ASC or DESC)
   * @return array Array of model instances
   */
  public static function all($orderBy = null, $direction = 'ASC')
  {
    $query = "SELECT * FROM " . static::$table;

    if ($orderBy) {
      $query .= " ORDER BY {$orderBy} {$direction}";
    }

    $stmt = PDO()->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
  }

  /**
   * Find a record by its primary key
   * 
   * @param mixed $id The primary key value
   * @return static|null The model instance or null if not found
   */
  public static function find($id)
  {
    $query = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id LIMIT 1";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
    $result = $stmt->fetch();
    return $result ?: null;
  }

  /**
   * Find records by a specific field value
   * 
   * @param string $field The field name
   * @param mixed $value The field value
   * @return array Array of model instances
   */
  public static function findBy($field, $value)
  {
    $query = "SELECT * FROM " . static::$table . " WHERE {$field} = :value";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':value', $value);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
  }

  /**
   * Find first record by a specific field value
   * 
   * @param string $field The field name
   * @param mixed $value The field value
   * @return static|null The model instance or null if not found
   */
  public static function findOneBy($field, $value)
  {
    $query = "SELECT * FROM " . static::$table . " WHERE {$field} = :value LIMIT 1";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':value', $value);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
    $result = $stmt->fetch();
    return $result ?: null;
  }
  /**
   * Find records by multiple field values
   *
   * @param array $conditions Array of conditions in the format ['field' => 'value', 'field2' => 'value2']
   * @return static|null The model instance or null if not found
   */
  public static function findOneWhere(array $conditions)
  {
    $table = static::$table;
    $whereClause = [];
    $params = [];

    foreach ($conditions as $column => $value) {
      $whereClause[] = "$column = :$column";
      $params[":$column"] = $value;
    }

    $whereSql = implode(' AND ', $whereClause);
    $query = "SELECT * FROM $table WHERE $whereSql LIMIT 1";

    $stmt = PDO()->prepare($query);
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }

    $stmt->execute();

    $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
    $result = $stmt->fetch();

    return $result ?: null;
  }


  /**
   * Create a new record
   * 
   * @param array $data The data to insert
   * @return static|false The created model instance or false on failure
   */
  public static function create(array $data)
  {
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));

    $query = "INSERT INTO " . static::$table . " ({$columns}) VALUES ({$placeholders})";
    $stmt = PDO()->prepare($query);

    foreach ($data as $key => $value) {
      $stmt->bindValue(":{$key}", $value);
    }

    if ($stmt->execute()) {
      $id = PDO()->lastInsertId();
      return static::find($id);
    }

    return false;
  }

  /**
   * Update a record
   * 
   * @param mixed $id The primary key value
   * @param array $data The data to update
   * @return bool True on success, false on failure
   */
  public static function update($id, array $data)
  {
    $setClause = [];
    foreach (array_keys($data) as $column) {
      $setClause[] = "{$column} = :{$column}";
    }
    $setClause = implode(', ', $setClause);

    $query = "UPDATE " . static::$table . " SET {$setClause} WHERE " . static::$primaryKey . " = :id";
    $stmt = PDO()->prepare($query);

    $stmt->bindParam(':id', $id);
    foreach ($data as $key => $value) {
      $stmt->bindValue(":{$key}", $value);
    }

    return $stmt->execute();
  }

  /**
   * Delete a record
   * 
   * @param mixed $id The primary key value
   * @return bool True on success, false on failure
   */
  public static function delete($id)
  {
    $query = "DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }

  /**
   * Execute a custom query
   * 
   * @param string $query The SQL query
   * @param array $params The query parameters
   * @return array The query results
   */
  public static function query($query, $params = [], $fetchMode = PDO::FETCH_CLASS)
  {
    try {

      $stmt = PDO()->prepare($query);

      foreach ($params as $key => $value) {
        $stmt->bindValue(is_numeric($key) ? $key + 1 : ":{$key}", $value);
      }

      $stmt->execute();

      if ($fetchMode === PDO::FETCH_CLASS) {
        $results = $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
        return $results;
      }

      return $stmt->fetchAll($fetchMode);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Tìm kiếm theo nhiều điều kiện
   * 
   * @param array $conditions Mảng điều kiện dạng ['field' => 'value', 'field2' => 'value2']
   * @param string $operator Toán tử logic giữa các điều kiện (AND/OR)
   * @return array Mảng các đối tượng model
   */
  public static function findWhere(array $conditions, $operator = 'AND')
  {
    if (empty($conditions)) {
      return self::all();
    }

    $query = "SELECT * FROM " . static::$table . " WHERE ";
    $whereConditions = [];
    $params = [];

    foreach ($conditions as $field => $value) {
      if ($value === null) {
        $whereConditions[] = "{$field} IS NULL";
      } else {
        $whereConditions[] = "{$field} = :{$field}";
        $params[$field] = $value;
      }
    }

    $query .= implode(" {$operator} ", $whereConditions);

    $stmt = PDO()->prepare($query);
    foreach ($params as $key => $value) {
      $stmt->bindValue(":{$key}", $value);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
  }

  /**
   * Tìm kiếm nâng cao với các toán tử so sánh
   * 
   * @param array $conditions Mảng điều kiện dạng [['field', 'operator', 'value'], ['field2', 'operator2', 'value2']]
   * @param string $operator Toán tử logic giữa các điều kiện (AND/OR)
   * @return array Mảng các đối tượng model
   */
  public static function findWhereAdvanced(array $conditions, $operator = 'AND')
  {
    if (empty($conditions)) {
      return self::all();
    }

    $query = "SELECT * FROM " . static::$table . " WHERE ";
    $whereConditions = [];
    $params = [];
    $paramIndex = 0;

    foreach ($conditions as $condition) {
      if (count($condition) < 3) {
        continue;
      }

      list($field, $compOperator, $value) = $condition;
      $paramName = "param" . $paramIndex++;

      if ($value === null && ($compOperator === '=' || $compOperator === '!=')) {
        $whereConditions[] = "{$field} " . ($compOperator === '=' ? 'IS NULL' : 'IS NOT NULL');
      } else {
        $whereConditions[] = "{$field} {$compOperator} :{$paramName}";
        $params[$paramName] = $value;
      }
    }

    $query .= implode(" {$operator} ", $whereConditions);

    $stmt = PDO()->prepare($query);
    foreach ($params as $key => $value) {
      $stmt->bindValue(":{$key}", $value);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
  }

  /**
   * Phân trang dữ liệu
   * 
   * @param int $page Số trang hiện tại (bắt đầu từ 1)
   * @param int $perPage Số bản ghi mỗi trang
   * @param string $orderBy Cột để sắp xếp
   * @param string $direction Hướng sắp xếp (ASC/DESC)
   * @return array Mảng chứa 'data' (dữ liệu trang hiện tại), 'total' (tổng số bản ghi), 'last_page' (trang cuối cùng)
   */
  public static function paginate($page = 1, $perPage = 10, $orderBy = null, $direction = 'ASC')
  {
    // Đảm bảo page và perPage là số nguyên dương
    $page = max(1, (int)$page);
    $perPage = max(1, (int)$perPage);

    // Đếm tổng số bản ghi
    $countQuery = "SELECT COUNT(*) FROM " . static::$table;
    $countStmt = PDO()->prepare($countQuery);
    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();

    // Tính toán offset và trang cuối cùng
    $offset = ($page - 1) * $perPage;
    $lastPage = ceil($total / $perPage);

    // Truy vấn dữ liệu cho trang hiện tại
    $query = "SELECT * FROM " . static::$table;

    if ($orderBy) {
      $query .= " ORDER BY {$orderBy} {$direction}";
    }

    $query .= " LIMIT :limit OFFSET :offset";

    $stmt = PDO()->prepare($query);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_CLASS, static::class);

    return [
      'data' => $data,
      'total' => $total,
      'current_page' => $page,
      'per_page' => $perPage,
      'last_page' => $lastPage,
      'from' => $offset + 1,
      'to' => min($offset + $perPage, $total)
    ];
  }

  /**
   * Phân trang với điều kiện tìm kiếm
   * 
   * @param array $conditions Mảng điều kiện dạng ['field' => 'value', 'field2' => 'value2']
   * @param int $page Số trang hiện tại (bắt đầu từ 1)
   * @param int $perPage Số bản ghi mỗi trang
   * @param string $operator Toán tử logic giữa các điều kiện (AND/OR)
   * @param string $orderBy Cột để sắp xếp
   * @param string $direction Hướng sắp xếp (ASC/DESC)
   * @return array Mảng chứa 'data', 'total', 'last_page', etc.
   */
  public static function paginateWhere(array $conditions, $page = 1, $perPage = 10, $operator = 'AND', $orderBy = null, $direction = 'ASC')
  {
    // Đảm bảo page và perPage là số nguyên dương
    $page = max(1, (int)$page);
    $perPage = max(1, (int)$perPage);

    // Xây dựng phần WHERE của truy vấn
    $whereClause = "";
    $params = [];

    if (!empty($conditions)) {
      $whereConditions = [];

      foreach ($conditions as $field => $value) {
        if ($value === null) {
          $whereConditions[] = "{$field} IS NULL";
        } else {
          if (isset($value['operator']) && isset($value['value'])) {
            $whereConditions[] = "{$field} {$value['operator']} :{$field}";
            $params[$field] = $value['value'];
          } else {
            $whereConditions[] = "{$field} = :{$field}";
            $params[$field] = $value;
          }
        }
      }

      $whereClause = " WHERE " . implode(" {$operator} ", $whereConditions);
    }

    // Đếm tổng số bản ghi thỏa mãn điều kiện
    $countQuery = "SELECT COUNT(*) FROM " . static::$table . $whereClause;
    $countStmt = PDO()->prepare($countQuery);

    foreach ($params as $key => $value) {
      $countStmt->bindValue(":{$key}", $value);
    }

    $countStmt->execute();
    $total = (int)$countStmt->fetchColumn();

    // Tính toán offset và trang cuối cùng
    $offset = ($page - 1) * $perPage;
    $lastPage = ceil($total / $perPage);

    // Truy vấn dữ liệu cho trang hiện tại
    $query = "SELECT * FROM " . static::$table . $whereClause;

    if ($orderBy) {
      $query .= " ORDER BY {$orderBy} {$direction}";
    }

    $query .= " LIMIT :limit OFFSET :offset";

    $stmt = PDO()->prepare($query);

    foreach ($params as $key => $value) {
      $stmt->bindValue(":{$key}", $value);
    }

    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_CLASS, static::class);

    return [
      'data' => $data,
      'total' => $total,
      'current_page' => $page,
      'total_pages' => ceil($total / $perPage), // Thay thế 'last_page' bằng 'total_pages'
      'per_page' => $perPage,
      'last_page' => $lastPage,
      'from' => $offset + 1,
      'to' => min($offset + $perPage, $total)
    ];
  }

  /**
   * Lấy tên khóa chính của model
   * 
   * @return string|array
   */
  public static function getPrimaryKey()
  {
    return static::$primaryKey;
  }

  /**
   * Lấy tên bảng của model
   * 
   * @return string
   */
  public static function getTable()
  {
    return static::$table;
  }

  public function toArray()
  {
    $properties = get_object_vars($this);
    return $properties;
  }
}
