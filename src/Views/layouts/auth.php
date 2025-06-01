<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= section('title', 'Client') ?></title>

  <!-- CSS - Library - Start -->
  <?php include_partial('head') ?>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


  <!-- CSS dùng chung -->
  <link rel="stylesheet" href="<?= base_url('assets/css/reset.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/common.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/header.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/footer.css') ?>">

  <?php section('links') ?>
</head>

<body>

  <!-- Start: Header -->
  <?php include_partial('client/header') ?>
  <!-- End: Header -->

  <!-- Start: Main content -->
  <main class="pawspa__container">
    <div class="pawspa-form__wrapper">
      <?= $content ?>
    </div>
  </main>
  <!-- End: Main content -->

  <!-- Start: Footer -->
  <?php include_partial('client/footer') ?>
  <!-- End: Footer -->

  <!-- Link file JS -->

  <?php include_partial('foot') ?>
  <?php section('scripts') ?>
</body>

</html>