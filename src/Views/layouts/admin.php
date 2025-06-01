<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= section('title', 'Admin') ?></title>

  <?php include_partial('head') ?>

  <link rel="stylesheet" href="<?= base_url('cms/assets/css/admin-common.css') ?>">

  <?php section('links') ?>
</head>

<body>
  <!-- Header -->
  <?php include_partial('admin/header') ?>

  <!-- Start: Sidebar -->
  <?php include_partial('admin/sidebar') ?>
  <!-- End: Sidebar -->

  <!-- Start: Main content -->
  <main class="admin-main">
    <div class="admin-main__container">
      <?= $content ?>
    </div>
  </main>
  <!-- End: Main content -->

  <!-- Start: Modal (popup) dùng để xác nhận các hành động -->
  <?php section('popup') ?>
  <!-- End: Modal (popup) dùng để xác nhận các hành động -->

  <?php include_partial('foot') ?>
  
  <!-- Link file JS -->
  <?php section('scripts') ?>

</body>

</html>