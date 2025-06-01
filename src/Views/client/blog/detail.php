<?php start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('meta'); ?>
<meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
<meta name="keywords" content="<?= htmlspecialchars($meta_keywords) ?>">
<meta property="og:title" content="<?= htmlspecialchars($og_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($og_description) ?>">
<meta property="og:type" content="article">
<meta property="og:url" content="<?= $canonical_url ?>">
<meta property="og:image" content="<?= $og_image ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="canonical" href="<?= $canonical_url ?>">
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/blog-detail.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/blog-list.css') ?>">
<?php end_section(); ?>

<main class="blog-detail-main">
  <!-- Breadcrumb -->
  <section class="breadcrumb-section">
    <div class="container">
      <nav class="breadcrumb">
        <a href="<?= base_url() ?>">Trang chủ</a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= base_url('blog') ?>">Blog</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?= htmlspecialchars($blog->title) ?></span>
      </nav>
    </div>
  </section>

  <!-- Article Content -->
  <article class="blog-article">
    <div class="container">
      <div class="article-content">
        <!-- Article Header -->
        <header class="article-header">
          <h1 class="article-title"><?= htmlspecialchars($blog->title) ?></h1>

          <div class="article-meta">
            <div class="meta-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12,6 12,12 16,14"></polyline>
              </svg>
              <span><?= $blog->getFormattedDate('d/m/Y H:i') ?></span>
            </div>

            <div class="meta-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <span><?= number_format($blog->view_count) ?> lượt xem</span>
            </div>
          </div>
        </header>

        <!-- Featured Image -->
        <?php if ($blog->featured_image): ?>
          <div class="article-featured-image">
            <img
              src="<?= $blog->getFeaturedImageUrl() ?>"
              alt="<?= htmlspecialchars($blog->title) ?>"
              loading="lazy">
          </div>
        <?php endif; ?>

        <!-- Article Body -->
        <div class="article-body">
          <?= $blog->content ?>
        </div>

        <!-- Article Footer -->
        <footer class="article-footer">
          <div class="article-actions">
            <button class="share-btn" onclick="shareArticle()">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
              </svg>
              Chia sẻ
            </button>
          </div>
        </footer>
      </div>

      <!-- Sidebar -->
      <aside class="article-sidebar">
        <!-- Back to Blog -->
        <div class="sidebar-card">
          <a href="<?= base_url('blog') ?>" class="back-to-blog">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="15,18 9,12 15,6"></polyline>
            </svg>
            Quay lại Blog
          </a>
        </div>

        <!-- Table of Contents (if content has headings) -->
        <div class="sidebar-card toc-card" id="tableOfContents" style="display: none;">
          <h3 class="sidebar-title">Mục lục</h3>
          <div class="toc-list" id="tocList"></div>
        </div>
      </aside>
    </div>
  </article>

  <!-- Related Articles -->
  <?php if (!empty($related_blogs)): ?>
    <section class="related-articles">
      <div class="container">
        <h2 class="section-title">Bài viết liên quan</h2>

        <div class="row g-4 mb-5">
          <?php foreach ($related_blogs as $related): ?>
            <div class="col-lg-4 col-md-6">
              <article class="blog-card h-100">
                <div class="blog-card-image">
                  <img
                    src="<?= $related->getFeaturedImageUrl() ?>"
                    alt="<?= htmlspecialchars($related->title) ?>"
                    class="card-img-top"
                    loading="lazy">
                  <div class="blog-card-date">
                    <?= $related->getFormattedDate('d/m/Y') ?>
                  </div>
                </div>

                <div class="card-body d-flex flex-column">
                  <h3 class="blog-card-title">
                    <a href="<?= $related->getUrl() ?>" class="text-decoration-none">
                      <?= htmlspecialchars($related->title) ?>
                    </a>
                  </h3>

                  <p class="blog-card-excerpt flex-grow-1">
                    <?= htmlspecialchars($related->getExcerpt(120)) ?>
                  </p>

                  <div class="blog-card-meta mb-3 d-flex justify-content-between align-items-center">
                    <span class="blog-views">
                      <i class="fas fa-eye me-1"></i>
                      <?= number_format($related->view_count) ?> lượt xem
                    </span>

                    <a href="<?= $related->getUrl() ?>" class="blog-card-btn btn btn-sm">
                      Đọc tiếp
                      <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                  </div>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>

<script>
  // Share functionality
  function shareArticle() {
    if (navigator.share) {
      navigator.share({
        title: document.title,
        url: window.location.href
      });
    } else {
      // Fallback - copy to clipboard
      navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Đã sao chép link bài viết!');
      });
    }
  }

  // Generate Table of Contents
  document.addEventListener('DOMContentLoaded', function() {
    const headings = document.querySelectorAll('.article-body h2, .article-body h3');
    const tocContainer = document.getElementById('tableOfContents');
    const tocList = document.getElementById('tocList');

    if (headings.length > 0) {
      tocContainer.style.display = 'block';

      headings.forEach((heading, index) => {
        // Add ID to heading
        const id = 'heading-' + index;
        heading.id = id;

        // Create TOC link
        const link = document.createElement('a');
        link.href = '#' + id;
        link.textContent = heading.textContent;
        link.style.paddingLeft = heading.tagName === 'H3' ? '15px' : '0';

        tocList.appendChild(link);
      });
    }
  });
</script>