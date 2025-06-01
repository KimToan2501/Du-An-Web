<?php

namespace App\Controllers\Client;

use App\Core\Auth;
use App\Models\Booking;
use App\Models\VnPayTransactions;
use Exception;

class BookingController
{
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

        $result = Booking::paginateWhere($whereConditions, $page, $perPage, $whereOperator, 'created_at', 'DESC');

        $data = [
            'title' => 'Lịch sử đặt lịch',
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

        render_view('client/booking/booking', $data, 'client');
    }

    public function response()
    {
        $params = $_GET;
        $bookingCode = isset($params['booking_code']) ? $params['booking_code'] : '';

        $data = [
            'booking' => null,
            'vnpay_result' => null,
            'is_vnpay_payment' => false,
            'payment_success' => false,
            'message' => '',
            'title' => 'Kết quả đặt lịch',
        ];

        // Nếu có booking_code thì không phải thanh toán VNPay
        if (!empty($bookingCode)) {
            $booking = Booking::findOneBy('booking_code', $bookingCode);
            if ($booking) {
                $data['booking'] = Booking::getBookingDetails($booking->id);
                $data['message'] = 'Thông tin đặt lịch của bạn';
            } else {
                $data['message'] = 'Không tìm thấy thông tin đặt lịch';
            }
        } else {
            // Xử lý response từ VNPay
            $data['is_vnpay_payment'] = true;
            $vnpayResult = $this->processVnPayResponse($params);
            $data['vnpay_result'] = $vnpayResult;

            if ($vnpayResult['booking']) {
                $data['booking'] = $vnpayResult['booking'];
                $data['payment_success'] = $vnpayResult['success'];
                $data['message'] = $vnpayResult['message'];
            }
        }

        // dd($data);

        render_view('client/booking/response', $data, 'client');
    }

    private function processVnPayResponse($params)
    {
        $result = [
            'success' => false,
            'message' => 'Thanh toán thất bại',
            'booking' => null,
            'transaction' => null
        ];

        try {
            // Lấy thông tin cần thiết từ VNPay response
            $vnp_TxnRef = $params['vnp_TxnRef'] ?? '';
            $vnp_Amount = $params['vnp_Amount'] ?? 0;
            $vnp_OrderInfo = $params['vnp_OrderInfo'] ?? '';
            $vnp_ResponseCode = $params['vnp_ResponseCode'] ?? '';
            $vnp_TransactionNo = $params['vnp_TransactionNo'] ?? '';
            $vnp_TransactionStatus = $params['vnp_TransactionStatus'] ?? '';
            $vnp_PayDate = $params['vnp_PayDate'] ?? '';
            $vnp_BankCode = $params['vnp_BankCode'] ?? '';
            $vnp_BankTranNo = $params['vnp_BankTranNo'] ?? '';
            $vnp_CardType = $params['vnp_CardType'] ?? '';
            $vnp_SecureHash = $params['vnp_SecureHash'] ?? '';

            // Xác thực chữ ký (cần có VNPay config)
            if (!$this->validateVnPaySignature($params)) {
                $result['message'] = 'Chữ ký không hợp lệ';
                return $result;
            }

            // Tìm transaction theo vnp_TxnRef
            $transaction = VnPayTransactions::findOneBy('vnp_txn_ref', $vnp_TxnRef);
            if (!$transaction) {
                $result['message'] = 'Không tìm thấy giao dịch';
                return $result;
            }

            // Lấy thông tin booking
            $booking = Booking::find($transaction->booking_id);
            if (!$booking) {
                $result['message'] = 'Không tìm thấy thông tin đặt lịch';
                return $result;
            }

            // Cập nhật thông tin transaction
            $updateTransactionData = [
                'vnp_transaction_no' => $vnp_TransactionNo,
                'vnp_response_code' => $vnp_ResponseCode,
                'vnp_transaction_status' => $vnp_TransactionStatus,
                'vnp_pay_date' => $this->formatVnPayDate($vnp_PayDate),
                'vnp_bank_code' => $vnp_BankCode,
                'vnp_bank_tran_no' => $vnp_BankTranNo,
                'vnp_card_type' => $vnp_CardType,
                'vnp_secure_hash' => $vnp_SecureHash,
                'return_url_data' => json_encode($params),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Kiểm tra kết quả thanh toán
            if ($vnp_ResponseCode === '00' && $vnp_TransactionStatus === '00') {
                // Thanh toán thành công
                $updateTransactionData['status'] = 'success';
                VnPayTransactions::update($transaction->id, $updateTransactionData);

                // Cập nhật trạng thái booking
                $updateBookingData = [
                    'payment_status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s'),
                    'status' => 'confirmed', // Tự động xác nhận khi thanh toán thành công
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Booking::update($booking->id, $updateBookingData);

                $result['success'] = true;
                $result['message'] = 'Thanh toán thành công! Đặt lịch của bạn đã được xác nhận.';
            } else {
                // Thanh toán thất bại
                $updateTransactionData['status'] = 'failed';
                VnPayTransactions::update($transaction->id, $updateTransactionData);

                // Cập nhật trạng thái booking
                $updateBookingData = [
                    'payment_status' => 'failed',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                Booking::update($booking->id, $updateBookingData);

                $result['message'] = $this->getVnPayErrorMessage($vnp_ResponseCode);
            }

            // Lấy thông tin booking đã cập nhật
            $result['booking'] = Booking::getBookingDetails($booking->id);
            $result['transaction'] = VnPayTransactions::find($transaction->id);
        } catch (Exception $e) {
            error_log('VNPay Response Error: ' . $e->getMessage());
            $result['message'] = 'Có lỗi xảy ra khi xử lý thanh toán';
        }

        return $result;
    }

    private function validateVnPaySignature($params)
    {
        // Cần có VNPay Hash Secret từ config
        $vnp_HashSecret = $_ENV['VNP_HASH_SECRET']; // Hoặc từ file config của bạn

        $vnp_SecureHash = $params['vnp_SecureHash'];
        unset($params['vnp_SecureHash']);

        ksort($params);
        $hashData = "";
        $i = 0;
        foreach ($params as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        return $secureHash === $vnp_SecureHash;
    }

    private function formatVnPayDate($vnpayDate)
    {
        // VNPay date format: YYYYMMDDHHmmss
        if (strlen($vnpayDate) === 14) {
            return date('Y-m-d H:i:s', strtotime($vnpayDate));
        }
        return null;
    }

    private function getVnPayErrorMessage($responseCode)
    {
        $errorMessages = [
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP).',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)'
        ];

        return $errorMessages[$responseCode] ?? 'Giao dịch không thành công';
    }

    public function ipn()
    {
        // Xử lý IPN từ VNPay (tương tự như response nhưng không redirect)
        $params = $_GET;

        try {
            // Xác thực chữ ký
            if (!$this->validateVnPaySignature($params)) {
                echo "RspCode=97&Message=Invalid signature";
                return;
            }

            $vnp_TxnRef = $params['vnp_TxnRef'] ?? '';
            $vnp_ResponseCode = $params['vnp_ResponseCode'] ?? '';
            $vnp_TransactionStatus = $params['vnp_TransactionStatus'] ?? '';

            // Tìm transaction
            $transaction = VnPayTransactions::findOneBy('vnp_txn_ref', $vnp_TxnRef);
            if (!$transaction) {
                echo "RspCode=01&Message=Order not found";
                return;
            }

            // Cập nhật IPN data
            $updateData = [
                'ipn_data' => json_encode($params),
                'vnp_response_code' => $vnp_ResponseCode,
                'vnp_transaction_status' => $vnp_TransactionStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            VnPayTransactions::update($transaction['id'], $updateData);

            echo "RspCode=00&Message=Confirm Success";
        } catch (Exception $e) {
            error_log('VNPay IPN Error: ' . $e->getMessage());
            echo "RspCode=99&Message=Unknown error";
        }
    }
}
