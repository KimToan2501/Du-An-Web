<div class="error-icon">⚠️</div>
<div class="error-code">500</div>
<h1 class="error-title">Lỗi máy chủ nội bộ</h1>
<p class="error-message">
  <?= htmlspecialchars($message ?? 'Đã xảy ra lỗi máy chủ nội bộ. Chúng tôi đang khắc phục sự cố này. Vui lòng thử lại sau ít phút.') ?>
</p>
<div class="btn-group">
  <a href="/" class="btn btn-primary">🏠 Về trang chủ</a>
  <a href="javascript:location.reload()" class="btn reload-btn">🔄 Tải lại trang</a>
  <a href="javascript:history.back()" class="btn btn-secondary">← Quay lại</a>
</div>