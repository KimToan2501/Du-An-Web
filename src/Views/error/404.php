<div class="error-icon">🔍</div>
<div class="error-code">404</div>
<h1 class="error-title">Oops! Trang không tìm thấy</h1>
<p class="error-message">
  <?= htmlspecialchars($message ?? 'Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc đã được di chuyển.') ?>
</p>
<div class="btn-group">
  <a href="/" class="btn btn-primary">🏠 Về trang chủ</a>
  <a href="javascript:history.back()" class="btn btn-secondary">← Quay lại</a>
</div>