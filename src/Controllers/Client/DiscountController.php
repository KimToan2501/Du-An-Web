<?php

namespace App\Controllers\Client;

use App\Models\Discount;

class DiscountController
{
    // public function index()
    // {
    //     $discounts = Discount::all();
    //     render_view('client/discount/index', ['discounts' => $discounts], 'client');
    // }

    public function checkCode()
    {
        $code = $_POST['code'];
        $discount = Discount::findByCode($code);
        // dd($discount);
        if (!isset($discount)) {
            echo json_encode(['status' => false, 'message' => 'Mã giảm giá không tồn tại']);
            return;
        };
        if ($discount->start_date > date('Y-m-d') || $discount->end_date < date('Y-m-d')) {
            echo json_encode(['status' => false, 'message' => 'Mã giảm giá đã hết hạn']);
            return;
        };
        echo json_encode(['status' => true, 'message' => 'Mã giảm giá hợp lệ', 'discount' => $discount]);
    }
}
