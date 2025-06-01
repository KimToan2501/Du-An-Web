<?php

namespace App\Controllers\Client;

use App\Core\Auth;
use App\Core\RestApi;
use App\Core\Uploader;
use App\Models\Pet;
use App\Models\Account;
use PDO;

class PetController
{
  /**
   * Hiển thị danh sách thú cưng
   */
  public function index()
  {
    $auth = Auth::getInstance();
    $user = $auth->user();

    // Get pagination parameters
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = isset($_GET['per_page']) ? max(1, min(50, (int)$_GET['per_page'])) : 10; // Default 10, max 50

    // Build where conditions
    $whereConditions = ['user_id' => $user['user_id']];
    $whereOperator = 'AND';

    $result = Pet::paginateWhere($whereConditions, $page, $perPage, $whereOperator, 'created_at', 'DESC');

    // Lấy thông tin owner cho mỗi pet (nếu cần)
    foreach ($result['data'] as $pet) {
      $pet->owner = Account::find($pet->user_id);
    }

    $data = [
      'title' => 'Thú Cưng Của Bạn',
      'metadata' => $result['data'],
      'pagination' => [
        'current' => $result['current_page'],
        'last' => $result['last_page'],
        'total' => $result['total'],
        'per_page' => $perPage,
        'from' => ($result['current_page'] - 1) * $perPage + 1,
        'to' => min($result['current_page'] * $perPage, $result['total'])
      ],
    ];

    render_view('client/user/pet/index', $data, 'client');
  }

  /**
   * Hiển thị form thêm thú cưng mới
   */
  public function create()
  {
    render_view('/client/user/pet/create', [
      'title' => 'Thêm Thú Cưng Mới'
    ], 'client');
  }

  /**
   * Xử lý thêm thú cưng mới
   */
  public function store()
  {
    RestApi::setHeaders();
    $auth = Auth::getInstance();
    $user = $auth->user();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      RestApi::responseError('Yêu cầu không hợp lệ!');
    }

    $data = $this->validatePetData($_POST);

    if (!empty($data['errors'])) {
      RestApi::responseError($data['errors']);
    }

    // Xử lý upload avatar nếu có
    if (isset($_FILES['avatar'])) {
      $avatarUrl = $this->uploadAvatar($_FILES['avatar']);
      if ($avatarUrl) {
        $data['pet_data']['avatar_url'] = $avatarUrl;
      }
    }

    $data['pet_data']['user_id'] = $user['user_id'];
    $data['pet_data']['created_at'] = date('Y-m-d H:i:s');
    $data['pet_data']['updated_at'] = date('Y-m-d H:i:s');

    $pet = Pet::create($data['pet_data']);

    if ($pet) {
      RestApi::responseSuccess($pet, 'Thú cưng đã được thêm thành công!');
    } else {
      RestApi::responseError('Có lỗi xảy ra khi thêm thú cưng!');
    }
  }

  /**
   * Hiển thị chi tiết thú cưng
   */
  public function show($id)
  {
    $auth = Auth::getInstance();
    $user = $auth->user();

    $pet = Pet::findOneWhere(['pet_id' => $id, 'user_id' => $user['user_id']]);

    if (!$pet) {
      $_SESSION['error'] = 'Không tìm thấy thú cưng!';
      redirect('/user/pets');
      return;
    }

    $pet->owner = Account::find($pet->user_id);

    render_view('client/user/pet/show', [
      'pet' => $pet,
      'title' => 'Chi Tiết Thú Cưng - ' . $pet->name
    ], 'client');
  }

  /**
   * Hiển thị form chỉnh sửa thú cưng
   */
  public function edit($id)
  {
    $auth = Auth::getInstance();
    $user = $auth->user();

    $pet = Pet::findOneWhere(['pet_id' => $id, 'user_id' => $user['user_id']]);

    if (!$pet) {
      show_404('Không tìm thấy thú cưng!');
    }

    render_view('client/user/pet/edit', [
      'pet' => $pet,
      'title' => 'Chỉnh Sửa Thú Cưng - ' . $pet->name
    ], 'client');
  }

  /**
   * Xử lý cập nhật thông tin thú cưng
   */
  public function update($id)
  {
    RestApi::setHeaders();
    $auth = Auth::getInstance();
    $user = $auth->user();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      RestApi::responseError('Yêu cầu không hợp lệ!');
    }

    $pet = Pet::findOneWhere(['pet_id' => $id, 'user_id' => $user['user_id']]);
    if (!$pet) {
      RestApi::responseError('Không tìm thấy thú cưng!');
    }

    $data = $this->validatePetData($_POST, $id);

    if (!empty($data['errors'])) {
      RestApi::responseError($data['errors']);
    }

    // Xử lý upload avatar mới nếu có
    if (isset($_FILES['avatar'])) {
      $uploadError = Uploader::checkUploadError($_FILES['avatar']);

      if ($uploadError) {
        RestApi::responseError($uploadError);
        return;
      }

      $avatarUrl = $this->uploadAvatar($_FILES['avatar']);
      if ($avatarUrl) {
        // Xóa avatar cũ nếu có
        if ($pet->avatar_url && file_exists('public/' . $pet->avatar_url)) {
          Uploader::deleteFile($pet->avatar_url);
        }
        $data['pet_data']['avatar_url'] = $avatarUrl;
      }
    }

    $updated = Pet::update($id, $data['pet_data']);

    if ($updated) {
      RestApi::responseSuccess($updated, 'Cập nhật thông tin thú cưng thành công!');
    } else {
      RestApi::responseError('Có lỗi xảy ra khi cập nhật thông tin!');
    }
  }

  /**
   * Xóa thú cưng (kiểm tra ràng buộc trước)
   */
  public function destroy($id)
  {
    RestApi::setHeaders();
    $auth = Auth::getInstance();
    $user = $auth->user();

    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
      RestApi::responseError('Method not allowed');
    }

    $pet = Pet::findOneWhere(['pet_id' => $id, 'user_id' => $user['user_id']]);
    if (!$pet) {
      RestApi::responseError('Không tìm thấy thú cưng!');
    }

    // Kiểm tra ràng buộc - xem pet có booking nào không
    $bookings = Pet::query(
      "SELECT COUNT(*) as count FROM booking_pets WHERE pet_id = :pet_id",
      ['pet_id' => $id],
      PDO::FETCH_ASSOC
    );

    if ($bookings && $bookings[0]['count'] > 0) {
      RestApi::responseError('Không thể xóa thú cưng này vì đã có lịch đặt dịch vụ!');
    }

    // Xóa avatar nếu có
    if ($pet->avatar_url && file_exists('public/' . $pet->avatar_url)) {
      Uploader::deleteFile($pet->avatar_url);
    }

    $deleted = Pet::delete($id);

    if ($deleted) {
      RestApi::responseSuccess(null, 'Xóa thú cưng thành công!');
    } else {
      RestApi::responseError('Có lỗi xảy ra khi xóa thú cưng!');
    }
  }

  /**
   * Bulk actions for pets
   */
  public function bulkAction()
  {
    RestApi::setHeaders();
    $auth = Auth::getInstance();
    $user = $auth->user();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      RestApi::responseError('Method not allowed');
    }

    $action = $_POST['action'] ?? '';
    $petIds = $_POST['pet_ids'] ?? [];

    if (empty($action) || empty($petIds) || !is_array($petIds)) {
      RestApi::responseError('Dữ liệu không hợp lệ!');
    }

    // Validate pet ownership
    $validPetIds = [];
    foreach ($petIds as $petId) {
      $pet = Pet::findWhere(['id' => $petId, 'user_id' => $user['user_id']]);
      if ($pet) {
        $validPetIds[] = $petId;
      }
    }

    if (empty($validPetIds)) {
      RestApi::responseError('Không tìm thấy thú cưng hợp lệ!');
    }

    switch ($action) {
      case 'delete':
        $deletedCount = 0;
        foreach ($validPetIds as $petId) {
          // Check for bookings
          $bookings = Pet::query(
            "SELECT COUNT(*) as count FROM booking_pets WHERE pet_id = :pet_id",
            ['pet_id' => $petId]
          );

          if (!$bookings || $bookings[0]->count == 0) {
            $pet = Pet::find($petId);
            Uploader::deleteFile($pet->avatar_url);
            if (Pet::delete($petId)) {
              $deletedCount++;
            }
          }
        }

        if ($deletedCount > 0) {
          RestApi::responseSuccess(null, "Đã xóa {$deletedCount} thú cưng thành công!");
        } else {
          RestApi::responseError('Không thể xóa thú cưng nào (có thể đã có lịch đặt dịch vụ)!');
        }
        break;

      default:
        RestApi::responseError('Hành động không hợp lệ!');
    }
  }

  /**
   * Export pets data
   */
  public function export()
  {
    $auth = Auth::getInstance();
    $user = $auth->user();

    $pets = Pet::findWhere(['user_id' => $user['user_id']], 'created_at', 'DESC');

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="my_pets_' . date('Y-m-d') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // CSV headers
    fputcsv($output, [
      'Tên',
      'Loại',
      'Giống',
      'Tuổi',
      'Đơn vị tuổi',
      'Giới tính',
      'Màu sắc',
      'Cân nặng (kg)',
      'Kích thước',
      'Ghi chú y tế',
      'Ghi chú hành vi',
      'Ngày tạo'
    ]);

    // CSV data
    foreach ($pets as $pet) {
      fputcsv($output, [
        $pet->name,
        $pet->getTypeName($pet->type),
        $pet->breed,
        $pet->age,
        $pet->getAgeUnitName($pet->age_unit),
        $pet->getGenderName($pet->gender),
        $pet->color,
        $pet->weight,
        $pet->getSizeName($pet->size),
        $pet->medical_notes,
        $pet->behavioral_notes,
        $pet->created_at
      ]);
    }

    fclose($output);
    exit;
  }

  /**
   * Get pet statistics
   */
  public function getStats()
  {
    RestApi::setHeaders();
    $auth = Auth::getInstance();
    $user = $auth->user();

    // Total pets
    $totalPets = Pet::query(
      "SELECT COUNT(*) as count FROM pets WHERE user_id = :user_id",
      ['user_id' => $user['user_id']]
    )[0]->count ?? 0;

    // Pets by type
    $petsByType = Pet::query(
      "SELECT type, COUNT(*) as count FROM pets WHERE user_id = :user_id GROUP BY type",
      ['user_id' => $user['user_id']]
    );

    // Recent pets (last 30 days)
    $recentPets = Pet::query(
      "SELECT COUNT(*) as count FROM pets WHERE user_id = :user_id AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
      ['user_id' => $user['user_id']]
    )[0]->count ?? 0;

    // Average age
    $avgAge = Pet::query(
      "SELECT AVG(age) as avg_age FROM pets WHERE user_id = :user_id AND age IS NOT NULL",
      ['user_id' => $user['user_id']]
    )[0]->avg_age ?? 0;

    RestApi::responseSuccess([
      'total_pets' => $totalPets,
      'pets_by_type' => $petsByType,
      'recent_pets' => $recentPets,
      'average_age' => round($avgAge, 1)
    ]);
  }

  /**
   * Validate dữ liệu pet
   */
  private function validatePetData($data, $excludeId = null)
  {
    $errors = [];
    $petData = [];

    // Validate required fields
    if (empty($data['name'])) {
      $errors['name'] = 'Tên thú cưng là bắt buộc';
    } else {
      $petData['name'] = trim($data['name']);
    }

    if (empty($data['type'])) {
      $errors['type'] = 'Loại thú cưng là bắt buộc';
    } else {
      $validTypes = ['dog', 'cat', 'bird', 'rabbit', 'hamster', 'other'];
      if (!in_array($data['type'], $validTypes)) {
        $errors['type'] = 'Loại thú cưng không hợp lệ';
      } else {
        $petData['type'] = $data['type'];
      }
    }

    // Validate optional fields
    if (!empty($data['breed'])) {
      $petData['breed'] = trim($data['breed']);
    }

    if (!empty($data['age'])) {
      $age = (int)$data['age'];
      if ($age < 0 || $age > 50) {
        $errors['age'] = 'Tuổi phải từ 0 đến 50';
      } else {
        $petData['age'] = $age;
      }
    }

    if (!empty($data['age_unit'])) {
      $validUnits = ['months', 'years', 'weeks', 'days'];
      if (!in_array($data['age_unit'], $validUnits)) {
        $errors['age_unit'] = 'Đơn vị tuổi không hợp lệ';
      } else {
        $petData['age_unit'] = $data['age_unit'];
      }
    }

    if (!empty($data['size'])) {
      $validSizes = ['tiny', 'small', 'medium', 'large', 'extra_large'];
      if (!in_array($data['size'], $validSizes)) {
        $errors['size'] = 'Kích thước không hợp lệ';
      } else {
        $petData['size'] = $data['size'];
      }
    }

    if (!empty($data['weight'])) {
      $weight = (float)$data['weight'];
      if ($weight < 0 || $weight > 999.99) {
        $errors['weight'] = 'Cân nặng không hợp lệ';
      } else {
        $petData['weight'] = $weight;
      }
    }

    if (!empty($data['color'])) {
      $petData['color'] = trim($data['color']);
    }

    if (!empty($data['gender'])) {
      $validGenders = ['male', 'female', 'unknown'];
      if (!in_array($data['gender'], $validGenders)) {
        $errors['gender'] = 'Giới tính không hợp lệ';
      } else {
        $petData['gender'] = $data['gender'];
      }
    }

    if (!empty($data['medical_notes'])) {
      $petData['medical_notes'] = trim($data['medical_notes']);
    }

    if (!empty($data['behavioral_notes'])) {
      $petData['behavioral_notes'] = trim($data['behavioral_notes']);
    }

    return [
      'errors' => $errors,
      'pet_data' => $petData
    ];
  }

  /**
   * Upload avatar cho pet
   */
  private function uploadAvatar($file)
  {
    return Uploader::uploadImage($file, 'public/uploads/pet/');
  }
}
