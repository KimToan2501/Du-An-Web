<?php start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<main class="container py-5">
  <div class="row g-5"> <!-- Dòng với khoảng cách giữa các cột là 5 -->
    <!-- Cột trái - Form liên hệ -->
    <div class="col-lg-6">
      <h2 class="fs-2 fw-bold text-purple mb-4">BẠN CẦN HỖ TRỢ ?</h2>
      <form class="d-flex flex-column gap-3"> <!-- Form dạng flex column với khoảng cách 3 -->
        <input class="form-control p-3" placeholder="Tên" type="text" />
        <input class="form-control p-3" placeholder="Số điện thoại" type="text" />
        <input class="form-control p-3" placeholder="Email" type="email" />
        <textarea class="form-control p-3" placeholder="Lời nhắn" rows="4"></textarea>
        <div class="d-flex gap-3"> <!-- Nhóm nút -->
          <button class="btn btn-purple px-4 py-2" type="submit">Gửi</button>
          <button class="btn btn-light text-dark px-4 py-2" type="reset">Làm lại</button>
        </div>
      </form>
    </div>

    <!-- Cột phải - Hình ảnh và mô tả -->
    <div class="col-lg-6">
      <div class="image-container position-relative">
        <img alt="Cat" class="img-fluid rounded mb-3" src="<?= base_url('/assets/images/pic/Rectangle 397.png') ?>" />
        <div class="text-overlay">
          <p class="">
            Chúng tôi quan tâm đến nhu cầu của thú cưng của bạn! Hãy để lại thông tin liên lạc của bạn và chúng tôi sẽ cập nhật cho bạn những ưu đãi tốt nhất về dịch vụ cho thú cưng. Đừng bỏ lỡ cơ hội cung cấp những thứ tốt nhất cho những bé pet của bạn! </p>
        </div>
      </div>

    </div>
  </div>
</main>

<?php start_section('links') ?>
<link rel="stylesheet" href="<?= base_url('/assets/css/contact.css')?>">

<style>
  /* Button styles */
  .header-nav-link {
    font-weight: bold;
    font-size: 18px;
    line-height: 22px;
    color: #999999;
    padding: 0 16px;
    transition: color 0.2s ease-in-out;
  }

  .btn-purple {
    background-color: var(--primary-purple-dark);
    color: white;
  }

  .btn-purple:hover {
    background-color: #350060;
    color: white;
  }

  /* Text color */
  .text-purple {
    color: var(--primary-purple-dark);
  }

  /* Footer background */
</style>
<?php end_section() ?>