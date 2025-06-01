<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Booking extends Model
{
    protected static $table = 'bookings';
    protected static $primaryKey = 'id';

    public $id;
    public $booking_code;
    public $user_id;
    public $staff_id;
    public $status;
    public $booking_date;
    public $total_pets;
    public $total_services;
    public $total_duration;
    public $subtotal;
    public $discount_amount;
    public $discount_percent;
    public $total_amount;
    public $discount_code;
    public $payment_method;
    public $payment_status;
    public $paid_at;
    public $notes;
    public $customer_notes;
    public $staff_notes;
    public $cancellation_reason;
    public $cancelled_at;
    public $created_at;
    public $updated_at;

    /**
     * Tìm kiếm booking theo nhiều điều kiện
     */
    public static function searchBookings($searchTerm, $page = 1, $perPage = 10, $conditions = [])
    {
        $page = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset = ($page - 1) * $perPage;

        // Xây dựng điều kiện tìm kiếm
        $whereConditions = [];
        $params = [];

        // Tìm kiếm theo booking_code, customer name, staff name
        if (!empty($searchTerm)) {
            $whereConditions[] = "(b.booking_code LIKE :search 
                                OR c.name LIKE :search 
                                OR c.email LIKE :search 
                                OR s.name LIKE :search)";
            $params['search'] = "%{$searchTerm}%";
        }

        // Thêm các điều kiện lọc khác
        if (!empty($conditions['status'])) {
            $whereConditions[] = "b.status = :status";
            $params['status'] = $conditions['status'];
        }

        if (!empty($conditions['staff_id'])) {
            $whereConditions[] = "b.staff_id = :staff_id";
            $params['staff_id'] = $conditions['staff_id'];
        }

        if (!empty($conditions['booking_date'])) {
            $whereConditions[] = "b.booking_date = :booking_date";
            $params['booking_date'] = $conditions['booking_date'];
        }

        if (!empty($conditions['payment_status'])) {
            $whereConditions[] = "b.payment_status = :payment_status";
            $params['payment_status'] = $conditions['payment_status'];
        }

        // Xây dựng WHERE clause
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Đếm tổng số bản ghi
        $countQuery = "SELECT COUNT(*) FROM bookings b 
                      LEFT JOIN accounts c ON b.user_id = c.user_id 
                      LEFT JOIN accounts s ON b.staff_id = s.user_id 
                      {$whereClause}";

        $countStmt = PDO()->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue(":{$key}", $value);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        // Truy vấn dữ liệu với thông tin customer và staff
        $query = "SELECT b.*, 
                         c.name as customer_name, c.email as customer_email, c.phone as customer_phone,
                         s.name as staff_name, s.email as staff_email
                  FROM bookings b 
                  LEFT JOIN accounts c ON b.user_id = c.user_id 
                  LEFT JOIN accounts s ON b.staff_id = s.user_id 
                  {$whereClause} 
                  ORDER BY b.created_at DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = PDO()->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $lastPage = ceil($total / $perPage);

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
     * Lấy booking với thông tin chi tiết
     */
    public static function getBookingDetails($bookingId)
    {
        $query = "SELECT b.*, 
                         c.name as customer_name, c.email as customer_email, 
                         c.phone as customer_phone, c.address as customer_address,
                         s.name as staff_name, s.email as staff_email
                  FROM bookings b 
                  LEFT JOIN accounts c ON b.user_id = c.user_id 
                  LEFT JOIN accounts s ON b.staff_id = s.user_id
                  WHERE b.id = :id";

        $stmt = PDO()->prepare($query);
        $stmt->bindParam(':id', $bookingId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết dịch vụ của booking
     */
    public static function getBookingServices($bookingId)
    {
        $query = "SELECT bd.*, s.name as service_name, s.description as service_description,
                         st.name as service_type_name
                  FROM booking_details bd
                  LEFT JOIN services s ON bd.service_id = s.service_id
                  LEFT JOIN service_types st ON s.service_type_id = st.service_type_id
                  WHERE bd.booking_id = :booking_id";

        $stmt = PDO()->prepare($query);
        $stmt->bindParam(':booking_id', $bookingId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thú cưng của booking
     */
    public static function getBookingPets($bookingId)
    {
        $query = "SELECT bp.*, p.name as pet_name, p.type, p.breed, p.age, p.age_unit,
                         p.size, p.weight, p.color, p.gender, p.avatar_url
                  FROM booking_pets bp
                  LEFT JOIN pets p ON bp.pet_id = p.pet_id
                  WHERE bp.booking_id = :booking_id";

        $stmt = PDO()->prepare($query);
        $stmt->bindParam(':booking_id', $bookingId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái booking
     */
    public static function updateStatus($bookingId, $status, $notes = null)
    {
        $updateData = ['status' => $status];

        if ($notes !== null) {
            $updateData['staff_notes'] = $notes;
        }

        // Cập nhật thời gian tương ứng với trạng thái
        switch ($status) {
            case 'cancelled':
                $updateData['cancelled_at'] = date('Y-m-d H:i:s');
                $updateData['cancellation_reason'] = $updateData['staff_notes'];
                break;
            case 'completed':
                if (empty($updateData['staff_notes'])) {
                    $updateData['staff_notes'] = 'Dịch vụ đã hoàn thành';
                }
                $updateData['paid_at'] = date('Y-m-d H:i:s');
                $updateData['payment_status'] = 'paid';
                break;
        }

        return self::update($bookingId, $updateData);
    }

    /**
     * Tạo mã booking tự động
     */
    public static function generateBookingCode()
    {
        do {
            $code = 'BK' . date('Ymd') . sprintf('%04d', rand(1, 9999));
            $existing = self::findOneBy('booking_code', $code);
        } while ($existing);

        return $code;
    }

    /**
     * Lấy danh sách trạng thái booking
     */
    public static function getStatuses()
    {
        return [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'in_progress' => 'Đang thực hiện',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'no_show' => 'Không đến'
        ];
    }

    /**
     * Lấy danh sách phương thức thanh toán
     */
    public static function getPaymentMethods()
    {
        return [
            'cash' => 'Tiền mặt',
            'vnpay' => 'VNPay',
            'momo' => 'MoMo',
            'bank_transfer' => 'Chuyển khoản'
        ];
    }

    /**
     * Lấy danh sách trạng thái thanh toán
     */
    public static function getPaymentStatuses()
    {
        return [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại'
        ];
    }

    /**
     * Thống kê booking theo trạng thái
     */
    public static function getBookingStats()
    {
        $query = "SELECT status, COUNT(*) as count FROM bookings GROUP BY status";
        $stmt = PDO()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy booking gần nhất
     */
    public static function getRecentBookings($limit = 5)
    {
        $query = "SELECT b.*, 
                         c.name as customer_name,
                         s.name as staff_name
                  FROM bookings b 
                  LEFT JOIN accounts c ON b.user_id = c.user_id 
                  LEFT JOIN accounts s ON b.staff_id = s.user_id
                  ORDER BY b.created_at DESC 
                  LIMIT :limit";

        $stmt = PDO()->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách lịch đã đặt của nhân viên thông qua booking ID
     * 
     * @param int $bookingId ID của booking
     * @return array Danh sách lịch làm việc của nhân viên
     */
    public static function getStaffBookingSchedules($bookingId)
    {
        $query = "SELECT 
                    bss.id as booking_staff_schedule_id,
                    bss.booking_id,
                    bss.staff_schedule_id,
                    ss.staff_schedule_id,
                    ss.account_id as staff_id,
                    ss.date as schedule_date,
                    ss.time_slot_id,
                    ss.is_available,
                    ts.start_time,
                    ts.end_time,
                    a.name as staff_name,
                    a.email as staff_email,
                    a.phone as staff_phone,
                    b.booking_code,
                    b.status as booking_status,
                    b.booking_date
                  FROM booking_staff_schedule bss
                  INNER JOIN staff_schedules ss ON bss.staff_schedule_id = ss.staff_schedule_id
                  INNER JOIN time_slots ts ON ss.time_slot_id = ts.time_slot_id
                  INNER JOIN accounts a ON ss.account_id = a.user_id
                  INNER JOIN bookings b ON bss.booking_id = b.id
                  WHERE bss.booking_id = :booking_id
                  ORDER BY ss.date ASC, ts.start_time ASC";

        $stmt = PDO()->prepare($query);
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format lại dữ liệu cho dễ sử dụng
        $schedules = [];
        foreach ($results as $result) {
            $schedules[] = [
                'booking_staff_schedule_id' => $result['booking_staff_schedule_id'],
                'booking_id' => $result['booking_id'],
                'booking_code' => $result['booking_code'],
                'booking_status' => $result['booking_status'],
                'booking_date' => $result['booking_date'],
                'staff_schedule' => [
                    'staff_schedule_id' => $result['staff_schedule_id'],
                    'date' => $result['schedule_date'],
                    'is_available' => $result['is_available']
                ],
                'staff' => [
                    'staff_id' => $result['staff_id'],
                    'name' => $result['staff_name'],
                    'email' => $result['staff_email'],
                    'phone' => $result['staff_phone']
                ],
                'time_slot' => [
                    'time_slot_id' => $result['time_slot_id'],
                    'start_time' => $result['start_time'],
                    'end_time' => $result['end_time'],
                    'time_range' => $result['start_time'] . ' - ' . $result['end_time']
                ]
            ];
        }

        return $schedules;
    }

    /**
     * Lấy danh sách lịch đã đặt của nhân viên (cải thiện method hiện có)
     * 
     * @param int $bookingId ID của booking
     * @return array Danh sách lịch với thông tin đầy đủ
     */
    public static function getStaffBookings($bookingId)
    {
        try {
            $bookingStaffSchedules = BookingStaffSchedule::findWhere(['booking_id' => $bookingId]);
            $data = [];

            foreach ($bookingStaffSchedules as $bookingStaffSchedule) {
                // Lấy thông tin staff schedule
                $staffSchedule = $bookingStaffSchedule->getStaffSchedule($bookingStaffSchedule->staff_schedule_id);

                if ($staffSchedule) {
                    $scheduleData = [
                        'booking_staff_schedule_id' => $bookingStaffSchedule->id,
                        'booking_id' => $bookingStaffSchedule->booking_id,
                        'staff_schedule_id' => $bookingStaffSchedule->staff_schedule_id,
                        'staff_schedule' => [
                            'staff_schedule_id' => $staffSchedule->staff_schedule_id,
                            'account_id' => $staffSchedule->account_id,
                            'date' => $staffSchedule->date,
                            'time_slot_id' => $staffSchedule->time_slot_id,
                            'is_available' => $staffSchedule->is_available
                        ],
                        'staff' => [
                            'staff_id' => $staffSchedule->staff->user_id ?? null,
                            'name' => $staffSchedule->staff->name ?? 'N/A',
                            'email' => $staffSchedule->staff->email ?? 'N/A',
                            'phone' => $staffSchedule->staff->phone ?? 'N/A',
                            'avatar' => show_avatar($staffSchedule->staff->avatar_url)
                        ],
                        'time_slot' => [
                            'time_slot_id' => $staffSchedule->time_slot->time_slot_id ?? null,
                            'start_time' => $staffSchedule->time_slot->start_time ?? 'N/A',
                            'end_time' => $staffSchedule->time_slot->end_time ?? 'N/A',
                            'time_range' => isset($staffSchedule->time_slot->start_time, $staffSchedule->time_slot->end_time)
                                ? $staffSchedule->time_slot->start_time . ' - ' . $staffSchedule->time_slot->end_time
                                : 'N/A'
                        ],
                        'created_at' => $bookingStaffSchedule->created_at,
                        'updated_at' => $bookingStaffSchedule->updated_at
                    ];

                    $data[] = $scheduleData;
                }
            }

            return $data;
        } catch (\Exception $e) {
            // Log error nếu cần
            return [];
        }
    }

    /**
     * Lấy danh sách nhân viên được gán cho booking
     * 
     * @param int $bookingId ID của booking
     * @return array Danh sách nhân viên unique
     */
    public static function getBookingStaffList($bookingId)
    {
        $schedules = self::getStaffBookingSchedules($bookingId);
        $staffList = [];
        $uniqueStaff = [];

        foreach ($schedules as $schedule) {
            $staffId = $schedule['staff']['staff_id'];

            if (!in_array($staffId, $uniqueStaff)) {
                $uniqueStaff[] = $staffId;
                $staffList[] = [
                    'staff_id' => $staffId,
                    'name' => $schedule['staff']['name'],
                    'email' => $schedule['staff']['email'],
                    'phone' => $schedule['staff']['phone'],
                    'schedule_count' => 1
                ];
            } else {
                // Tăng số lượng lịch của nhân viên
                foreach ($staffList as &$staff) {
                    if ($staff['staff_id'] == $staffId) {
                        $staff['schedule_count']++;
                        break;
                    }
                }
            }
        }

        return $staffList;
    }

    /**
     * Lấy lịch làm việc của một nhân viên cụ thể trong booking
     * 
     * @param int $bookingId ID của booking
     * @param int $staffId ID của nhân viên
     * @return array Danh sách lịch của nhân viên đó
     */
    public static function getStaffSchedulesByBooking($bookingId, $staffId)
    {
        $allSchedules = self::getStaffBookingSchedules($bookingId);

        return array_filter($allSchedules, function ($schedule) use ($staffId) {
            return $schedule['staff']['staff_id'] == $staffId;
        });
    }

    /**
     * Lấy thống kê lịch làm việc của booking
     * 
     * @param int $bookingId ID của booking
     * @return array Thông tin thống kê
     */
    public static function getBookingScheduleStats($bookingId)
    {
        $schedules = self::getStaffBookingSchedules($bookingId);
        $staffCount = count(self::getBookingStaffList($bookingId));
        $totalSchedules = count($schedules);

        // Tính tổng thời gian (giả sử mỗi time slot là 1 giờ)
        $totalHours = $totalSchedules; // Có thể tính chính xác hơn dựa trên start_time và end_time

        return [
            'total_staff' => $staffCount,
            'total_schedules' => $totalSchedules,
            'estimated_hours' => $totalHours,
            'schedules_per_staff' => $staffCount > 0 ? round($totalSchedules / $staffCount, 2) : 0
        ];
    }

    /**
     * Lấy VnPay transaction cuối cùng của booking
     * 
     * @param int $bookingId ID của booking
     * @return VnPayTransactions|null VnPay transaction object hoặc null nếu không tìm thấy
     */
    public static function getLatestVnPayTransaction($bookingId)
    {
        $query = "SELECT * FROM vnpay_transactions 
              WHERE booking_id = :booking_id 
              ORDER BY created_at DESC 
              LIMIT 1";

        $stmt = PDO()->prepare($query);
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_CLASS, VnPayTransactions::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Lấy tất cả VnPay transactions của booking (sắp xếp theo thời gian tạo mới nhất)
     * 
     * @param int $bookingId ID của booking
     * @return array Mảng các VnPay transaction objects
     */
    public static function getAllVnPayTransactions($bookingId)
    {
        $query = "SELECT * FROM vnpay_transactions 
              WHERE booking_id = :booking_id 
              ORDER BY created_at DESC";

        $stmt = PDO()->prepare($query);
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, VnPayTransactions::class);
    }

    /**
     * Lấy VnPay transaction thành công cuối cùng của booking
     * 
     * @param int $bookingId ID của booking
     * @return VnPayTransactions|null VnPay transaction object hoặc null nếu không tìm thấy
     */
    public static function getLatestSuccessfulVnPayTransaction($bookingId)
    {
        $query = "SELECT * FROM vnpay_transactions 
              WHERE booking_id = :booking_id 
              AND vnp_response_code = '00' 
              AND vnp_transaction_status = '00'
              ORDER BY created_at DESC 
              LIMIT 1";

        $stmt = PDO()->prepare($query);
        $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_CLASS, VnPayTransactions::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Kiểm tra xem booking có transaction VnPay thành công không
     * 
     * @param int $bookingId ID của booking
     * @return bool True nếu có transaction thành công, false nếu không
     */
    public static function hasSuccessfulVnPayTransaction($bookingId)
    {
        $transaction = self::getLatestSuccessfulVnPayTransaction($bookingId);
        return $transaction !== null;
    }

    /**
     * Lấy thông tin booking kèm VnPay transaction cuối cùng
     * 
     * @param int $bookingId ID của booking
     * @return array|null Mảng chứa thông tin booking và vnpay transaction
     */
    public static function getBookingWithLatestVnPayTransaction($bookingId)
    {
        // Lấy thông tin booking
        $booking = self::getBookingDetails($bookingId);
        if (!$booking) {
            return null;
        }

        // Lấy VnPay transaction cuối cùng
        $vnpayTransaction = self::getLatestVnPayTransaction($bookingId);

        return [
            'booking' => $booking,
            'vnpay_transaction' => $vnpayTransaction ? $vnpayTransaction->toArray() : null
        ];
    }
}
