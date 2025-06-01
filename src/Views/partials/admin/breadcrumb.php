<?php
// Kiểm tra xem biến $title có được truyền vào không, nếu không thì đặt giá trị mặc định
$title = isset($title) ? $title : 'Tiêu đề mặc định';

// Kiểm tra xem biến $breadcrumbs có được truyền vào không, nếu không thì đặt giá trị mặc định là một mảng rỗng
$breadcrumbs = isset($breadcrumbs) ? $breadcrumbs : [];
?>

<!-- Start: Tiêu đề & Breadcrumb -->
<div class="admin-main__header">
  <h2 class="admin-main__title"><?= htmlspecialchars($title) ?></h2>
  <!-- Start: Breadcrumb -->
  <nav class="breadcrumb">
    <?php foreach ($breadcrumbs as $index => $crumb): ?>
      <?php if ($index > 0): ?>
        <span class="breadcrumb__separator">/</span>
      <?php endif; ?>
      <?php if (isset($crumb['url']) && $crumb['url']): ?>
        <a href="<?= htmlspecialchars(base_url($crumb['url'])) ?>" class="breadcrumb__link"><?= htmlspecialchars($crumb['text']) ?></a>
      <?php else: ?>
        <span class="breadcrumb__link breadcrumb__link--active"><?= htmlspecialchars($crumb['text']) ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>
  <!-- End: Breadcrumb -->
</div>
<!-- End: Tiêu đề & Breadcrumb -->