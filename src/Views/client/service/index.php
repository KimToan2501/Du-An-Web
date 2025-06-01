<?php

use App\Models\Review;

start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ProductPageCss.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/service.css') ?>">
<link rel="stylesheet" href="<?= base_url('/cms/assets/css/components/admin-pagination.css') ?>">
<?php end_section(); ?>

<section class="hero">
    <div class="container hero-content">
        <div class="text">
            <h1>Tình Yêu Và Chăm Sóc Cho Thú Cưng Của Bạn</h1>
            <p>
                Chúng tôi cung cấp các dịch vụ chăm sóc và huấn luyện tốt nhất cho
                thú cưng của bạn.
            </p>
        </div>
        <div class="hero-images">
            <img src="<?= base_url('assets/images/service/pet1.png') ?>" alt="Pets" />
        </div>
    </div>
    <div class="service-cards">
        <div class="card">Spa</div>
        <div class="card">Cắt tỉa lông</div>
        <div class="card">Khách sạn lưu trú</div>
    </div>
</section>

<div class="container service-section services">
    <h2 class="section-title text-center text-uppercase">Dịch vụ có sẵn</h2>

    <?php if (!empty($service_types)): ?>
        <form method="GET" class="mb-2 d-flex">
            <select name="type" id="" class="form-select">
                <option value="">Tất cả</option>
                <?php foreach ($service_types as $item): ?>
                    <option value="<?= $item->service_type_id ?>" <?= $item->service_type_id == $type ? 'selected' : '' ?>>
                        <?= $item->name ?>
                    </option>
                <?php endforeach ?>
            </select>

            <button type="submit" class="btn btn-primary">Lọc</button>
        </form>
    <?php endif ?>

    <div class="row g-4">
        <?php if (!empty($metadata)): ?>
            <?php foreach ($metadata as $item): ?>
                <?php
                $img = $item->get_first_image($item->service_id);
                $totalReview = Review::countReviewsByService($item->service_id);
                $rating = Review::getAverageRatingByService($item->service_id);
                ?>

                <!-- Card 1 -->
                <div class="col-md-3">
                    <div class="product-card p-2">
                        <img
                            src="<?= base_url($img ?? '/assets/images/Product/CATTIALONGTAOKIEU.webp') ?>"
                            class="product-image w-100 mb-2"
                            alt="Cắt tỉa lông tạo kiểu" />

                        <h6 class="fw-bold"><?= $item->name ?></h6>

                        <div class="rating-container">
                            <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                            <span class="rating-text">(<?= $rating ?>) • <?= $totalReview ?>+ reviews</span>
                        </div>

                        <div>
                            <?php if ($item->discount_percent): ?>
                                <span class="price">
                                    <?= format_price($item->price_new()) ?>
                                </span>
                                <span class="old-price"><?= format_price($item->price) ?></span>
                            <?php else: ?>
                                <span class="price"><?= format_price($item->price) ?></span>
                            <?php endif ?>
                        </div>

                        <a
                            href="<?= base_url('/service/detail/' . $item->service_id) ?>"
                            class="btn btn-purple w-100 mt-2"
                            style="background-color: #6f42c1; color: white">
                            Chi tiết
                        </a>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <p class="text-center">Không có dịch vụ nào</p>
        <?php endif ?>
    </div>

    <?php include_partial('admin/pagination', ['pagination' => $pagination]) ?>
</div>

<?php if (!empty($staffs)): ?>
    <style>
        :root {
            --primary-color: #6f42c1;
            --secondary-color: #f8f9fa;
            --accent-color: #20c997;
            --text-dark: #2c3e50;
            --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .hero-section {
            background: var(--gradient-bg);
            color: white;
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><path d="M0,20 Q250,80 500,20 T1000,20 V100 H0 Z"/></svg>') no-repeat bottom;
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .section-title {
            position: relative;
            margin-bottom: 3rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 2px;
        }

        .staff-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            overflow: hidden;
            margin-bottom: 30px;
            position: relative;
        }

        .staff-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .staff-image-container {
            position: relative;
            overflow: hidden;
            height: 280px;
        }

        .staff-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .staff-card:hover .staff-image {
            transform: scale(1.1);
        }

        .staff-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(111, 66, 193, 0.8), rgba(32, 201, 151, 0.6));
            opacity: 0;
            transition: opacity 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .staff-card:hover .staff-overlay {
            opacity: 1;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-link {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .staff-info {
            padding: 25px;
            text-align: center;
        }

        .staff-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .staff-position {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .rating-container {
            margin: 15px 0;
        }

        .rating-stars {
            color: #ffd700;
            font-size: 1.1rem;
            margin-right: 8px;
        }

        .rating-text {
            color: #666;
            font-weight: 500;
        }

        .staff-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .experience-badge {
            display: inline-block;
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 10px;
        }

        .contact-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }

        .contact-btn:hover {
            background: var(--accent-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .stats-section {
            background: white;
            padding: 40px 0;
            margin: 60px 0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .stat-label {
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .filter-tabs {
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-btn {
            background: white;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0 40px;
            }

            .staff-card {
                margin-bottom: 20px;
            }

            .stat-number {
                font-size: 2.5rem;
            }
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-elements::before {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-elements::after {
            top: 60%;
            right: 10%;
            animation-delay: 3s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }
    </style>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="floating-elements"></div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4">Đội Ngũ Chuyên Gia</h1>
                    <p class="lead mb-4">Gặp gỡ những chuyên gia tận tâm, yêu thương và chăm sóc thú cưng như chính con em của họ</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <span class="badge bg-light text-dark px-3 py-2"><i class="fas fa-heart me-2"></i>Tận Tâm</span>
                        <span class="badge bg-light text-dark px-3 py-2"><i class="fas fa-certificate me-2"></i>Chuyên Nghiệp</span>
                        <span class="badge bg-light text-dark px-3 py-2"><i class="fas fa-shield-alt me-2"></i>An Toàn</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <div class="container">
        <div class="stats-section">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">15+</span>
                        <div class="stat-label">Chuyên Gia</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">1000+</span>
                        <div class="stat-label">Khách Hàng</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">5</span>
                        <div class="stat-label">Năm Kinh Nghiệm</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <span class="stat-number">4.9</span>
                        <div class="stat-label">Đánh Giá</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Nhân Viên Thân Thiện</h2>

            <div class="row">
                <?php foreach ($staffs as $item):  ?>
                    <?php $totalReview = Review::countReviewsByStaff($item->user_id) ?>

                    <div class="col-lg-4 col-md-6" data-category="groomer">
                        <div class="staff-card">
                            <div class="staff-image-container">
                                <img src="<?= show_avatar($item->avatar_url) ?>" alt="<?= $item->name ?>" class="staff-image">
                                <div class="staff-overlay">
                                    <div class="social-links">
                                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                        <a href="#" class="social-link"><i class="fas fa-phone"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="staff-info">
                                <h3 class="staff-name"><?= $item->name ?></h3>
                                <div class="staff-position">Senior Groomer</div>
                                <div class="rating-container">
                                    <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                                    <span class="rating-text">(<?= $item->rating ?>) • <?= $totalReview ?>+ reviews</span>
                                </div>
                                <p class="staff-description">
                                    Chuyên gia cắt tỉa lông với 8 năm kinh nghiệm. Tôi yêu thích tạo ra những kiểu dáng độc đáo cho từng thú cưng.
                                </p>
                                <div class="experience-badge">8 năm kinh nghiệm</div>
                                <a href="#" class="contact-btn">Liên Hệ</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Load More Button -->
            <div class="text-center mt-5">
                <button class="contact-btn" style="padding: 15px 40px; font-size: 1.1rem;">
                    <i class="fas fa-plus-circle me-2"></i>Xem Thêm Nhân Viên
                </button>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container text-center text-white">
            <h2 class="display-5 fw-bold mb-4">Sẵn Sàng Chăm Sóc Thú Cưng?</h2>
            <p class="lead mb-4">Đặt lịch ngay hôm nay để thú cưng của bạn được chăm sóc bởi đội ngũ chuyên nghiệp nhất</p>
            <a href="#" class="btn btn-light btn-lg px-5 py-3 fw-bold">
                <i class="fas fa-calendar-alt me-2"></i>Đặt Lịch Ngay
            </a>
        </div>
    </section>


    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const staffCards = document.querySelectorAll('[data-category]');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');

                    staffCards.forEach(card => {
                        if (filter === 'all' || card.getAttribute('data-category') === filter) {
                            card.style.display = 'block';
                            card.style.animation = 'fadeIn 0.5s ease-in';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });

            // Add fade in animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `;
            document.head.appendChild(style);
        });

        // Smooth scroll for CTA buttons
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
<?php endif ?>

<section class="services container">
    <h2>What We Offer</h2>
    <p class="subtitle">
        Trung tâm chăm sóc thú cưng của chúng tôi cung cấp đầy đủ dịch vụ từ
        tắm, cắt tỉa, spa và lưu trú cao cấp. Với đội ngũ tận tâm và yêu thú
        cưng như chính thú cưng của mình, chúng tôi cam kết mang đến sự chăm sóc
        tốt nhất để thú cưng của bạn luôn khỏe mạnh và hạnh phúc.
    </p>

    <div class="service-list">
        <div class="service-item">
            <img src="<?= base_url('assets/images/service/pet2.png') ?>" alt="Group Training" />
            <div class="text">
                <h3>Group Training</h3>
                <p>Đào tạo theo nhóm với các huấn luyện viên chuyên nghiệp.</p>
            </div>
        </div>

        <div class="service-item reverse">
            <img src="<?= base_url('assets/images/service/pet3.png') ?>" alt="Puppy Training" />
            <div class="text">
                <h3>Puppy Training</h3>
                <p>Đào tạo dành riêng cho chó con từ 8 tuần tuổi.</p>
            </div>
        </div>

        <div class="service-item">
            <img src="<?= base_url('assets/images/service/pet4.png') ?>" alt="Private Training" />
            <div class="text">
                <h3>Private Training</h3>
                <p>Đào tạo cá nhân với huấn luyện viên tại nhà hoặc trung tâm.</p>
            </div>
        </div>

        <div class="service-item reverse">
            <img src="<?= base_url('assets/images/service/pet5.png') ?>" alt="Specialty Program" />
            <div class="text">
                <h3>Specialty Program</h3>
                <p>Chương trình đặc biệt cho các nhu cầu hành vi cụ thể.</p>
            </div>
        </div>

        <div class="service-item">
            <img src="<?= base_url('assets/images/service/pet6.png') ?>" alt="Virtual Training" />
            <div class="text">
                <h3>Virtual Training</h3>
                <p>Đào tạo trực tuyến qua Zoom, Skype với huấn luyện viên.</p>
            </div>
        </div>

        <div class="service-item reverse">
            <img src="<?= base_url('assets/images/service/pet7.png') ?>" alt="Security Program" />
            <div class="text">
                <h3>Security Program</h3>
                <p>Đào tạo thú cưng cho các nhiệm vụ an ninh hoặc bảo vệ.</p>
            </div>
        </div>
    </div>
</section>

<section class="appointment">
    <h2>Đặt Lịch</h2>
    <p>Chọn thời gian phù hợp để đặt lịch cho thú cưng của bạn.</p>
    <a href="#" class="btn">Đặt lịch ngay</a>
</section>