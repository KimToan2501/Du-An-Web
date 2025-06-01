<?php

namespace App\Controllers\Admin;

use App\Models\Booking;
use PDO;

class DashboardController
{
  public function index()
  {
    // Lấy thống kê tổng quan
    $stats = $this->getDashboardStats();

    // Lấy doanh thu theo thời gian
    $revenueStats = $this->getRevenueStats();

    // Lấy thống kê dịch vụ
    $serviceStats = $this->getServiceStats();

    // Lấy booking gần nhất
    $recentBookings = Booking::getRecentBookings(5);

    $data = [
      'stats' => $stats,
      'revenueStats' => $revenueStats,
      'serviceStats' => $serviceStats,
      'recentBookings' => $recentBookings,
      'title' => 'Dashboard',
      'breadcrumbs' => [
        ['text' => 'Trang chủ', 'url' => '/admin/dashboard'],
        ['text' => 'Dashboard']
      ],
    ];

    render_view('admin/dashboard/index', $data, 'admin');
  }

  /**
   * Lấy thống kê tổng quan dashboard
   */
  private function getDashboardStats()
  {
    $stats = [];

    // Tổng khách hàng
    $query = "SELECT COUNT(*) as total FROM accounts WHERE role = 'customer'";
    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $stats['total_customers'] = (int)$stmt->fetchColumn();

    // Số lịch hẹn
    $query = "SELECT COUNT(*) as total FROM bookings";
    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $stats['total_bookings'] = (int)$stmt->fetchColumn();

    // Đang sử dụng dịch vụ (in_progress)
    $query = "SELECT COUNT(*) as total FROM bookings WHERE status = 'in_progress'";
    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $stats['in_progress_bookings'] = (int)$stmt->fetchColumn();

    // Đã sử dụng dịch vụ (completed)
    $query = "SELECT COUNT(*) as total FROM bookings WHERE status = 'completed'";
    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $stats['completed_bookings'] = (int)$stmt->fetchColumn();

    return $stats;
  }

  /**
   * Lấy thống kê doanh thu
   */
  private function getRevenueStats($period = 'week')
  {
    $stats = [];

    // Doanh thu tổng
    $query = "SELECT SUM(total_amount) as total_revenue FROM bookings WHERE payment_status = 'paid'";
    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $stats['total_revenue'] = (float)$stmt->fetchColumn() ?: 0;

    // Doanh thu theo kỳ
    switch ($period) {
      case 'week':
        $dateCondition = "DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
      case 'month':
        $dateCondition = "MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        break;
      case 'year':
        $dateCondition = "YEAR(created_at) = YEAR(CURDATE())";
        break;
      default:
        $dateCondition = "DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    }

    $query = "SELECT SUM(total_amount) as period_revenue FROM bookings 
                  WHERE payment_status = 'paid' AND {$dateCondition}";
    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $stats['period_revenue'] = (float)$stmt->fetchColumn() ?: 0;

    // Tính phần trăm so với tổng
    $stats['revenue_percentage'] = $stats['total_revenue'] > 0
      ? round(($stats['period_revenue'] / $stats['total_revenue']) * 100)
      : 0;

    return $stats;
  }

  /**
   * Lấy thống kê dịch vụ
   */
  private function getServiceStats()
  {
    $stats = [];

    // Tổng số dịch vụ
    $query = "SELECT COUNT(*) as total FROM services";
    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $stats['total_services'] = (int)$stmt->fetchColumn();

    // Dịch vụ được sử dụng (có trong booking_details)
    $query = "SELECT COUNT(DISTINCT service_id) as used_services FROM booking_details";
    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $stats['used_services'] = (int)$stmt->fetchColumn();

    // Tính phần trăm dịch vụ được sử dụng
    $stats['service_usage_percentage'] = $stats['total_services'] > 0
      ? round(($stats['used_services'] / $stats['total_services']) * 100)
      : 0;

    return $stats;
  }

  /**
   * API endpoint để lấy dữ liệu biểu đồ doanh thu
   */
  public function getRevenueChartData()
  {
    $period = $_GET['period'] ?? 'week';

    switch ($period) {
      case 'week':
        $query = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue 
                         FROM bookings 
                         WHERE payment_status = 'paid' 
                         AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                         GROUP BY DATE(created_at)
                         ORDER BY date";
        break;

      case 'month':
        $query = "SELECT DAY(created_at) as day, SUM(total_amount) as revenue 
                         FROM bookings 
                         WHERE payment_status = 'paid' 
                         AND MONTH(created_at) = MONTH(CURDATE()) 
                         AND YEAR(created_at) = YEAR(CURDATE())
                         GROUP BY DAY(created_at)
                         ORDER BY day";
        break;

      case 'year':
        $query = "SELECT MONTH(created_at) as month, SUM(total_amount) as revenue 
                         FROM bookings 
                         WHERE payment_status = 'paid' 
                         AND YEAR(created_at) = YEAR(CURDATE())
                         GROUP BY MONTH(created_at)
                         ORDER BY month";
        break;
    }

    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($data);
  }

  /**
   * API endpoint để lấy dữ liệu biểu đồ dịch vụ
   */
  public function getServiceChartData()
  {
    $query = "SELECT s.name, COUNT(bd.service_id) as usage_count 
                 FROM services s
                 LEFT JOIN booking_details bd ON s.service_id = bd.service_id
                 LEFT JOIN bookings b ON bd.booking_id = b.id
                 WHERE b.status = 'completed'
                 GROUP BY s.service_id, s.name
                 ORDER BY usage_count DESC
                 LIMIT 10";

    $stmt = PDO()->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($data);
  }
}
