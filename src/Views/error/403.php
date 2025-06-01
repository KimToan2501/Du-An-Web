<div class="error-icon">🚫</div>
<div class="error-code">403</div>
<h1 class="error-title">Truy cập bị từ chối</h1>
<p class="error-message">
  <?= htmlspecialchars($message ?? 'Bạn không có quyền truy cập vào tài nguyên này. Vui lòng liên hệ quản trị viên nếu bạn cho rằng đây là lỗi.') ?>
</p>
<div class="btn-group">
  <a href="/" class="btn btn-primary">🏠 Về trang chủ</a>
  <a href="/login" class="btn btn-secondary">🔐 Đăng nhập</a>
</div>