<?php

namespace App\Controllers\Client;

use App\Core\Auth;
use App\Core\RestApi;
use App\models\Account;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Service;

class ReviewController
{
  /**
   * Lưu review mới
   */
  public function store()
  {
    try {
      RestApi::setHeaders();
      $body = RestApi::getBody();
      $auth = Auth::getInstance();
      $user = $auth->user();

      $userId = $user['user_id'];

      $bookingId = $body['booking_id'];
      $isAnonymous = isset($body['is_anonymous']) ? 1 : 0;
      $services = $body['services'];

      // Validation
      if (empty($services)) {
        RestApi::responseError('Vui lòng đánh giá các dịch vụ');
      }

      // Kiểm tra booking
      $booking = Booking::find($bookingId);
      if (!$booking || $booking->user_id !== $userId) {
        RestApi::responseError('Booking không tồn tại hoặc không thuộc về bạn');
      }

      $staffId = $booking->staff_id;

      foreach ($services as $service) {
        $comment = $service['comment'];
        $rating = $service['rating'];
        $serviceId = $service['service_id'];

        // Kiểm tra service
        $existingService = Service::find($serviceId);
        if (!$existingService) {
          RestApi::responseError('Dịch vụ không tồn tại');
        }

        // Kiểm tra đã review chưa
        $existingReview = Review::findOneWhere([
          'booking_id' => $bookingId,
          'user_id' => $userId,
          'service_id' => $serviceId
        ]);

        if ($existingReview) {
          RestApi::responseError("Bạn đã đánh giá dịch vụ {$existingService->name} này rồi");
        }

        $newReview = new Review();
        $newReview->booking_id = $bookingId;
        $newReview->user_id = $userId;
        $newReview->service_id = $serviceId;
        $newReview->rating = $rating;
        $newReview->comment = $comment;
        $newReview->is_anonymous = $isAnonymous;
        $newReview->created_at = date('Y-m-d H:i:s');
        $newReview->updated_at = date('Y-m-d H:i:s');

        $newReview->save();
      } 

      // Cập nhật rating của staff sau khi lưu tất cả reviews
      Account::updateStaffRating($staffId);

      RestApi::responseSuccess(true, 'Bạn đã đánh giá thành công', 201);
    } catch (\Throwable $th) {
      RestApi::responseError($th->getMessage());
    }
  }
}
