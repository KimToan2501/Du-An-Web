<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class StaffSchedule extends Model
{
  protected static $table = 'staff_schedules';
  protected static $primaryKey = 'staff_schedule_id';

  public $staff_schedule_id;
  public $account_id;
  public $date;
  public $time_slot_id;
  public $is_available;
  public $created_at;
  public $updated_at;

  public $staff;
  public $time_slot;

  public function get_time_slot($time_slot_id)
  {
    return TimeSlot::findOneBy('time_slot_id', $time_slot_id);
  }

  public function get_account($account_id)
  {
    return Account::findBy('user_id', $account_id);
  }

  public static function getStaff($id)
  {
    $staff = Account::find($id);
    return $staff;
  }

  /**
   * Tìm kiếm lịch làm việc theo thời gian và nhân viên
   * 
   * @param int $staffId ID của nhân viên
   * @param string $searchTerm Từ khóa tìm kiếm (thời gian)
   * @param int $page Trang hiện tại
   * @param int $perPage Số bản ghi trên mỗi trang
   * @return array Kết quả phân trang
   */
  public static function searchByTimeSlot($staffId, $searchTerm, $page = 1, $perPage = 10)
  {
    try {
      // Lấy tất cả time slots để tìm kiếm
      $timeSlots = TimeSlot::all();
      $matchingTimeSlotIds = [];

      $searchTermLower = strtolower(trim($searchTerm));

      

      // Tìm kiếm trong các time slot
      foreach ($timeSlots as $timeSlot) {
        $startTime = strtolower($timeSlot->start_time);
        $endTime = strtolower($timeSlot->end_time);
        $timeRange = $startTime . '-' . $endTime;
        $timeRangeWithSpace = $startTime . ' - ' . $endTime;

        // Kiểm tra nhiều định dạng tìm kiếm
        if (
          self::matchesSearchTerm($startTime, $searchTermLower) ||
          self::matchesSearchTerm($endTime, $searchTermLower) ||
          self::matchesSearchTerm($timeRange, $searchTermLower) ||
          self::matchesSearchTerm($timeRangeWithSpace, $searchTermLower)
        ) {
          $matchingTimeSlotIds[] = $timeSlot->time_slot_id;
        }
      }

      if (empty($matchingTimeSlotIds)) {
        return [
          'data' => [],
          'current_page' => 1,
          'last_page' => 1,
          'total' => 0
        ];
      }

      // Tìm kiếm staff schedules với time slot ids phù hợp
      return self::paginateByTimeSlotIds($staffId, $matchingTimeSlotIds, $page, $perPage);
    } catch (\Exception $e) {
      return [
        'data' => [],
        'current_page' => 1,
        'last_page' => 1,
        'total' => 0
      ];
    }
  }

  /**
   * Kiểm tra xem chuỗi có khớp với từ khóa tìm kiếm không
   */
  private static function matchesSearchTerm($haystack, $needle)
  {
    return strpos($haystack, $needle) !== false;
  }

  /**
   * Phân trang staff schedules theo time slot IDs
   */
  private static function paginateByTimeSlotIds($staffId, $timeSlotIds, $page, $perPage)
  {
    try {
      if (empty($timeSlotIds)) {
        return [
          'data' => [],
          'current_page' => 1,
          'last_page' => 1,
          'total' => 0
        ];
      }

      // Tạo placeholder cho IN clause
      $placeholders = implode(',', array_fill(0, count($timeSlotIds), '?'));

      // Tham số cho query
      $params = array_merge([$staffId], $timeSlotIds);

      // Đếm tổng số records
      $countSql = "SELECT COUNT(*) as total FROM " . self::$table . " 
                   WHERE account_id = ? AND time_slot_id IN ($placeholders)";

      $totalResult = self::query($countSql, $params, PDO::FETCH_ASSOC);

      $total = isset($totalResult[0]) ? (int)$totalResult[0]['total'] : 0;

      // Tính toán phân trang
      $lastPage = max(1, ceil($total / $perPage));
      $currentPage = max(1, min($page, $lastPage));
      $offset = ($currentPage - 1) * $perPage;

      // Lấy dữ liệu với phân trang
      $dataSql = "SELECT * FROM " . self::$table . " 
                  WHERE account_id = ? AND time_slot_id IN ($placeholders)
                  ORDER BY date DESC, time_slot_id ASC
                  LIMIT $perPage OFFSET $offset";

      $schedules = self::query($dataSql, $params);

      // Chuyển đổi kết quả thành objects
      $data = [];
      foreach ($schedules as $schedule) {
        $staffSchedule = new self();
        foreach ($schedule as $key => $value) {
          if (property_exists($staffSchedule, $key)) {
            $staffSchedule->$key = $value;
          }
        }
        $data[] = $staffSchedule;
      }

      return [
        'data' => $data,
        'current_page' => $currentPage,
        'last_page' => $lastPage,
        'total' => $total
      ];
    } catch (\Throwable $th) {

      error_log('Error query::::' . $th->getMessage());

      return [
        'data' => [],
        'current_page' => 1,
        'last_page' => 1,
        'total' => 0
      ];
    }
  }

  /**
   * Lấy danh sách lịch làm việc của nhân viên với thông tin time slot
   * 
   * @param int $staffId ID của nhân viên
   * @param string $date Ngày cụ thể (optional)
   * @return array Danh sách lịch làm việc
   */
  public static function getStaffScheduleWithTimeSlot($staffId, $date = null)
  {
    $conditions = ['account_id' => $staffId];

    if ($date) {
      $conditions['date'] = $date;
    }

    $schedules = self::findWhere($conditions);
    $result = [];

    foreach ($schedules as $schedule) {
      $timeSlot = TimeSlot::findOneBy('time_slot_id', $schedule->time_slot_id);
      if ($timeSlot) {
        $scheduleData = [
          'schedule' => $schedule,
          'time_slot' => $timeSlot,
          'time_range' => $timeSlot->start_time . ' - ' . $timeSlot->end_time
        ];
        $result[] = $scheduleData;
      }
    }

    return $result;
  }

  /**
   * Kiểm tra xem nhân viên có lịch làm việc trong khoảng thời gian không
   * 
   * @param int $staffId ID của nhân viên
   * @param string $date Ngày
   * @param string $startTime Thời gian bắt đầu
   * @param string $endTime Thời gian kết thúc
   * @return bool
   */
  public static function hasScheduleInTimeRange($staffId, $date, $startTime, $endTime)
  {
    // Lấy tất cả time slots trong khoảng thời gian
    $timeSlots = TimeSlot::findWhere([]);
    $overlappingTimeSlotIds = [];

    foreach ($timeSlots as $timeSlot) {
      if (self::timeRangesOverlap($startTime, $endTime, $timeSlot->start_time, $timeSlot->end_time)) {
        $overlappingTimeSlotIds[] = $timeSlot->time_slot_id;
      }
    }

    if (empty($overlappingTimeSlotIds)) {
      return false;
    }

    // Kiểm tra xem có lịch làm việc nào trong các time slot này không
    foreach ($overlappingTimeSlotIds as $timeSlotId) {
      $existingSchedule = self::findOneWhere([
        'account_id' => $staffId,
        'date' => $date,
        'time_slot_id' => $timeSlotId
      ]);

      if ($existingSchedule) {
        return true;
      }
    }

    return false;
  }

  /**
   * Kiểm tra xem hai khoảng thời gian có chồng lấp không
   */
  private static function timeRangesOverlap($start1, $end1, $start2, $end2)
  {
    return ($start1 < $end2) && ($start2 < $end1);
  }

  /**
   * Lấy thống kê lịch làm việc của nhân viên
   * 
   * @param int $staffId ID của nhân viên
   * @param string $fromDate Từ ngày (optional)
   * @param string $toDate Đến ngày (optional)
   * @return array Thông tin thống kê
   */
  public static function getScheduleStats($staffId, $fromDate = null, $toDate = null)
  {
    $conditions = ['account_id' => $staffId];
    $params = [$staffId];
    $whereClause = "account_id = ?";

    if ($fromDate) {
      $whereClause .= " AND date >= ?";
      $params[] = $fromDate;
    }

    if ($toDate) {
      $whereClause .= " AND date <= ?";
      $params[] = $toDate;
    }

    // Đếm tổng số lịch làm việc
    $totalSql = "SELECT COUNT(*) as total FROM " . self::$table . " WHERE $whereClause";
    $totalResult = self::query($totalSql, $params);
    $total = isset($totalResult[0]) ? (int)$totalResult[0]->total : 0;

    // Đếm số ngày có lịch làm việc
    $daysSql = "SELECT COUNT(DISTINCT date) as unique_days FROM " . self::$table . " WHERE $whereClause";
    $daysResult = self::query($daysSql, $params);
    $uniqueDays = isset($daysResult[0]) ? (int)$daysResult[0]->unique_days : 0;

    return [
      'total_schedules' => $total,
      'unique_days' => $uniqueDays,
      'average_schedules_per_day' => $uniqueDays > 0 ? round($total / $uniqueDays, 2) : 0
    ];
  }

  public static function getScheduleByDate($date)
  {
    $conditions = ['date' => $date];
    $results = self::findWhere($conditions);

    foreach ($results as $result) {
      $result->staff = Account::findOneBy('user_id', $result->account_id);
      $result->time_slot = TimeSlot::findOneBy('time_slot_id', $result->time_slot_id);
    }

    return $results;
  }
}
