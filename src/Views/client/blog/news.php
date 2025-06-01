<?php start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('meta'); ?>
<meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
<meta name="keywords" content="thú cưng, chăm sóc thú cưng, blog thú cưng, PawSpa">
<meta property="og:title" content="<?= htmlspecialchars($title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= base_url('blog') ?>">
<meta property="og:image" content="<?= base_url('assets/images/og-blog.jpg') ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="canonical" href="<?= base_url('blog') ?>">
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/blog-list.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/blog.css') ?>">
<?php end_section(); ?>


<main>
  <div class="dog-frame1">
    <img src="<?= base_url('/assets/images/blog/dog-frame1.png') ?>" alt="dog-frame1" class="dog-frame1">
  </div>
  <div class="frame2">
    <img src="<?= base_url('/assets/images/blog/base-frame2.png') ?>" alt="base-frame2" class="base-frame2">
    <div class="div-h1-frame2">
      <h1 class="h1-frame2">NUÔI THÚ CƯNG CẦN GÌ: DANH SÁCH ĐẦY ĐỦ CHO NGƯỜI MỚI BẮT ĐẦU</h1>
    </div>
    <div class="container">

      <img src="<?= base_url('/assets/images/blog/dog-frame2.png') ?>" alt="Chó dễ thương" class="dog-image" />


      <div class="content">


        <h2>Tìm hiểu về đặc điểm của từng loài</h2>
        <p>Khi nuôi thú cưng, việc nghiên cứu và lựa chọn thú cưng phù hợp là bước đầu tiên quan trọng. Tìm hiểu về đặc điểm của từng loài để biết bạn nuôi thú cưng cần gì.</p>
        <ul>
          <li>Đặc tính: Hiếu động, tính cam, độc lập,...</li>
          <li>Kích thước: Nhỏ, vừa, lớn.</li>
          <li>Mức độ rụng lông: Ít, trung bình, nhiều.</li>
          <li>Nhu cầu vận động: Cao, trung bình, thấp.</li>
          <li>Khả năng hòa nhập: Với trẻ em, vật nuôi khác.</li>
        </ul>

        <h2>Cân nhắc điều kiện sống</h2>
        <ul>
          <li>Không gian sống: Bạn có đủ không gian cho thú cưng thoải mái vận động?</li>
          <li>Thời gian chăm sóc: Bạn có thể dành đủ thời gian cho việc cho ăn, dọn dẹp, chơi đùa và đưa đi dạo?</li>
          <li>Ngân sách: Bạn có đủ khả năng chi trả cho thức ăn, vật dụng, dịch vụ chăm sóc sức khỏe?</li>
        </ul>
      </div>

    </div>
  </div>
</main>

<main class="blog-main">
  <!-- Header Section -->
  <section class="blog-header">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h1 class="blog-header-title">Blog & Tin Tức</h1>
          <p class="blog-header-subtitle">Khám phá những kiến thức hữu ích về chăm sóc thú cưng</p>

          <!-- Search Form -->
          <div class="blog-search mt-4">
            <form action="<?= base_url('blog/search') ?>" method="GET" class="search-form">
              <div class="search-input-group">
                <input
                  type="text"
                  name="q"
                  placeholder="Tìm kiếm bài viết..."
                  value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                  class="search-input form-control">
                <button type="submit" class="search-btn btn">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog Grid Section -->
  <section class="blog-grid-section py-5">
    <div class="container">
      <?php if (empty($blogs)): ?>
        <div class="row justify-content-center">
          <div class="col-lg-6">
            <div class="no-blogs text-center">
              <div class="no-blogs-icon mb-3">📝</div>
              <h3>Chưa có bài viết nào</h3>
              <p>Hãy quay lại sau để đọc những bài viết mới nhất!</p>
            </div>
          </div>
        </div>
      <?php else: ?>
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
                      <?= htmlspecialchars($blog->title) ?>
                    </a>
                  </h3>

                  <p class="blog-card-excerpt flex-grow-1">
                    <?= htmlspecialchars($blog->getExcerpt(120)) ?>
                  </p>

                  <div class="blog-card-meta mb-3 d-flex justify-content-between align-items-center">
                    <span class="blog-views">
                      <i class="fas fa-eye me-1"></i>
                      <?= number_format($blog->view_count) ?> lượt xem
                    </span>

                    <a href="<?= $blog->getUrl() ?>" class="blog-card-btn btn btn-sm">
                      Đọc tiếp
                      <i class="fas fa-arrow-right ms-2"></i>
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
              <nav class="pagination-wrapper" aria-label="Blog pagination">
                <div class="pagination-info text-center mb-3">
                  Trang <?= $pagination['current_page'] ?> / <?= $pagination['total_pages'] ?>
                  (<?= number_format($pagination['total']) ?> bài viết)
                </div>

                <ul class="pagination justify-content-center">
                  <?php if ($pagination['has_prev']): ?>
                    <li class="page-item">
                      <a class="page-link" href="<?= base_url('blog?page=' . $pagination['prev_page']) ?>">
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
                      <a class="page-link" href="<?= base_url('blog?page=' . $i) ?>">
                        <?= $i ?>
                      </a>
                    </li>
                  <?php endfor; ?>

                  <?php if ($pagination['has_next']): ?>
                    <li class="page-item">
                      <a class="page-link" href="<?= base_url('blog?page=' . $pagination['next_page']) ?>">
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