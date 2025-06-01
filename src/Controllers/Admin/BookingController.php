<?php

namespace App\Controllers\Admin;

use App\Core\RestApi;
use App\Core\UserRole;
use App\Models\Account;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\BookingPet;
use App\Models\Service;
use App\Models\TimeSlot;

class BookingController
{
  public function index()
  {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $searching = isset($_GET['q']) ? (string)$_GET['q'] : '';
    $statusFilter = isset($_GET['status']) ? (string)$_GET['status'] : '';
    $staffFilter = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : '';
    $dateFilter = isset($_GET['date']) ? (string)$_GET['date'] : '';
    $paymentStatusFilter = isset($_GET['payment_status']) ? (string)$_GET['payment_status'] : '';
    $perPage = 10;

    // Tạo điều kiện lọc
    $conditions = [];
    if (!empty($statusFilter)) {
      $conditions['status'] = $statusFilter;
    }
    if (!empty($staffFilter)) {
      $conditions['staff_id'] = $staffFilter;
    }
    if (!empty($dateFilter)) {
      $conditions['booking_date'] = $dateFilter;
    }
    if (!empty($paymentStatusFilter)) {
      $conditions['payment_status'] = $paymentStatusFilter;
    }

    // Thực hiện tìm kiếm hoặc lấy tất cả
    if (!empty($searching) || !empty($conditions)) {
      $result = Booking::searchBookings($searching, $page, $perPage, $conditions);
    } else {
      $result = Booking::paginate($page, $perPage, 'created_at', 'DESC');
      // Convert result data to match search format
      $bookings = [];
      foreach ($result['data'] as $booking) {
        $bookingDetails = Booking::getBookingDetails($booking->id);
        $bookings[] = $bookingDetails;
      }
      $result['data'] = $bookings;
    }

    // Lấy danh sách nhân viên để lọc
    $staffList = Account::findWhere(['role' => UserRole::STAFF]);

    $title = 'Quản lý booking';

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => $title],
      ],
      'metadata' => $result['data'],
      'pagination' => [
        'current' => $result['current_page'],
        'last' => $result['last_page'],
        'total' => $result['total']
      ],
      'searching' => $searching,
      'statusFilter' => $statusFilter,
      'staffFilter' => $staffFilter,
      'dateFilter' => $dateFilter,
      'paymentStatusFilter' => $paymentStatusFilter,
      'staffList' => $staffList,
      'statuses' => Booking::getStatuses(),
      'paymentStatuses' => Booking::getPaymentStatuses()
    ];

    render_view('admin/booking/index', $data, 'admin');
  }

  public function showAdd()
  {
    $customers = Account::findWhere(['role' => UserRole::CUSTOMER]);
    $staff = Account::findWhere(['role' => UserRole::STAFF]);
    $services = Service::all();
    $timeSlots = TimeSlot::all();

    $title = 'Thêm booking';

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý booking', 'url' => '/admin/booking'],
        ['text' => $title],
      ],
      'customers' => $customers,
      'staff' => $staff,
      'services' => $services,
      'timeSlots' => $timeSlots,
      'statuses' => Booking::getStatuses(),
      'paymentMethods' => Booking::getPaymentMethods(),
      'paymentStatuses' => Booking::getPaymentStatuses()
    ];

    render_view('admin/booking/add', $data, 'admin');
  }

  public function add()
  {
    try {
      RestApi::setHeaders();

      // Validate required fields
      $requiredFields = ['user_id', 'staff_id', 'booking_date', 'time_slot_id'];
      foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
          RestApi::responseError("Trường {$field} không được để trống.");
        }
      }

      // Validate customer exists
      $customer = Account::findOneWhere([
        'user_id' => $_POST['user_id'],
        'role' => UserRole::CUSTOMER
      ]);
      if (!$customer) {
        RestApi::responseError('Khách hàng không tồn tại.');
      }

      // Validate staff exists
      $staff = Account::findOneWhere([
        'user_id' => $_POST['staff_id'],
        'role' => UserRole::STAFF
      ]);
      if (!$staff) {
        RestApi::responseError('Nhân viên không tồn tại.');
      }

      // Generate booking code
      $bookingCode = Booking::generateBookingCode();

      // Create booking data
      $bookingData = [
        'booking_code' => $bookingCode,
        'user_id' => $_POST['user_id'],
        'staff_id' => $_POST['staff_id'],
        'booking_date' => $_POST['booking_date'],
        'time_slot_id' => $_POST['time_slot_id'],
        'status' => $_POST['status'] ?? 'pending',
        'payment_method' => $_POST['payment_method'] ?? 'cash',
        'payment_status' => $_POST['payment_status'] ?? 'pending',
        'total_pets' => $_POST['total_pets'] ?? 1,
        'total_services' => $_POST['total_services'] ?? 1,
        'total_duration' => $_POST['total_duration'] ?? 0,
        'subtotal' => $_POST['subtotal'] ?? 0,
        'discount_amount' => $_POST['discount_amount'] ?? 0,
        'discount_percent' => $_POST['discount_percent'] ?? 0,
        'total_amount' => $_POST['total_amount'] ?? 0,
        'discount_code' => $_POST['discount_code'] ?? null,
        'notes' => $_POST['notes'] ?? null,
        'customer_notes' => $_POST['customer_notes'] ?? null,
        'staff_notes' => $_POST['staff_notes'] ?? null
      ];

      $booking = Booking::create($bookingData);

      if ($booking) {
        RestApi::responseSuccess($booking, 'Đã thêm booking thành công', 201);
      } else {
        RestApi::responseError('Không thể tạo booking.');
      }
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function show($id)
  {
    $booking = Booking::getBookingDetails($id);
    if (!$booking) {
      redirect(base_url('/404'));
    }

    $services = Booking::getBookingServices($id);
    $pets = Booking::getBookingPets($id);
    $staffSchedules = Booking::getStaffBookings($id);
    $transaction = Booking::getLatestVnPayTransaction($id);

    // dd($booking);

    $title = 'Chi tiết booking #' . $booking['booking_code'];

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý booking', 'url' => '/admin/booking'],
        ['text' => $title],
      ],
      'booking' => $booking,
      'services' => $services,
      'pets' => $pets,
      'staffSchedules' => $staffSchedules,
      'statuses' => Booking::getStatuses(),
      'paymentMethods' => Booking::getPaymentMethods(),
      'paymentStatuses' => Booking::getPaymentStatuses(),
      'transaction' => $transaction
    ];

    render_view('admin/booking/detail', $data, 'admin');
  }

  public function showUpdate($id)
  {
    $booking = Booking::getBookingDetails($id);
    if (!$booking) {
      redirect(base_url('/404'));
    }

    $customers = Account::findWhere(['role' => UserRole::CUSTOMER]);
    $staff = Account::findWhere(['role' => UserRole::STAFF]);
    $services = Service::all();
    $timeSlots = TimeSlot::all();
    $bookingServices = Booking::getBookingServices($id);
    $bookingPets = Booking::getBookingPets($id);

    $title = 'Cập nhật booking #' . $booking['booking_code'];

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý booking', 'url' => '/admin/booking'],
        ['text' => $title],
      ],
      'booking' => $booking,
      'customers' => $customers,
      'staff' => $staff,
      'services' => $services,
      'timeSlots' => $timeSlots,
      'bookingServices' => $bookingServices,
      'bookingPets' => $bookingPets,
      'statuses' => Booking::getStatuses(),
      'paymentMethods' => Booking::getPaymentMethods(),
      'paymentStatuses' => Booking::getPaymentStatuses()
    ];

    render_view('admin/booking/update', $data, 'admin');
  }

  public function edit($id)
  {
    try {
      RestApi::setHeaders();

      $booking = Booking::find($id);
      if (!$booking) {
        RestApi::responseError('Booking không tồn tại.');
      }

      // Validate customer exists if changed
      if (!empty($_POST['user_id'])) {
        $customer = Account::findOneWhere([
          'user_id' => $_POST['user_id'],
          'role' => UserRole::CUSTOMER
        ]);
        if (!$customer) {
          RestApi::responseError('Khách hàng không tồn tại.');
        }
      }

      // Validate staff exists if changed
      if (!empty($_POST['staff_id'])) {
        $staff = Account::findOneWhere([
          'user_id' => $_POST['staff_id'],
          'role' => UserRole::STAFF
        ]);
        if (!$staff) {
          RestApi::responseError('Nhân viên không tồn tại.');
        }
      }

      // Prepare update data
      $updateData = [];
      $allowedFields = [
        'user_id',
        'staff_id',
        'status',
        'booking_date',
        'time_slot_id',
        'total_pets',
        'total_services',
        'total_duration',
        'subtotal',
        'discount_amount',
        'discount_percent',
        'total_amount',
        'discount_code',
        'payment_method',
        'payment_status',
        'notes',
        'customer_notes',
        'staff_notes'
      ];

      foreach ($allowedFields as $field) {
        if (isset($_POST[$field])) {
          $updateData[$field] = $_POST[$field];
        }
      }

      // Handle special status updates
      if (isset($_POST['status'])) {
        switch ($_POST['status']) {
          case 'cancelled':
            $updateData['cancelled_at'] = date('Y-m-d H:i:s');
            if (empty($updateData['staff_notes'])) {
              $updateData['cancellation_reason'] = $_POST['cancellation_reason'] ?? 'Booking bị hủy bởi admin';
            }
            break;
          case 'paid':
            if ($booking->payment_status !== 'paid') {
              $updateData['paid_at'] = date('Y-m-d H:i:s');
            }
            break;
        }
      }

      $result = Booking::update($id, $updateData);

      if ($result) {
        RestApi::responseSuccess(true, 'Đã cập nhật booking thành công', 200);
      } else {
        RestApi::responseError('Không thể cập nhật booking.');
      }
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function delete($id)
  {
    try {
      RestApi::setHeaders();

      $booking = Booking::find($id);
      if (!$booking) {
        RestApi::responseError('Booking không tồn tại.');
      }

      // Chỉ cho phép xóa booking ở trạng thái pending hoặc cancelled
      if (!in_array($booking->status, ['pending', 'cancelled'])) {
        RestApi::responseError('Chỉ có thể xóa booking ở trạng thái "Chờ xác nhận" hoặc "Đã hủy".');
      }

      $result = Booking::delete($id);

      if ($result) {
        RestApi::responseSuccess(true, 'Đã xóa booking thành công', 200);
      } else {
        RestApi::responseError('Không thể xóa booking.');
      }
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  /**
   * Cập nhật nhanh trạng thái booking (cho dashboard)
   */
  public function quickUpdate($id)
  {
    try {
      RestApi::setHeaders();

      $booking = Booking::find($id);
      if (!$booking) {
        RestApi::responseError('Booking không tồn tại.');
      }

      $action = $_POST['action'] ?? '';
      $notes = $_POST['notes'] ?? '';

      switch ($action) {
        case 'accept':
          $result = Booking::updateStatus($id, 'confirmed', $notes);
          $message = 'Đã xác nhận booking thành công';
          break;
        case 'reject':
          $result = Booking::updateStatus($id, 'cancelled', $notes ?: 'Booking bị từ chối');
          $message = 'Đã từ chối booking thành công';
          break;
        case 'finish':
          $result = Booking::updateStatus($id, 'completed', $notes ?: 'Dịch vụ đã hoàn thành');
          $message = 'Đã hoàn thành booking thành công';
          break;
        case 'in_progress':
          $result = Booking::updateStatus($id, 'in_progress', $notes ?: 'Đang thực hiện dịch vụ');
          $message = 'Đã cập nhật trạng thái booking thành công';
          break;
        case 'cancel':
          $result = Booking::updateStatus($id, 'cancelled', $notes ?: 'Booking bị hủy');
          $message = 'Đã hủy booking thành công';
          break;
        default:
          RestApi::responseError('Hành động không hợp lệ.');
          return;
      }

      if ($result) {
        RestApi::responseSuccess(true, $message, 200);
      } else {
        RestApi::responseError('Không thể cập nhật trạng thái booking.');
      }
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  /**
   * API lấy pets của customer
   */
  // public function getCustomerPets($customerId)
  // {
  //   try {
  //     RestApi::setHeaders();

  //     $customer = Account::findOneWhere([
  //       'user_id' => $customerId,
  //       'role' => UserRole::CUSTOMER
  //     ]);

  //     if (!$customer) {
  //       RestApi::responseError('Khách hàng không tồn tại.');
  //     }

  //     $pets = Pet::findWhere(['user_id' => $customerId]);

  //     RestApi::responseSuccess($pets, 'Lấy danh sách pet thành công', 200);
  //   } catch (\Throwable $th) {
  //     RestApi::responseError($th->getMessage());
  //   }
  // }
}
