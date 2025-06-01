<?php

namespace App\Models;

use App\Core\Model;

class Review extends Model
{
  protected static $table = 'reviews';
  protected static $primaryKey = 'id';

  public $id;
  public $booking_id;
  public $user_id;
  public $service_id;
  public $rating;
  public $comment;
  public $is_anonymous;
  public $created_at;
  public $updated_at;

  /**
   * Lấy reviews theo booking_id
   */
  public static function findByBookingId($bookingId)
  {
    return self::findBy('booking_id', $bookingId);
  }

  /**
   * Lấy reviews theo user_id
   */
  public static function findByUserId($userId)
  {
    return self::findBy('user_id', $userId);
  }

  /**
   * Lấy reviews theo service_id (sắp xếp theo ngày tạo mới nhất)
   */
  public static function findByServiceId($serviceId)
  {
    $query = "SELECT * FROM " . static::$table . " WHERE service_id = :service_id ORDER BY created_at DESC";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':service_id', $serviceId);
    $stmt->execute();

    $results = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      $review = new static();
      foreach ($row as $key => $value) {
        $review->$key = $value;
      }
      $results[] = $review;
    }

    return $results;
  }

  /**
   * Lấy reviews đã được phê duyệt
   */
  public static function getApprovedReviews()
  {
    return self::findWhere(['is_approved' => 1]);
  }

  /**
   * Lấy reviews chưa được phê duyệt
   */
  public static function getPendingReviews()
  {
    return self::findWhere(['is_approved' => 0]);
  }

  /**
   * Tính rating trung bình cho một dịch vụ
   */
  public static function getAverageRatingByService($serviceId)
  {
    $query = "SELECT AVG(rating) as avg_rating FROM " . static::$table . " WHERE service_id = :service_id";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':service_id', $serviceId);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
  }

  /**
   * Đếm số lượng reviews cho một dịch vụ
   */
  public static function countReviewsByService($serviceId)
  {
    $query = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE service_id = :service_id";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':service_id', $serviceId);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'];
  }

  /**
   * Đếm số lượng reviews theo rating cụ thể cho một dịch vụ
   */
  public static function countByRating($serviceId, $rating)
  {
    $query = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE service_id = :service_id AND rating = :rating";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':service_id', $serviceId);
    $stmt->bindParam(':rating', $rating);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'];
  }

  /**
   * Đếm số lượng reviews cho một nhân viên
   */
  public static function countReviewsByStaff($staffId)
  {
    $query = "SELECT COUNT(*) as total FROM reviews r INNER JOIN bookings b ON r.booking_id = b.id WHERE b.staff_id = :staff_id AND r.rating IS NOT NULL";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':staff_id', $staffId);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'];
  }

  /**
   * Lấy rating trung bình cho một nhân viên
   */
  public static function getAverageRatingByStaff($staffId)
  {
    $query = "SELECT AVG(r.rating) as avg_rating FROM reviews r INNER JOIN bookings b ON r.booking_id = b.id WHERE b.staff_id = :staff_id AND r.rating IS NOT NULL";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':staff_id', $staffId);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
  }

  /**
   * Lấy reviews gần đây nhất cho một dịch vụ (giới hạn số lượng)
   */
  public static function getLatestReviewsByService($serviceId, $limit = 5)
  {
    $query = "SELECT * FROM " . static::$table . " WHERE service_id = :service_id ORDER BY created_at DESC LIMIT :limit";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':service_id', $serviceId);
    $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
    $stmt->execute();

    $results = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      $review = new static();
      foreach ($row as $key => $value) {
        $review->$key = $value;
      }
      $results[] = $review;
    }

    return $results;
  }

  /**
   * Lấy phân bố rating cho một dịch vụ
   */
  public static function getRatingDistribution($serviceId)
  {
    $distribution = [];
    for ($i = 1; $i <= 5; $i++) {
      $distribution[$i] = self::countByRating($serviceId, $i);
    }
    return $distribution;
  }

  /**
   * Kiểm tra user đã review dịch vụ này chưa
   */
  public static function hasUserReviewedService($userId, $serviceId)
  {
    $query = "SELECT COUNT(*) as total FROM " . static::$table . " WHERE user_id = :user_id AND service_id = :service_id";
    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':service_id', $serviceId);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'] > 0;
  }
}
