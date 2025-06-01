<?php

namespace App\Controllers\Admin;

use App\Core\RestApi;
use App\Core\UserRole;
use App\Models\Account;
use App\Models\StaffSchedule;
use App\Models\TimeSlot;

class StaffScheduleController
{
  public function index($staffId)
  {
    $staff = Account::findOneBy('user_id', $staffId);
    if (!isset($staff) || $staff->role !== UserRole::STAFF) {
      redirect(base_url('/404'));
    }

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $searching = isset($_GET['q']) ? (string) $_GET['q'] : '';
    $perPage = 10;

    // Thực hiện tìm kiếm nếu có từ khóa
    if (!empty($searching)) {
      $result = $this->searchStaffSchedules($staffId, $searching, $page, $perPage);
    } else {
      $result = StaffSchedule::paginateWhere([
        'account_id' => $staffId,
      ], $page, $perPage);
    }

    $title = 'Quản lý lịch làm việc nhân viên - ' . $staff->name;

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý nhân viên', 'url' => '/admin/staff'],
        ['text' => $title],
      ],
      'metadata' => $result['data'],
      'pagination' => [
        'current' => $result['current_page'],
        'last' => $result['last_page'],
        'total' => $result['total']
      ],
      'searching' => $searching,
      'staff' => $staff
    ];

    render_view('admin/staff/schedule/index', $data, 'admin');
  }

  /**
   * Tìm kiếm lịch làm việc theo thời gian bắt đầu và kết thúc
   */
  private function searchStaffSchedules($staffId, $searchTerm, $page, $perPage)
  {
    return StaffSchedule::searchByTimeSlot($staffId, $searchTerm, $page, $perPage);
  }

  public function showAdd($staffId)
  {
    $staff = Account::findOneBy('user_id', $staffId);

    if (!isset($staff) || $staff->role !== UserRole::STAFF) {
      redirect(base_url('/404'));
    }

    $timeSlots = TimeSlot::all();

    $title = 'Thêm lịch lịch làm việc nhân viên ' . $staff->name;

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý nhân viên', 'url' => '/admin/staff'],
        ['text' => 'Quản lý lịch làm việc nhân viên ' . $staff->name, 'url' => '/admin/staff/schedule/' . $staffId],
        ['text' => $title],
      ],
      'metadata' => $staff,
      'timeSlots' => $timeSlots
    ];

    render_view('admin/staff/schedule/add', $data, 'admin');
  }

  public function add($staffId)
  {
    try {
      RestApi::setHeaders();

      $staff = Account::findOneBy('user_id', $staffId);

      if (!isset($staff) || $staff->role !== UserRole::STAFF) {
        RestApi::responseError('Nhân viên không tồn tại hoặc không có quyền.');
      }

      $date = $_POST['date'];
      $time_slot_ids = explode(',', $_POST['time_slot_ids']); // Lấy mảng các time_slot_id


      if (empty($date) || empty($time_slot_ids)) {
        RestApi::responseError('Vui lòng cung cấp đầy đủ ngày và ca làm việc.');
      }

      $errors = [];
      foreach ($time_slot_ids as $time_slot_id) {
        // Kiểm tra xem lịch làm việc đã tồn tại chưa cho từng ca
        $existingSchedule = StaffSchedule::findOneWhere(["account_id" => $staffId, "date" => $date, "time_slot_id" => $time_slot_id]);

        if ($existingSchedule) {
          $timeSlot = TimeSlot::findOneBy('time_slot_id', $time_slot_id);

          if (!$timeSlot) {
            $errors[] = 'Ca làm việc không tồn tại.';
            continue;
          }

          $errors[] = 'Ca làm việc ' . $timeSlot->start_time . ' - ' . $timeSlot->end_time . ' đã tồn tại cho nhân viên vào ngày này.';
        } else {
          StaffSchedule::create([
            'account_id' => $staffId,
            'date' => $date,
            'time_slot_id' => $time_slot_id
          ]);
        }
      }

      if (!empty($errors)) {
        RestApi::responseError(implode('<br>', $errors));
      } else {
        RestApi::responseSuccess(true, 'Đã thêm lịch làm việc thành công', 201);
      }
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }

  public function showUpdate($staffId, $date)
  {
    $staff = Account::findOneBy('user_id', $staffId);

    if (!isset($staff) || $staff->role !== UserRole::STAFF) {
      redirect(base_url('/404'));
    }

    $staffSchedule = StaffSchedule::findWhere(['account_id' => $staffId, 'date' => $date]);

    if (!isset($staffSchedule) || count($staffSchedule) === 0) {
      redirect(base_url('/404'));
    }

    $title = 'Cập nhật lịch làm việc của nhân viên - ' . $staff->name;

    $initialData = [];

    foreach ($staffSchedule as $item) {
      $newItem = [];
      $newItem['staff_schedule_id'] = $item->staff_schedule_id;
      $newItem['account_id'] = $item->account_id;
      $newItem['date'] = $item->date;
      $newItem['time_slot_id'] = $item->time_slot_id;
      $newItem['is_available'] = $item->is_available;

      $timeSlot = TimeSlot::findOneBy('time_slot_id', $item->time_slot_id);
      if ($timeSlot) {
        $newItem['time_slot'] = $timeSlot;
        $initialData[] = $newItem;
      }
    }

    $data = [
      'title' => $title,
      'breadcrumbs' => [
        ['text' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['text' => 'Quản lý lịch làm việc nhân viên', 'url' => '/admin/staff'],
        ['text' => 'Quản lý lịch làm việc nhân viên ' . $staff->name, 'url' => '/admin/staff/schedule/' . $staffId],
        ['text' => $title],
      ],
      'staff' => $staff,
      'metadata' => $staffSchedule,
      'initialData' => $initialData, // Thêm biến này để truyền dữ liệu ban đầu vào view 'admin/staff/schedule/update
      'timeSlots' => TimeSlot::all(),
      'date' => $date,
    ];

    render_view('admin/staff/schedule/update', $data, 'admin');
  }

  public function edit($staffId, $date = null)
  {
    try {
      RestApi::setHeaders();

      $staff = Account::findOneBy('user_id', $staffId);

      if (!isset($staff) || $staff->role !== UserRole::STAFF) {
        RestApi::responseError('Nhân viên không tồn tại hoặc không có quyền.');
      }

      $requestDate = $date ?? $_POST['date'];
      $timeSlotIds = isset($_POST['time_slot_ids']) ? explode(',', $_POST['time_slot_ids']) : [];
      $scheduleIds = isset($_POST['schedule_ids']) ? explode(',', $_POST['schedule_ids']) : [];
      $removedScheduleIds = isset($_POST['removed_schedule_ids']) ? explode(',', $_POST['removed_schedule_ids']) : [];

      // Validate input data
      if (empty($requestDate)) {
        RestApi::responseError('Vui lòng cung cấp ngày làm việc.');
      }

      if (empty($timeSlotIds)) {
        RestApi::responseError('Vui lòng chọn ít nhất một ca làm việc.');
      }

      // Filter valid IDs
      $timeSlotIds = array_filter($timeSlotIds, function ($id) {
        return is_numeric($id) && $id > 0;
      });
      $scheduleIds = array_filter($scheduleIds, function ($id) {
        return is_numeric($id) && $id > 0;
      });
      $removedScheduleIds = array_filter($removedScheduleIds, function ($id) {
        return is_numeric($id) && $id > 0;
      });

      $errors = [];
      $successCount = 0;

      // Start transaction (if your framework supports it)
      // DB::beginTransaction();

      try {
        // 1. Remove schedules that are no longer selected
        if (!empty($removedScheduleIds)) {
          foreach ($removedScheduleIds as $scheduleId) {
            $schedule = StaffSchedule::findOneBy('staff_schedule_id', $scheduleId);
            if ($schedule && $schedule->account_id == $staffId) {
              StaffSchedule::delete($scheduleId);
              $successCount++;
            }
          }
        }

        // 2. Get existing schedules for this staff and date
        $existingSchedules = StaffSchedule::findWhere(['account_id' => $staffId, 'date' => $requestDate]);
        $existingTimeSlotIds = [];
        foreach ($existingSchedules as $existing) {
          $existingTimeSlotIds[] = $existing->time_slot_id;
        }

        // 3. Process each selected time slot
        foreach ($timeSlotIds as $timeSlotId) {
          $timeSlotId = (int)$timeSlotId;

          // Validate time slot exists
          $timeSlot = TimeSlot::findOneBy('time_slot_id', $timeSlotId);
          if (!$timeSlot) {
            $errors[] = "Ca làm việc ID {$timeSlotId} không tồn tại.";
            continue;
          }

          // Check if this time slot already exists for this staff and date
          $existingSchedule = null;
          foreach ($existingSchedules as $existing) {
            if ($existing->time_slot_id == $timeSlotId) {
              $existingSchedule = $existing;
              break;
            }
          }

          if ($existingSchedule) {
            // Schedule already exists, just update if needed
            // You can add additional update logic here if required
            $successCount++;
          } else {
            // Create new schedule
            $newSchedule = StaffSchedule::create([
              'account_id' => $staffId,
              'date' => $requestDate,
              'time_slot_id' => $timeSlotId,
              'is_available' => 1 // Default to available
            ]);

            if ($newSchedule) {
              $successCount++;
            } else {
              $errors[] = "Không thể tạo lịch làm việc cho ca {$timeSlot->start_time} - {$timeSlot->end_time}.";
            }
          }
        }

        if (!empty($errors)) {
          RestApi::responseError('Một số lỗi đã xảy ra:<br>' . implode('<br>', $errors));
        } else {
          $message = "Đã cập nhật lịch làm việc thành công. ";
          $message .= "Tổng số ca làm việc được xử lý: {$successCount}";
          RestApi::responseSuccess(true, $message, 200);
        }
      } catch (\Exception $e) {
        // Rollback transaction
        // DB::rollback();
        throw $e;
      }
    } catch (\Throwable $th) {
      RestApi::responseError('Lỗi hệ thống: ' . $th->getMessage());
    }
  }

  public function delete($staffId, $staffScheduleId)
  {
    try {
      RestApi::setHeaders();

      $staff = Account::findOneBy('user_id', $staffId);

      if (!isset($staff) || $staff->role !== UserRole::STAFF) {
        RestApi::responseError('Nhân viên không tồn tại');
      }

      $staff = StaffSchedule::findOneBy('staff_schedule_id', $staffScheduleId);
      if (!isset($staff)) {
        RestApi::responseError('Lịch làm việc nhân viên không tồn tại');
      }

      StaffSchedule::delete($staffScheduleId);

      RestApi::responseSuccess(true, 'Đã xoá lịch làm việc nhân viên thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }
}
