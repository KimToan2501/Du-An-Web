<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Blog extends Model
{
  protected static $table = 'blogs';
  protected static $primaryKey = 'blog_id';

  public $blog_id;
  public $title;
  public $slug;
  public $content;
  public $excerpt;
  public $featured_image;
  public $status;
  public $meta_title;
  public $meta_description;
  public $view_count;
  public $created_at;
  public $updated_at;

  /**
   * Tìm blog theo slug
   * 
   * @param string $slug
   * @return static|null
   */
  public static function findBySlug($slug)
  {
    return static::findOneBy('slug', $slug);
  }

  /**
   * Lấy các blog đã published
   * 
   * @param string $orderBy
   * @param string $direction
   * @return array
   */
  public static function getPublished($orderBy = 'created_at', $direction = 'DESC')
  {
    return static::findWhere(['status' => 'published'], 'AND', $orderBy, $direction);
  }

  /**
   * Phân trang blog published
   * 
   * @param int $page
   * @param int $perPage
   * @return array
   */
  public static function paginatePublished($page = 1, $perPage = 10)
  {
    return static::paginateWhere(
      ['status' => 'published'],
      $page,
      $perPage,
      'AND',
      'created_at',
      'DESC'
    );
  }

  /**
   * Lấy bài viết liên quan
   * 
   * @param int $excludeId
   * @param int $limit
   * @return array
   */
  public static function getRelatedBlogs($excludeId, $limit = 4)
  {
    $query = "SELECT * FROM " . static::$table . " 
              WHERE status = 'published' AND " . static::$primaryKey . " != :exclude_id 
              ORDER BY created_at DESC LIMIT :limit";

    $stmt = \PDO()->prepare($query);
    $stmt->bindParam(':exclude_id', $excludeId, \PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
  }

  /**
   * Tìm kiếm blog published
   * 
   * @param string $keyword
   * @param int $page
   * @param int $perPage
   * @return array
   */
  public static function searchPublished($keyword, $page = 1, $perPage = 10)
  {
    $offset = ($page - 1) * $perPage;

    // Count total results
    $countQuery = "SELECT COUNT(*) FROM " . static::$table . " 
                   WHERE status = 'published' 
                   AND (title LIKE :keyword1 OR content LIKE :keyword2 OR excerpt LIKE :keyword3)";

    $countStmt = PDO()->prepare($countQuery);
    $searchTerm = '%' . $keyword . '%';
    $countStmt->bindParam(':keyword1', $searchTerm);
    $countStmt->bindParam(':keyword2', $searchTerm);
    $countStmt->bindParam(':keyword3', $searchTerm);
    $countStmt->execute();
    $total = $countStmt->fetchColumn();

    // Get paginated results
    $query = "SELECT * FROM " . static::$table . " 
              WHERE status = 'published' 
              AND (title LIKE :keyword1 OR content LIKE :keyword2 OR excerpt LIKE :keyword3)
              ORDER BY created_at DESC 
              LIMIT :limit OFFSET :offset";

    $stmt = PDO()->prepare($query);
    $stmt->bindParam(':keyword1', $searchTerm);
    $stmt->bindParam(':keyword2', $searchTerm);
    $stmt->bindParam(':keyword3', $searchTerm);
    $stmt->bindParam(':limit', $perPage, \PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);

    return [
      'data' => $data,
      'total' => $total,
      'per_page' => $perPage,
      'current_page' => $page,
      'total_pages' => ceil($total / $perPage)
    ];
  }

  /**
   * Lấy blog phổ biến (view count cao)
   * 
   * @param int $limit
   * @return array
   */
  public static function getPopular($limit = 5)
  {
    return static::findWhere(['status' => 'published'], 'AND', 'view_count', 'DESC', $limit);
  }

  /**
   * Lấy blog mới nhất
   * 
   * @param int $limit
   * @return array
   */
  public static function getLatest($limit = 5)
  {
    return static::findWhere(['status' => 'published'], 'AND', 'created_at', 'DESC', $limit);
  }

  /**
   * Tạo slug từ title
   * 
   * @param string $title
   * @return string
   */
  public static function createSlug($title)
  {
    // Chuyển về chữ thường
    $slug = strtolower($title);

    // Thay thế các ký tự đặc biệt tiếng Việt
    $vietnamese = [
      'à',
      'á',
      'ạ',
      'ả',
      'ã',
      'â',
      'ầ',
      'ấ',
      'ậ',
      'ẩ',
      'ẫ',
      'ă',
      'ằ',
      'ắ',
      'ặ',
      'ẳ',
      'ẵ',
      'è',
      'é',
      'ẹ',
      'ẻ',
      'ẽ',
      'ê',
      'ề',
      'ế',
      'ệ',
      'ể',
      'ễ',
      'ì',
      'í',
      'ị',
      'ỉ',
      'ĩ',
      'ò',
      'ó',
      'ọ',
      'ỏ',
      'õ',
      'ô',
      'ồ',
      'ố',
      'ộ',
      'ổ',
      'ỗ',
      'ơ',
      'ờ',
      'ớ',
      'ợ',
      'ở',
      'ỡ',
      'ù',
      'ú',
      'ụ',
      'ủ',
      'ũ',
      'ư',
      'ừ',
      'ứ',
      'ự',
      'ử',
      'ữ',
      'ỳ',
      'ý',
      'ỵ',
      'ỷ',
      'ỹ',
      'đ'
    ];

    $english = [
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'a',
      'e',
      'e',
      'e',
      'e',
      'e',
      'e',
      'e',
      'e',
      'e',
      'e',
      'e',
      'i',
      'i',
      'i',
      'i',
      'i',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'o',
      'u',
      'u',
      'u',
      'u',
      'u',
      'u',
      'u',
      'u',
      'u',
      'u',
      'u',
      'y',
      'y',
      'y',
      'y',
      'y',
      'd'
    ];

    $slug = str_replace($vietnamese, $english, $slug);

    // Thay thế khoảng trắng và ký tự đặc biệt bằng dấu gạch ngang
    $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');

    return $slug;
  }

  /**
   * Kiểm tra slug có tồn tại không (trừ blog hiện tại)
   * 
   * @param string $slug
   * @param int|null $excludeId
   * @return bool
   */
  public static function slugExists($slug, $excludeId = null)
  {
    $blog = static::findBySlug($slug);

    if (!$blog) {
      return false;
    }

    if ($excludeId && $blog->blog_id == $excludeId) {
      return false;
    }

    return true;
  }

  /**
   * Tạo slug unique
   * 
   * @param string $title
   * @param int|null $excludeId
   * @return string
   */
  public static function createUniqueSlug($title, $excludeId = null)
  {
    $baseSlug = static::createSlug($title);
    $slug = $baseSlug;
    $counter = 1;

    while (static::slugExists($slug, $excludeId)) {
      $slug = $baseSlug . '-' . $counter;
      $counter++;
    }

    return $slug;
  }

  /**
   * Tăng view count
   * 
   * @param int $id
   * @return bool
   */
  public static function incrementViewCount($id)
  {
    $query = "UPDATE " . static::$table . " SET view_count = view_count + 1 WHERE " . static::$primaryKey . " = :id";
    $stmt = \PDO()->prepare($query);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
  }

  /**
   * Lấy excerpt từ content nếu không có excerpt
   * 
   * @param int $length
   * @return string
   */
  public function getExcerpt($length = 150)
  {
    if (!empty($this->excerpt)) {
      return $this->excerpt;
    }

    // Loại bỏ HTML tags và lấy text thuần
    $plainText = strip_tags($this->content);

    if (strlen($plainText) <= $length) {
      return $plainText;
    }

    return substr($plainText, 0, $length) . '...';
  }

  /**
   * Format created_at
   * 
   * @param string $format
   * @return string
   */
  public function getFormattedDate($format = 'd/m/Y H:i')
  {
    return date($format, strtotime($this->created_at));
  }

  /**
   * Lấy URL của blog
   * 
   * @return string
   */
  public function getUrl()
  {
    return base_url('blog/' . $this->slug);
  }

  /**
   * Lấy featured image URL
   * 
   * @return string
   */
  public function getFeaturedImageUrl()
  {
    if ($this->featured_image) {
      return base_url($this->featured_image);
    }

    return base_url('/assets/images/default-blog.png');
  }

  /**
   * Kiểm tra có featured image không
   * 
   * @return bool
   */
  public function hasFeaturedImage()
  {
    return !empty($this->featured_image);
  }
}
