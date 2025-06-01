<?php start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('meta'); ?>
<meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
<meta name="keywords" content="tìm kiếm blog, thú cưng, chăm sóc thú cưng, PawSpa">
<meta property="og:title" content="<?= htmlspecialchars($title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= base_url('blog/search?q=' . urlencode($keyword)) ?>">
<meta property="og:image" content="<?= base_url('assets/images/og-blog.jpg') ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="canonical" href="<?= base_url('blog/search?q=' . urlencode($keyword)) ?>">
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/blog-search.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/blog-list.css') ?>">
<?php end_section(); ?>

<main class="blog-search-main">
  <!-- Search Header -->
  <section class="search-header">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h1 class="search-title">
            Kết quả tìm kiếm cho:
            <span class="search-keyword">"<?= htmlspecialchars($keyword) ?>"</span>
          </h1>

          <div class="search-stats mb-4">
            <?php if ($pagination['total'] > 0): ?>
              Tìm thấy <strong><?= number_format($pagination['total']) ?></strong> bài viết
            <?php else: ?>
              Không tìm thấy kết quả nào
            <?php endif; ?>
          </div>

          <!-- Search Form -->
          <div class="search-form-wrapper">
            <form action="<?= base_url('blog/search') ?>" method="GET" class="search-form">
              <div class="search-input-group">
                <input
                  type="text"
                  name="q"
                  placeholder="Nhập từ khóa tìm kiếm..."
                  value="<?= htmlspecialchars($keyword) ?>"
                  class="search-input form-control"
                  required>
                <button type="submit" class="search-btn btn">
                  <i class="fas fa-search me-2"></i>
                  <!-- Tìm kiếm -->
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Search Results -->
  <section class="search-results-section py-5">
    <div class="container">
      <?php if (empty($blogs)): ?>
        <!-- No Results -->
        <div class="row justify-content-center">
          <div class="col-lg-6">
            <div class="no-results text-center">
              <div class="no-results-icon mb-4">
                <i class="fas fa-search" style="font-size: 4rem; color: #cbd5e1;"></i>
              </div>

              <h3 class="no-results-title">Không tìm thấy kết quả</h3>
              <p class="no-results-text">
                Rất tiếc, chúng tôi không tìm thấy bài viết nào khớp với từ khóa
                "<strong><?= htmlspecialchars($keyword) ?></strong>".
              </p>

              <div class="search-suggestions">
                <h4>Gợi ý tìm kiếm:</h4>
                <ul class="list-unstyled">
                  <li><i class="fas fa-check-circle text-primary me-2"></i>Kiểm tra lại chính tả từ khóa</li>
                  <li><i class="fas fa-check-circle text-primary me-2"></i>Thử sử dụng từ khóa khác hoặc tổng quát hơn</li>
                  <li><i class="fas fa-check-circle text-primary me-2"></i>Tìm kiếm với ít từ khóa hơn</li>
                </ul>
              </div>

              <div class="search-actions mt-4">
                <a href="<?= base_url('blog') ?>" class="btn btn-primary btn-lg">
                  <i class="fas fa-arrow-left me-2"></i>
                  Xem tất cả bài viết
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <!-- Results Grid -->
        <div class="row g-4 mb-5">
          <?php foreach ($blogs as $blog): ?>
            <div class="col-lg-4 col-md-6">
              <article class="blog-card h-100">
                <div class="blog-card-image">
                  <img
                    src="<?= $blog->getFeaturedImageUrl() ?>"
                    alt="<?= htmlspecialchars($blog->title) ?>"
                    class="card-img-top"
                    loading="lazy">
                  <div class="blog-card-date">
                    <?= $blog->getFormattedDate('d/m/Y') ?>
                  </div>
                </div>

                <div class="card-body d-flex flex-column">
                  <h3 class="blog-card-title">
                    <a href="<?= $blog->getUrl() ?>" class="text-decoration-none">
                      <?= highlightSearchKeyword($blog->title, $keyword) ?>
                    </a>
                  </h3>

                  <p class="blog-card-excerpt flex-grow-1">
                    <?= highlightSearchKeyword($blog->getExcerpt(150), $keyword) ?>
                  </p>

                  <div class="blog-card-meta d-flex justify-content-between align-items-center">
                    <span class="blog-views">
                      <i class="fas fa-eye me-1"></i>
                      <?= number_format($blog->view_count) ?> lượt xem
                    </span>

                    <a href="<?= $blog->getUrl() ?>" class="blog-card-btn btn btn-sm">
                      Đọc tiếp
                      <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                  </div>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
          <div class="row">
            <div class="col-12">
              <nav class="search-pagination" aria-label="Search results pagination">
                <div class="pagination-info text-center mb-3">
                  Trang <?= $pagination['current_page'] ?> / <?= $pagination['total_pages'] ?>
                  (<?= number_format($pagination['total']) ?> kết quả)
                </div>

                <ul class="pagination justify-content-center">
                  <?php if ($pagination['has_prev']): ?>
                    <li class="page-item">
                      <a class="page-link" href="<?= base_url('blog/search?q=' . urlencode($keyword) . '&page=' . $pagination['prev_page']) ?>">
                        <i class="fas fa-chevron-left me-1"></i>
                        Trước
                      </a>
                    </li>
                  <?php endif; ?>

                  <?php
                  $start = max(1, $pagination['current_page'] - 2);
                  $end = min($pagination['total_pages'], $pagination['current_page'] + 2);

                  for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                      <a class="page-link" href="<?= base_url('blog/search?q=' . urlencode($keyword) . '&page=' . $i) ?>">
                        <?= $i ?>
                      </a>
                    </li>
                  <?php endfor; ?>

                  <?php if ($pagination['has_next']): ?>
                    <li class="page-item">
                      <a class="page-link" href="<?= base_url('blog/search?q=' . urlencode($keyword) . '&page=' . $pagination['next_page']) ?>">
                        Sau
                        <i class="fas fa-chevron-right ms-1"></i>
                      </a>
                    </li>
                  <?php endif; ?>
                </ul>
              </nav>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php
// Helper function để highlight keyword trong text
function highlightSearchKeyword($text, $keyword)
{
  if (empty($keyword)) return htmlspecialchars($text);

  $highlighted = preg_replace(
    '/(' . preg_quote($keyword, '/') . ')/iu',
    '<mark class="search-highlight">$1</mark>',
    htmlspecialchars($text)
  );

  return $highlighted;
}
?>