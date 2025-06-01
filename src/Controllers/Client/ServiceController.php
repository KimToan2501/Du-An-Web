<?php

namespace App\controllers\client;

use App\Core\UserRole;
use App\models\Account;
use App\Models\Service;
use App\Models\Review;
use App\Models\ServiceType;

class ServiceController
{
    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $type = isset($_GET['type']) ? (int)$_GET['type'] : null;
        $perPage = 8;
        $title = 'Dịch vụ';
        $servicesTypes = ServiceType::all();

        if ($type) {
            $result = Service::paginateWhere(['service_type_id' => $type], $page, $perPage);
            $title = 'Dịch vụ - ' . ServiceType::find($type)->name;
        } else {
            $result = Service::paginate($page, $perPage);
        }

        // Lấy danh sách các loại dịch vụ
        $staffs = Account::findBy('role', UserRole::STAFF);

        $data = [
            'title' => $title,
            'metadata' => $result['data'],
            'pagination' => [
                'current' => $result['current_page'],
                'last' => $result['last_page'],
                'total' => $result['total']
            ],
            'staffs' => $staffs,
            'service_types' => $servicesTypes,
            'type' => $type,
        ];

        render_view('client/service/index', $data, 'client');
    }

    public function detail($id)
    {
        $service = Service::find($id);

        if (!isset($service)) {
            show_404('Không tìm thấy dịch vụ có ID là ' . $id);
        }

        // Lấy thông tin đánh giá
        $reviews = Review::findByServiceId($service->service_id);
        $totalReviews = Review::countReviewsByService($service->service_id);
        $averageRating = Review::getAverageRatingByService($service->service_id);
        $ratingDistribution = Review::getRatingDistribution($service->service_id);

        // Lấy hình ảnh dịch vụ
        $serviceImages = $service->service_images($service->service_id);

        $data = [
            'title' => $service->name,
            'breadcrumbs' => [
                ['text' => 'Trang chủ', 'url' => '/'],
                ['text' => 'Dịch vụ', 'url' => '/service'],
                ['text' => $service->name],
            ],
            'service' => $service,
            'reviews' => $reviews,
            'totalReviews' => $totalReviews,
            'averageRating' => $averageRating,
            'ratingDistribution' => $ratingDistribution,
            'serviceImages' => $serviceImages,
        ];

        render_view('client/service/detail_service', $data, 'client');
    }

    /**
     * API để lấy đánh giá theo dịch vụ (cho AJAX)
     */
    public function getReviews($serviceId)
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 5;
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest'; // newest, oldest, highest, lowest

        $service = Service::find($serviceId);
        if (!$service) {
            http_response_code(404);
            echo json_encode(['error' => 'Service not found']);
            return;
        }

        // Xây dựng query dựa trên sort
        $orderBy = 'created_at DESC';
        switch ($sortBy) {
            case 'oldest':
                $orderBy = 'created_at ASC';
                break;
            case 'highest':
                $orderBy = 'rating DESC, created_at DESC';
                break;
            case 'lowest':
                $orderBy = 'rating ASC, created_at DESC';
                break;
            default:
                $orderBy = 'created_at DESC';
        }

        $offset = ($page - 1) * $perPage;

        // Lấy reviews với phân trang
        $query = "SELECT r.*, u.username, u.full_name 
                  FROM reviews r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  WHERE r.service_id = :service_id 
                  ORDER BY {$orderBy} 
                  LIMIT :limit OFFSET :offset";

        $stmt = PDO()->prepare($query);
        $stmt->bindParam(':service_id', $serviceId);
        $stmt->bindParam(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        $reviews = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $reviews[] = $row;
        }

        // Đếm tổng số reviews
        $totalReviews = Review::countReviewsByService($serviceId);
        $totalPages = ceil($totalReviews / $perPage);

        $response = [
            'reviews' => $reviews,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalReviews,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ],
            'summary' => [
                'total_reviews' => $totalReviews,
                'average_rating' => Review::getAverageRatingByService($serviceId),
                'rating_distribution' => Review::getRatingDistribution($serviceId)
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Lấy dịch vụ liên quan
     */
    public function getRelatedServices($serviceId, $limit = 4)
    {
        $service = Service::find($serviceId);
        if (!$service) {
            return [];
        }

        // Lấy dịch vụ cùng loại hoặc giá tương đương
        $query = "SELECT * FROM services 
                  WHERE service_id != :service_id 
                  AND (service_type_id = :service_type_id 
                       OR ABS(price - :price) <= :price_range)
                  ORDER BY 
                    CASE WHEN service_type_id = :service_type_id THEN 0 ELSE 1 END,
                    ABS(price - :price)
                  LIMIT :limit";

        $priceRange = $service->price * 0.3; // 30% giá gốc

        $stmt = PDO()->prepare($query);
        $stmt->bindParam(':service_id', $serviceId);
        $stmt->bindParam(':service_type_id', $service->service_type_id);
        $stmt->bindParam(':price', $service->price);
        $stmt->bindParam(':price_range', $priceRange);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $relatedServices = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $relatedService = new Service();
            foreach ($row as $key => $value) {
                $relatedService->$key = $value;
            }
            $relatedServices[] = $relatedService;
        }

        return $relatedServices;
    }
}
