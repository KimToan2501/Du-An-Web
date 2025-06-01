<?php
// Kiểm tra xem biến $pagination có được truyền vào không
$pagination = isset($pagination) ? $pagination : [];

// Lấy các giá trị từ mảng pagination
$current = isset($pagination['current']) ? (int)$pagination['current'] : 1;
$last = isset($pagination['last']) ? (int)$pagination['last'] : 1;
$total = isset($pagination['total']) ? (int)$pagination['total'] : 0;

// Tính toán các trang hiển thị
$start = max(1, $current - 2);
$end = min($last, $current + 2);

// URL hiện tại để tạo link pagination
$currentUrl = $_SERVER['REQUEST_URI'];
$urlParts = parse_url($currentUrl);
$baseUrl = $urlParts['path'];

// Function để tạo URL với page parameter
function createPageUrl($baseUrl, $page) {
    $queryParams = $_GET;
    $queryParams['page'] = $page;
    return $baseUrl . '?' . http_build_query($queryParams);
}
?>

<?php if ($last > 1): ?>
<!-- Start: Phân trang -->
<div id="pagination">
    <!-- Nút Previous -->
    <?php if ($current > 1): ?>
        <a href="<?= createPageUrl($baseUrl, $current - 1) ?>" class="pagination__btn">
            <i class="fa-solid fa-angle-left"></i>
        </a>
    <?php else: ?>
        <button class="pagination__btn pagination__btn--disabled">
            <i class="fa-solid fa-angle-left"></i>
        </button>
    <?php endif; ?>

    <!-- Trang đầu tiên -->
    <?php if ($start > 1): ?>
        <a href="<?= createPageUrl($baseUrl, 1) ?>" class="pagination__btn" data-page="1">1</a>
        <?php if ($start > 2): ?>
            <button class="pagination__btn">...</button>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Các trang ở giữa -->
    <?php for ($i = $start; $i <= $end; $i++): ?>
        <?php if ($i == $current): ?>
            <button class="pagination__btn pagination__btn--active" data-page="<?= $i ?>"><?= $i ?></button>
        <?php else: ?>
            <a href="<?= createPageUrl($baseUrl, $i) ?>" class="pagination__btn" data-page="<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- Trang cuối cùng -->
    <?php if ($end < $last): ?>
        <?php if ($end < $last - 1): ?>
            <button class="pagination__btn">...</button>
        <?php endif; ?>
        <a href="<?= createPageUrl($baseUrl, $last) ?>" class="pagination__btn" data-page="<?= $last ?>"><?= $last ?></a>
    <?php endif; ?>

    <!-- Nút Next -->
    <?php if ($current < $last): ?>
        <a href="<?= createPageUrl($baseUrl, $current + 1) ?>" class="pagination__btn">
            <i class="fa-solid fa-angle-right"></i>
        </a>
    <?php else: ?>
        <button class="pagination__btn pagination__btn--disabled">
            <i class="fa-solid fa-angle-right"></i>
        </button>
    <?php endif; ?>
</div>
<!-- End: Phân trang -->
<?php endif; ?>