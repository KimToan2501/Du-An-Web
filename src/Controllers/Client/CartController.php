<?php

namespace App\controllers\client;

use App\Core\Auth;
use App\Core\RestApi;
use App\Models\Account;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\BookingPet;
use App\Models\BookingStaffSchedule;
use App\Models\Pet;
use App\Models\Service;
use App\Models\StaffSchedule;
use App\Models\VnPayTransactions;
use Exception;

class CartController
{

    public function index()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $cart = $_SESSION['cart'];

        foreach ($cart as $id => $item) {
            $service = Service::find($id);

            if ($service) {
                $price_new = $service->price_new();
                $image = $service->get_first_image($id);
                $service = $service->toArray();

                $payload = [
                    ...$item,
                    ...$service,
                    'price_new' => $price_new,
                    'image' => $image
                ];

                $cart[$id] = $payload;
            }
        }

        // dd($cart);

        $title = 'Giỏ hàng';

        $data = [
            'cart' => $cart,
            'title' => $title
        ];

        render_view('client/cart/index', $data, 'client');
    }

    private function getBookingInfo()
    {
        if (!isset($_SESSION['booking_info'])) {
            return [];
        }

        return  $_SESSION['booking_info'];
    }

    private function getSelectedServices($services)
    {
        $selectedServices = [];
        $cart = !isset($_SESSION['cart']) ? [] : $_SESSION['cart'];

        foreach ($services as $id) {
            foreach ($cart as $itemId => $item) {
                if ($itemId == $id) {
                    $service = Service::find($itemId);

                    // Tính giá sau discount
                    $originalPrice = $service->price;
                    $discountPercent = $service->discount_percent ?? 0;
                    $price_new = $originalPrice;

                    if ($discountPercent > 0) {
                        $discountAmount = ($originalPrice * $discountPercent) / 100;
                        $price_new = $originalPrice - $discountAmount;
                    }

                    $image = $service->get_first_image($itemId);
                    $service = $service->toArray();

                    $selectedServices[] = [
                        ...$service,
                        'quantity' => $item['quantity'],
                        'price_new' => $price_new,
                        'image' => $image
                    ];
                }
            }
        }

        return $selectedServices;
    }

    public function info()
    {
        if (!isset($_SESSION['cart']) || empty($this->getBookingInfo())) {
            $_SESSION['cart'] = [];
            redirect(base_url('/cart'));
        }

        $auth = Auth::getInstance();
        $user = $auth->user();

        $bookingInfo = $this->getBookingInfo();
        $selectedServices = $this->getSelectedServices($bookingInfo['selected_services']);
        $pets = Pet::getMyPets($user['user_id']);

        $data = [
            'title' => 'Thông tin thanh toán',
            'selected_services' => $selectedServices,
            'booking_info' => $bookingInfo,
            'pets' => $pets
        ];

        render_view('client/cart/info', $data, 'client');
    }

    public function staff()
    {
        if (!isset($_SESSION['cart']) || empty($this->getBookingInfo())) {
            $_SESSION['cart'] = [];
            redirect(base_url('/cart'));
        }

        $bookingInfo = $this->getBookingInfo();
        $selectedServices = $this->getSelectedServices($bookingInfo['selected_services']);
        $dateSelected = $bookingInfo['customer_info']['selected_date'];

        $staffSchedules = StaffSchedule::getScheduleByDate($dateSelected);
        // Filter only available schedules
        $availableSchedules = array_filter($staffSchedules, function ($schedule) {
            return $schedule->is_available == 1;
        });

        $data = [
            'title' => 'Chọn nhân viên làm dịch vụ',
            'selected_services' => $selectedServices,
            'booking_info' => $bookingInfo,
            'staff_schedules' => $availableSchedules,
            'dateSelected' => $dateSelected,
        ];

        render_view('client/cart/staff', $data, 'client');
    }

    public function finish()
    {
        if (!isset($_SESSION['cart']) || empty($this->getBookingInfo())) {
            $_SESSION['cart'] = [];
            redirect(base_url('/cart'));
        }

        $bookingInfo = $this->getBookingInfo();
        $selectedServices = $this->getSelectedServices($bookingInfo['selected_services']);
        $staffInfo = $bookingInfo['staff_info'];
        $staffId = $staffInfo['staff_id'];
        $customerInfo = $bookingInfo['customer_info'];
        $pets = [];
        $staffSelected = Account::findOneBy('user_id', $staffId);

        foreach ($customerInfo['selected_pets'] as $petId) {
            $pet = Pet::find($petId);
            if ($pet) {
                $pets[] = $pet;
            }
        }

        $data = [
            'title' => 'Thông tin thanh toán',
            'selected_services' => $selectedServices,
            'booking_info' => $bookingInfo,
            'pets' => $pets,
            'staff_info' => $staffInfo,
            'customer_info' => $customerInfo,
            'staff_selected' => $staffSelected,
        ];

        render_view('client/cart/finish', $data, 'client');
    }

    public function confirmBooking()
    {
        RestApi::setHeaders();

        if (!isset($_SESSION['cart']) || empty($this->getBookingInfo())) {
            RestApi::responseError('Giỏ hàng trống');
        }

        $pdo = PDO();

        try {
            // Bắt đầu transaction
            $pdo->beginTransaction();

            $auth = Auth::getInstance();
            $user = $auth->user();

            $userId = $user['user_id'];
            $bookingInfo = $this->getBookingInfo();
            $selectedServices = $this->getSelectedServices($bookingInfo['selected_services']);
            $staffInfo = $bookingInfo['staff_info'];
            $staffId = $staffInfo['staff_id'];
            $customerInfo = $bookingInfo['customer_info'];
            $bookingDate = $customerInfo['selected_date'];

            // Tính toán các thông số
            $total_services = 0;
            $total_duration = 0;
            $total_pets = count($customerInfo['selected_pets']);
            $subtotal = 0;

            foreach ($selectedServices as $service) {
                $total_services += $service['quantity'];
                $total_duration += $service['duration'] * $service['quantity'];

                // Tính giá gốc trước khi áp dụng discount
                $originalPrice = $service['price']; // Giá gốc từ database
                $serviceDiscountPercent = $service['discount_percent'] ?? 0; // Discount của service

                // Tính giá sau discount của service (price_new)
                $discountedPrice = $originalPrice;
                if ($serviceDiscountPercent > 0) {
                    $discountAmount = ($originalPrice * $serviceDiscountPercent) / 100;
                    $discountedPrice = $originalPrice - $discountAmount;
                }

                // Cộng vào subtotal
                $subtotal += $discountedPrice * $service['quantity'];
            }

            // Tính toán giảm giá tổng đơn hàng (discount code)
            $discount_amount = 0;
            $discount_percent = $bookingInfo['discount_percent'] ?? 0;
            if ($discount_percent > 0) {
                $discount_amount = ($subtotal * $discount_percent) / 100;
            }

            $total_amount = $subtotal - $discount_amount;

            // Generate booking code
            $bookingCode = Booking::generateBookingCode();

            // 1. Tạo booking chính
            $bookingData = [
                'booking_code' => $bookingCode,
                'user_id' => $userId,
                'staff_id' => $staffId,
                'booking_date' => $bookingDate,
                'payment_method' => $customerInfo['payment_method'] ?? 'cash',
                'payment_status' => 'pending',
                'status' => 'pending',
                'total_pets' => $total_pets,
                'total_services' => $total_services,
                'total_duration' => $total_duration,
                'subtotal' => $subtotal,
                'discount_amount' => $discount_amount,
                'discount_percent' => $discount_percent,
                'total_amount' => $total_amount,
                'discount_code' => $bookingInfo['discount_code'] ?? null,
                'notes' => $customerInfo['notes'] ?? null,
                'customer_notes' => $customerInfo['notes'] ?? null,
                'staff_notes' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $booking = new Booking();
            foreach ($bookingData as $key => $value) {
                if (property_exists($booking, $key)) {
                    $booking->$key = $value;
                }
            }

            if (!$booking->save()) {
                throw new Exception('Không thể lưu thông tin đặt lịch');
            }

            // update point user
            $userFound = Account::find($userId);
            if ($userFound) {
                $userFound->points = $userFound->points + 1000;
                $userFound->ranking = $userFound->uptoRank($userFound->points); // Calculate ranking after updating points

                if (!$userFound->save()) {
                    throw new Exception('Không thể lưu thông tin điểm');
                }
            }

            $bookingId = $booking->id;

            // 2. Tạo booking details (chi tiết dịch vụ) - PHẦN ĐÃ SỬA
            foreach ($selectedServices as $service) {
                // Lấy thông tin service từ database để đảm bảo tính chính xác
                $serviceFromDB = Service::find($service['service_id']);

                $originalPrice = $serviceFromDB->price; // Giá gốc
                $serviceDiscountPercent = $serviceFromDB->discount_percent ?? 0; // % giảm giá của service

                // Tính unit_price sau khi áp dụng discount của service
                $unitPrice = $originalPrice;
                if ($serviceDiscountPercent > 0) {
                    $discountAmount = ($originalPrice * $serviceDiscountPercent) / 100;
                    $unitPrice = $originalPrice - $discountAmount;
                }

                $bookingDetail = new BookingDetail();
                $bookingDetail->booking_id = $bookingId;
                $bookingDetail->service_id = $service['service_id'];
                $bookingDetail->quantity = $service['quantity'];
                $bookingDetail->unit_price = $unitPrice; // Giá đã giảm
                $bookingDetail->discount_percent = $serviceDiscountPercent; // % giảm giá của service
                $bookingDetail->total_price = $unitPrice * $service['quantity']; // Tổng giá đã giảm
                $bookingDetail->duration = $service['duration'];
                $bookingDetail->notes = null;

                if (!$bookingDetail->save()) {
                    throw new Exception('Không thể lưu chi tiết dịch vụ');
                }
            }

            // 3. Tạo booking pets (thú cưng được chọn)
            foreach ($customerInfo['selected_pets'] as $petId) {
                $pet = Pet::find($petId);
                if (!$pet) {
                    throw new Exception('Không tìm thấy thú cưng với ID: ' . $petId);
                }

                $bookingPet = new BookingPet();
                $bookingPet->booking_id = $bookingId;
                $bookingPet->pet_id = $petId;
                $bookingPet->special_notes = null;

                if (!$bookingPet->save()) {
                    throw new Exception('Không thể lưu thông tin thú cưng');
                }
            }

            // 4. Tạo booking staff schedule (lịch nhân viên được chọn)
            if (isset($staffInfo['selected_appointments']) && !empty($staffInfo['selected_appointments'])) {
                foreach ($staffInfo['selected_appointments'] as $key => $schedule) {
                    $scheduleId = $schedule['scheduleId'];

                    // Kiểm tra schedule có tồn tại và available không
                    $schedule = StaffSchedule::find($scheduleId);
                    if (!$schedule || !$schedule->is_available) {
                        throw new Exception('Lịch làm việc không khả dụng');
                    }

                    $bookingStaffSchedule = new BookingStaffSchedule();
                    $bookingStaffSchedule->booking_id = $bookingId;
                    $bookingStaffSchedule->staff_schedule_id = $scheduleId;

                    if (!$bookingStaffSchedule->save()) {
                        throw new Exception('Không thể lưu lịch nhân viên');
                    }

                    // Cập nhật trạng thái schedule thành không khả dụng
                    $schedule->is_available = 0;
                    if (!$schedule->save()) {
                        throw new Exception('Không thể cập nhật trạng thái lịch làm việc');
                    }
                }
            }

            // Commit transaction
            $pdo->commit();

            // Xóa cart và booking info sau khi thành công
            unset($_SESSION['cart']);
            unset($_SESSION['booking_info']);

            // Chuyển hướng dựa trên phương thức thanh toán
            $paymentMethod = $customerInfo['payment_method'];

            $results = [
                'booking_code' => $bookingCode,
            ];

            if ($paymentMethod === 'vnpay') {
                // Chuyển hướng đến VNPay
                $results['payment_url'] = $this->redirectToVNPay($bookingId, $total_amount, $bookingCode);
            } elseif ($paymentMethod === 'momo') {
                // Chuyển hướng đến MoMo (implement sau)
                $this->redirectToMoMo($bookingId, $total_amount, $bookingCode);
            }

            RestApi::responseSuccess($results, 'Đặt lịch thành công');
        } catch (Exception $e) {
            // Rollback transaction nếu có lỗi
            $pdo->rollBack();

            // Log lỗi (nếu có hệ thống log)
            error_log('Booking Error: ' . $e->getMessage());

            // Chuyển hướng về trang lỗi hoặc hiển thị thông báo
            $message = 'Có lỗi xảy ra khi tạo đặt lịch: ' . $e->getMessage();
            RestApi::responseError($message);
        }
    }

    /**
     * Chuyển hướng đến VNPay để thanh toán
     */
    private function redirectToVNPay($bookingId, $amount, $bookingCode)
    {
        try {
            // Import VNPay helper hoặc service class
            // Giả sử bạn có VNPayService

            $vnp_TxnRef = $bookingCode . '_' . time(); // Mã giao dịch duy nhất
            $vnp_OrderInfo = 'Thanh toan dat lich dich vu thu cung - Ma booking: ' . $bookingCode;
            $vnp_Amount = $amount * 100; // VNPay yêu cầu nhân với 100

            // Lưu thông tin giao dịch VNPay
            $vnPayData = [
                'booking_id' => $bookingId,
                'vnp_txn_ref' => $vnp_TxnRef,
                'vnp_amount' => $vnp_Amount,
                'vnp_order_info' => $vnp_OrderInfo,
                'user_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'status' => 'created'
            ];

            // Tạo bản ghi VNPay transaction
            $vnPayTransaction = new VnPayTransactions();

            foreach ($vnPayData as $key => $value) {
                if (property_exists($vnPayTransaction, $key)) {
                    $vnPayTransaction->$key = $value;
                }
            }

            $vnPayTransaction->save();

            // Tạo URL thanh toán VNPay (implement theo tài liệu VNPay)
            $paymentUrl = $this->createVNPayUrl($vnp_TxnRef, $vnp_Amount, $vnp_OrderInfo);

            // Cập nhật payment URL vào database
            $vnPayTransaction->payment_url = $paymentUrl;
            $vnPayTransaction->save();

            return $paymentUrl;
        } catch (Exception $e) {
            error_log('VNPay Error: ' . $e->getMessage());
            throw new Exception('Không thể tạo URL thanh toán VNPay');
        }
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    private function createVNPayUrl($txnRef, $amount, $orderInfo)
    {
        // Implement theo tài liệu VNPay
        // Đây là ví dụ cơ bản, bạn cần điền đầy đủ theo config VNPay của mình

        $vnp_Url = $_ENV['VNP_URL']; // URL sandbox
        $vnp_return_url = base_url($_ENV['VNP_RETURN_URL']);
        $vnp_TmnCode = $_ENV['VNP_TMM_CODE']; // Mã website tại VNPay
        $vnp_HashSecret = $_ENV['VNP_HASH_SECRET']; // Chuỗi bí mật

        $vnp_CreateDate = date('YmdHis');
        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $orderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_return_url,
            "vnp_TxnRef" => $txnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashingData = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashingData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashingData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;

        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashingData, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    /**
     * Chuyển hướng đến MoMo (implement sau)
     */
    private function redirectToMoMo($bookingId, $amount, $bookingCode)
    {
        // TODO: Implement MoMo payment
        $_SESSION['error_message'] = 'Phương thức thanh toán MoMo chưa được hỗ trợ';
        redirect(base_url('/cart/finish'));
    }

    /**
     * Xử lý callback từ VNPay
     */
    public function vnpayReturn()
    {
        try {
            // Lấy dữ liệu từ VNPay return
            $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
            $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
            $vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';
            $vnp_Amount = $_GET['vnp_Amount'] ?? 0;
            $vnp_PayDate = $_GET['vnp_PayDate'] ?? '';
            $vnp_BankCode = $_GET['vnp_BankCode'] ?? '';
            $vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';

            // Tìm giao dịch VNPay
            $vnpayTransaction = \App\Models\VnPayTransactions::findOneBy('vnp_txn_ref', $vnp_TxnRef);

            if (!$vnpayTransaction) {
                throw new Exception('Không tìm thấy giao dịch');
            }

            // Cập nhật thông tin giao dịch
            $vnpayTransaction->vnp_response_code = $vnp_ResponseCode;
            $vnpayTransaction->vnp_transaction_no = $vnp_TransactionNo;
            $vnpayTransaction->vnp_pay_date = $vnp_PayDate;
            $vnpayTransaction->vnp_bank_code = $vnp_BankCode;
            $vnpayTransaction->vnp_secure_hash = $vnp_SecureHash;
            $vnpayTransaction->return_url_data = json_encode($_GET);

            if ($vnp_ResponseCode === '00') {
                // Thanh toán thành công
                $vnpayTransaction->status = 'success';
                $vnpayTransaction->save();

                // Cập nhật trạng thái booking
                $booking = Booking::find($vnpayTransaction->booking_id);
                if ($booking) {
                    $booking->payment_status = 'paid';
                    $booking->paid_at = date('Y-m-d H:i:s');
                    $booking->status = 'confirmed';
                    $booking->save();
                }

                redirect(base_url('/booking/success?code=' . $booking->booking_code));
            } else {
                // Thanh toán thất bại
                $vnpayTransaction->status = 'failed';
                $vnpayTransaction->save();

                // Cập nhật trạng thái booking
                $booking = Booking::find($vnpayTransaction->booking_id);
                if ($booking) {
                    $booking->payment_status = 'failed';
                    $booking->save();
                }

                $_SESSION['error_message'] = 'Thanh toán không thành công';
                redirect(base_url('/booking/failed'));
            }
        } catch (Exception $e) {
            error_log('VNPay Return Error: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Có lỗi xảy ra khi xử lý thanh toán';
            redirect(base_url('/booking/failed'));
        }
    }

    public function add($id)
    {
        $service = Service::find($id);

        if ($service) {
            // Bước 1: Lấy danh sách cart từ session
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $cart = $_SESSION['cart'];

            // Bước 2: Kiểm tra sản phẩm đã tồn tại trong cart chưa
            $exists = false;
            foreach ($cart as $itemId => $quantity) {
                if ($itemId == $id) {
                    // Nếu đã tồn tại thì tăng số lượng lên 1
                    $cart[$itemId] = [
                        'quantity' => $quantity['quantity'] + 1
                    ];
                    $exists = true;
                    break;
                }
            }

            // Nếu chưa tồn tại thì thêm sản phẩm vào cart với số lượng là 1
            if (!$exists) {
                $cart[$id] = [
                    'quantity' => 1
                ];
            }

            // Bước 3: Lưu danh sách cart vào session
            $_SESSION['cart'] = $cart;
        }

        // Bước 4: Chuyển hướng đến trang cart
        redirect(base_url('/cart'));
    }

    public function remove($id)
    {
        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];

            foreach ($cart as $key => $item) {
                if ($key == $id) {
                    unset($cart[$key]);
                    break;
                }
            }

            $_SESSION['cart'] = $cart;
            // Bước 4: Chuyển hướng đến trang cart
            redirect(base_url('/cart'));
        }
    }


    public function updateQuantity()
    {
        $quantity = $_POST['quantity'];
        $id = $_POST['id'];
        $total_price = 0;

        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];

            foreach ($cart as $key => $item) {
                if ($key == $id) {
                    $cart[$key]['quantity'] = (int) $quantity;
                    break;
                }
            }

            $_SESSION['cart'] = $cart;

            foreach ($cart as $key => $item) {
                $service = Service::find($key);
                $price_new = $service->price_new();
                $item['price'] = $price_new;
                $total_price += $item['price'] * $item['quantity'];
            }

            echo $total_price;
        }
    }

    // Method mới để lưu thông tin booking
    public function saveBookingInfo()
    {
        try {
            // Lấy dữ liệu từ POST request
            $selectedServices = $_POST['selected_services'] ?? [];
            $discountCode = $_POST['discount_code'] ?? '';
            $discountPercent = $_POST['discount_percent'] ?? 0;
            $subtotal = $_POST['subtotal'] ?? 0;
            $totalPrice = $_POST['total_price'] ?? 0;

            // Validation
            if (empty($selectedServices)) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Vui lòng chọn ít nhất một dịch vụ!'
                ]);
                return;
            }

            // Lưu thông tin vào session
            $_SESSION['booking_info'] = [
                'selected_services' => $selectedServices,
                'discount_code' => $discountCode,
                'discount_percent' => (float)$discountPercent,
                'subtotal' => (float)$subtotal,
                'total_price' => (float)$totalPrice,
                'created_at' => date('Y-m-d H:i:s')
            ];

            echo json_encode([
                'status' => true,
                'message' => 'Lưu thông tin thành công!',
                'redirect_url' => base_url('/cart/info')
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function saveCustomerInfo()
    {
        try {
            RestApi::setHeaders();

            $body = RestApi::getBody();

            // Lấy dữ liệu từ POST request
            $selectedPets = $body['pets'] ?? [];
            $paymentMethod = $body['paymentMethod'] ?? '';
            $selectedDate = $body['date'] ?? '';
            $userInfo = $body['userInfo'] ?? [];

            // Validation
            if (empty($selectedPets)) {
                RestApi::responseError('Vui lòng chọn ít nhất một thú cưng!');
            }

            if (empty($paymentMethod)) {
                RestApi::responseError('Vui lòng chọn hình thức thanh toán!');
            }

            if (empty($selectedDate)) {
                RestApi::responseError('Vui lòng chọn ngày đặt lịch!');
            }

            // Validate date không được là quá khứ
            $today = date('Y-m-d');
            if ($selectedDate < $today) {
                RestApi::responseError('Ngày đặt lịch phải là ngày trong tương lai!');
            }

            // Lấy thông tin booking_info hiện tại từ session (từ step trước)
            $existingBookingInfo = $_SESSION['booking_info'] ?? [];

            // Cập nhật thông tin customer vào booking_info
            $_SESSION['booking_info'] = array_merge($existingBookingInfo, [
                'customer_info' => [
                    'selected_pets' => $selectedPets,
                    'payment_method' => $paymentMethod,
                    'selected_date' => $selectedDate,
                    'user_info' => $userInfo,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);

            RestApi::responseSuccess(['redirect_url' => base_url('/cart/staff')], 'Lưu thông tin thành công!');
        } catch (Exception $e) {
            RestApi::responseError('Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function saveStaffSchedule()
    {
        try {
            RestApi::setHeaders();

            $body = RestApi::getBody();

            // Lấy dữ liệu từ POST request
            $selectedAppointment = $body['selected_appointments'] ?? [];
            $staffId = $body['staff_id'] ?? '';

            // Validation
            if (empty($selectedAppointment)) {
                RestApi::responseError('Vui lòng chọn lịch hẹn trước khi tiếp tục!');
            }

            if (empty($staffId)) {
                RestApi::responseError('Vui lòng chọn nhân viên!');
            }

            // Lấy thông tin booking_info hiện tại từ session (từ step trước)
            $existingBookingInfo = $_SESSION['booking_info'] ?? [];

            // Cập nhật thông tin customer vào booking_info
            $_SESSION['booking_info'] = array_merge($existingBookingInfo, [
                'staff_info' => [
                    'selected_appointments' => $selectedAppointment,
                    'staff_id' => $staffId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);

            RestApi::responseSuccess(['redirect_url' => base_url('/cart/finished')], 'Lưu thông tin thành công!');
        } catch (Exception $e) {
            RestApi::responseError('Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Method để xóa thông tin booking (nếu cần)
    public function clearBookingInfo()
    {
        unset($_SESSION['booking_info']);

        echo json_encode([
            'status' => true,
            'message' => 'Đã xóa thông tin booking!'
        ]);
    }
}
