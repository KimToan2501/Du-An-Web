<?php

use App\Models\Review;

start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?= start_section('links') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/detail_service.css') ?>">
<?= end_section() ?>

<!-- Banner sản phẩm -->
<div class="banner">
    <img src="<?= base_url('assets/images/Product/Chitiet/banner.png') ?>" alt="Banner Sản Phẩm" class="banner-img">
</div>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('/service') ?>">Dịch vụ</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Chi tiết sản phẩm -->
<section class="product-main container mt-2">
    <div class="product-images">
        <?php
        $images = $service->service_images($service->service_id);
        $defaultImages = [
            'assets/images/Product/Chitiet/hinh1.svg',
            'assets/images/Product/Chitiet/hinh2.jpg',
            'assets/images/Product/Chitiet/hinh3.svg'
        ];
        ?>

        <div id="serviceImageCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $index => $image): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= base_url($image->image_url) ?>"
                                class="d-block w-100 img-fluid object-fit-contain"
                                style="height: 400px;"
                                alt="<?= $service->name ?> - Hình <?= $index + 1 ?>">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($defaultImages as $index => $imagePath): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= base_url($imagePath) ?>"
                                class="d-block w-100 img-fluid object-fit-contain"
                                style="height: 400px;"
                                alt="<?= $service->name ?> - Hình <?= $index + 1 ?>">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ((empty($images) && count($defaultImages) > 1) || (!empty($images) && count($images) > 1)): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#serviceImageCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#serviceImageCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            <?php endif; ?>
        </div>

        <!-- Thumbnail images -->
        <div class="thumbnail-images mt-3">
            <?php if (!empty($images)): ?>
                <?php foreach ($images as $index => $image): ?>
                    <img src="<?= base_url($image->image_url) ?>"
                        data-bs-target="#serviceImageCarousel"
                        data-bs-slide-to="<?= $index ?>"
                        alt="Thumbnail <?= $index + 1 ?>"
                        class="thumbnail-img <?= $index === 0 ? 'active' : '' ?>"
                        style="width: 100px; height: 80px; cursor: pointer; margin: 0 5px; border: 2px solid transparent; object-fit: cover;">
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($defaultImages as $index => $imagePath): ?>
                    <img src="<?= base_url($imagePath) ?>"
                        data-bs-target="#serviceImageCarousel"
                        data-bs-slide-to="<?= $index ?>"
                        alt="Thumbnail <?= $index + 1 ?>"
                        class="thumbnail-img <?= $index === 0 ? 'active' : '' ?>"
                        style="width: 100px; height: 80px; cursor: pointer; margin: 0 5px; border: 2px solid transparent; object-fit: cover;">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-info">
        <h1><?= $service->name;  ?></h1>
        <div class="rating-container">
            <?php
            $totalReview = Review::countReviewsByService($service->service_id);
            $rating = Review::getAverageRatingByService($service->service_id);
            ?>
            <span class="rating-stars">
                <?php
                $fullStars = floor($rating);
                $halfStar = ($rating - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                for ($i = 0; $i < $fullStars; $i++) echo '⭐';
                if ($halfStar) echo '⭐';
                for ($i = 0; $i < $emptyStars; $i++) echo '☆';
                ?>
            </span>
            <span class="rating-text">(<?= number_format($rating, 1) ?>) • <?= $totalReview ?> đánh giá</span>
        </div>

        <div class="price">
            <?php if ($service->discount_percent): ?>
                <span class="current-price">
                    <?= format_price($service->price_new()) ?>
                </span>
                <span class="old-price"><?= format_price($service->price) ?></span>
                <span class="discount-badge">-<?= $service->discount_percent ?>%</span>
            <?php else: ?>
                <span class="current-price"><?= format_price($service->price) ?></span>
            <?php endif ?>
        </div>

        <div class="actions mt-2">
            <a href="<?= base_url('/cart/add/' . $service->service_id) ?>" class="btn-primary">Thêm giỏ hàng</a>
        </div>
    </div>
</section>

<!-- Tab thông tin sản phẩm và đánh giá -->
<section class="product-tabs container">
    <div class="tabs">
        <button class="tab active" data-tab="info">Thông tin sản phẩm</button>
        <button class="tab" data-tab="reviews">Đánh giá (<?= $totalReview ?>)</button>
    </div>

    <div class="tab-content active" id="info-content">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="list-group mb-4">
                    <a href="#information" class="list-group-item list-group-item-action active">Thông tin</a>
                    <a href="#description" class="list-group-item list-group-item-action">Mô tả dịch vụ</a>
                    <a href="#benefits" class="list-group-item list-group-item-action">Lợi ích</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6>Thông tin dịch vụ</h6>
                        <p>Thời gian: <strong><?= $service->duration ?> phút</strong></p>
                        <p>Giá: <strong><?= format_price($service->price) ?></strong></p>
                        <?php if ($service->discount_percent): ?>
                            <p>Giảm giá: <strong><?= $service->discount_percent ?>%</strong></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9">
                <div id="information">
                    <h5>Thông tin</h5>
                    <p><strong>Tên dịch vụ:</strong> <?= $service->name ?></p>
                    <p><strong>Thời gian thực hiện:</strong> <?= $service->duration ?> phút</p>
                    <p><strong>Giá dịch vụ:</strong> <?= format_price($service->price) ?></p>
                </div>

                <div id="description" class="mt-4">
                    <h5>Mô tả dịch vụ</h5>
                    <p><?= $service->description ?: 'Chưa cập nhật thông tin mô tả' ?></p>
                </div>

                <div id="benefits" class="mt-4">
                    <h5>Lợi ích</h5>
                    <ul>
                        <li>Dịch vụ chuyên nghiệp với đội ngũ có kinh nghiệm</li>
                        <li>Sử dụng các sản phẩm an toàn cho thú cưng</li>
                        <li>Môi trường sạch sẽ, thân thiện</li>
                        <li>Giá cả hợp lý, chất lượng cao</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="reviews-content">
        <div class="reviews-section">
            <h2>Đánh giá từ khách hàng</h2>

            <!-- Review Summary -->
            <div class="review-summary mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="rating-overview text-center">
                            <div class="big-rating"><?= number_format($rating, 1) ?></div>
                            <div class="stars mb-2">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rating ? '⭐' : '☆';
                                }
                                ?>
                            </div>
                            <div class="total-reviews"><?= $totalReview ?> đánh giá</div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <?php
                        // Tính phân bố đánh giá theo sao
                        $ratingDistribution = [];
                        for ($i = 1; $i <= 5; $i++) {
                            $ratingDistribution[$i] = Review::countByRating($service->service_id, $i);
                        }
                        ?>
                        <div class="rating-bars">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <div class="rating-bar-row">
                                    <span><?= $i ?> ⭐</span>
                                    <div class="progress mx-2">
                                        <div class="progress-bar" style="width: <?= $totalReview > 0 ? ($ratingDistribution[$i] / $totalReview * 100) : 0 ?>%"></div>
                                    </div>
                                    <span><?= $ratingDistribution[$i] ?></span>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Controls -->
            <div class="review-controls mb-3 d-flex justify-content-between align-items-center">
                <div class="review-count">
                    <span id="review-count-display">Hiển thị <?= $totalReview ?> đánh giá (<?= number_format($rating, 1) ?>⭐)</span>
                </div>
                <div class="review-sorting">
                    <select id="review-sort" class="form-select form-select-sm" style="width: auto;">
                        <option value="newest">Mới nhất</option>
                        <option value="oldest">Cũ nhất</option>
                        <option value="highest">Điểm cao nhất</option>
                        <option value="lowest">Điểm thấp nhất</option>
                    </select>
                </div>
            </div>

            <!-- Individual Reviews -->
            <div class="reviews-list" id="reviews-list">
                <?php
                $reviews = Review::findByServiceId($service->service_id);
                if (!empty($reviews)):
                ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="review-header d-flex justify-content-between">
                                <div>
                                    <strong><?= $review->is_anonymous ? 'Khách hàng ẩn danh' : 'Khách hàng' ?></strong>
                                    <div class="review-stars">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $review->rating ? '⭐' : '☆';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($review->created_at)) ?></small>
                            </div>
                            <div class="review-content mt-2">
                                <p><?= htmlspecialchars($review->comment) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-reviews text-center py-4">
                        <p>Chưa có đánh giá nào cho dịch vụ này.</p>
                        <p class="text-muted">Hãy là người đầu tiên đánh giá dịch vụ này!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php start_section('scripts') ?>
<!-- In your layout head or before closing body tag -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /**
     * Detail Service Page JavaScript
     * Xử lý slide ảnh, reviews, và các tương tác khác
     */

    class DetailServiceManager {
        constructor() {
            this.currentPage = 1;
            this.serviceId = this.getServiceIdFromUrl();
            this.reviewsPerPage = 5;
            this.currentSort = 'newest';
            this.allReviews = [];

            this.init();
        }

        init() {
            // Wait for Bootstrap to be available
            this.waitForBootstrap(() => {
                this.initImageCarousel();
            });

            this.initTabs();
            this.initSidebarNavigation();
            this.initReviewSorting();
            this.initLazyLoading();
            this.initScrollEffects();
            this.loadAllReviews();
        }

        waitForBootstrap(callback) {
            if (typeof bootstrap !== 'undefined') {
                callback();
            } else {
                // Wait for Bootstrap to load
                setTimeout(() => this.waitForBootstrap(callback), 100);
            }
        }

        getServiceIdFromUrl() {
            const pathParts = window.location.pathname.split('/');
            return pathParts[pathParts.length - 1];
        }

        /**
         * Load all reviews from PHP data
         */
        loadAllReviews() {
            // Get reviews from existing HTML
            const reviewItems = document.querySelectorAll('.review-item');
            this.allReviews = [];

            reviewItems.forEach((item, index) => {
                const customerName = item.querySelector('strong').textContent;
                const stars = item.querySelectorAll('.review-stars ⭐').length ||
                    (item.querySelector('.review-stars').textContent.match(/⭐/g) || []).length;
                const dateText = item.querySelector('.text-muted').textContent;
                const comment = item.querySelector('.review-content p').textContent;

                this.allReviews.push({
                    customer_name: customerName,
                    rating: stars,
                    date: dateText,
                    comment: comment,
                    created_at: this.parseDate(dateText)
                });
            });
        }

        parseDate(dateString) {
            const parts = dateString.split('/');
            if (parts.length === 3) {
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }
            return new Date();
        }

        /**
         * Khởi tạo carousel hình ảnh
         */
        initImageCarousel() {
            const carousel = document.getElementById('serviceImageCarousel');
            if (!carousel) return;

            // Xử lý thumbnail clicks
            document.querySelectorAll('.thumbnail-img').forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.updateThumbnailActive(index);

                    // Trigger carousel slide
                    if (typeof bootstrap !== 'undefined') {
                        const carouselInstance = bootstrap.Carousel.getOrCreateInstance(carousel);
                        carouselInstance.to(index);
                    }
                });
            });

            // Xử lý carousel slide events
            carousel.addEventListener('slide.bs.carousel', (e) => {
                this.updateThumbnailActive(e.to);
            });

            // Preload images
            this.preloadImages();
        }

        updateThumbnailActive(activeIndex) {
            document.querySelectorAll('.thumbnail-img').forEach((thumb, index) => {
                thumb.classList.remove('active');
                thumb.style.border = '2px solid transparent';

                if (index === activeIndex) {
                    thumb.classList.add('active');
                    thumb.style.border = '2px solid #007bff';
                }
            });
        }

        preloadImages() {
            const images = document.querySelectorAll('.carousel-item img');
            images.forEach(img => {
                if (!img.complete) {
                    img.addEventListener('load', () => {
                        img.classList.add('loaded');
                    });
                } else {
                    img.classList.add('loaded');
                }
            });
        }

        /**
         * Khởi tạo tab switching
         */
        initTabs() {
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tabType = tab.getAttribute('data-tab');
                    this.switchTab(tabType, tab);
                });
            });
        }

        switchTab(tabType, clickedTab) {
            // Remove active từ tất cả tabs và contents
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Add active cho tab được click
            clickedTab.classList.add('active');

            // Hiển thị content tương ứng
            const targetContent = document.getElementById(tabType + '-content');
            if (targetContent) {
                targetContent.classList.add('active');
            }
        }

        /**
         * Khởi tạo sidebar navigation
         */
        initSidebarNavigation() {
            document.querySelectorAll('.list-group-item').forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();

                    // Update active state
                    document.querySelectorAll('.list-group-item').forEach(i => {
                        i.classList.remove('active');
                    });
                    item.classList.add('active');

                    // Smooth scroll to target
                    const target = item.getAttribute('href');
                    if (target && target !== '#') {
                        const targetElement = document.querySelector(target);
                        if (targetElement) {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                });
            });
        }

        /**
         * Khởi tạo review sorting
         */
        initReviewSorting() {
            const sortSelect = document.getElementById('review-sort');
            if (sortSelect) {
                sortSelect.addEventListener('change', (e) => {
                    this.currentSort = e.target.value;
                    this.sortAndDisplayReviews();
                });
            }
        }

        sortAndDisplayReviews() {
            let sortedReviews = [...this.allReviews];

            switch (this.currentSort) {
                case 'newest':
                    sortedReviews.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                    break;
                case 'oldest':
                    sortedReviews.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    break;
                case 'highest':
                    sortedReviews.sort((a, b) => b.rating - a.rating);
                    break;
                case 'lowest':
                    sortedReviews.sort((a, b) => a.rating - b.rating);
                    break;
            }

            this.renderReviews(sortedReviews);
        }

        renderReviews(reviews) {
            const reviewsList = document.getElementById('reviews-list');
            if (!reviewsList) return;

            if (reviews.length === 0) {
                reviewsList.innerHTML = `
                    <div class="no-reviews text-center py-4">
                        <p>Chưa có đánh giá nào cho dịch vụ này.</p>
                        <p class="text-muted">Hãy là người đầu tiên đánh giá dịch vụ này!</p>
                    </div>
                `;
                return;
            }

            const reviewsHTML = reviews.map(review => this.createReviewHTML(review)).join('');
            reviewsList.innerHTML = reviewsHTML;

            // Animate reviews
            this.animateReviews();
        }

        createReviewHTML(review) {
            const stars = '⭐'.repeat(review.rating) + '☆'.repeat(5 - review.rating);

            return `
                <div class="review-item border-bottom pb-3 mb-3" style="opacity: 0; transform: translateY(20px);">
                    <div class="review-header d-flex justify-content-between">
                        <div>
                            <strong>${this.escapeHtml(review.customer_name)}</strong>
                            <div class="review-stars">${stars}</div>
                        </div>
                        <small class="text-muted">${review.date}</small>
                    </div>
                    <div class="review-content mt-2">
                        <p>${this.escapeHtml(review.comment)}</p>
                    </div>
                </div>
            `;
        }

        animateReviews() {
            const reviewItems = document.querySelectorAll('.review-item');
            reviewItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }

        /**
         * Khởi tạo lazy loading cho hình ảnh
         */
        initLazyLoading() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src || img.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        }

        /**
         * Khởi tạo scroll effects
         */
        initScrollEffects() {
            window.addEventListener('scroll', this.throttle(() => {
                this.handleScrollEffects();
            }, 100));
        }

        handleScrollEffects() {
            // Sticky tab navigation
            const tabsSection = document.querySelector('.product-tabs');
            const tabs = document.querySelector('.tabs');

            if (tabsSection && tabs) {
                const rect = tabsSection.getBoundingClientRect();
                if (rect.top <= 0 && rect.bottom > 0) {
                    tabs.classList.add('sticky-tabs');
                } else {
                    tabs.classList.remove('sticky-tabs');
                }
            }
        }

        /**
         * Utility functions
         */
        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        throttle(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        new DetailServiceManager();
    });

    // Add sticky tabs CSS
    const stickyTabsCSS = `
        .sticky-tabs {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0 15px;
        }

        .sticky-tabs .tabs {
            max-width: 1200px;
            margin: 0 auto;
        }
    `;

    // Inject CSS
    const style = document.createElement('style');
    style.textContent = stickyTabsCSS;
    document.head.appendChild(style);
</script>
<?php end_section() ?>