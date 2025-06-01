<?php

namespace App\controllers\client;

class HomeController
{
    public function index()
    {
        $data = [
            'title' => 'Trang chủ',
        ];

        render_view('client/home/index', $data, 'client');
    }

    public function introduce()
    {
        $data = [
            'title' => 'Giới thiệu',
        ];

        render_view('client/home/introduce', $data, 'client');
    }

    public function contact()
    {
        $data = [
            'title' => 'Liên hệ',
        ];

        render_view('client/home/contact', $data, 'client');
    }
}
