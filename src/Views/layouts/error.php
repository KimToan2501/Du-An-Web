<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? '403 - Truy cập bị từ chối' ?></title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #333;
    }

    .error-container {
      background: white;
      padding: 60px 40px;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 600px;
      width: 90%;
      animation: fadeInUp 0.8s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .error-icon {
      font-size: 120px;
      color: #e67e22;
      margin-bottom: 30px;
      animation: shake 1.5s ease-in-out infinite;
    }

    @keyframes shake {

      0%,
      100% {
        transform: translateX(0);
      }

      10%,
      30%,
      50%,
      70%,
      90% {
        transform: translateX(-5px);
      }

      20%,
      40%,
      60%,
      80% {
        transform: translateX(5px);
      }
    }

    .error-code {
      font-size: 72px;
      font-weight: bold;
      color: #e67e22;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .error-title {
      font-size: 28px;
      margin-bottom: 20px;
      color: #2c3e50;
      font-weight: 600;
    }

    .error-message {
      font-size: 16px;
      color: #7f8c8d;
      margin-bottom: 40px;
      line-height: 1.6;
    }

    .btn-group {
      display: flex;
      gap: 15px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      padding: 12px 30px;
      text-decoration: none;
      border-radius: 25px;
      font-weight: 500;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .btn-primary {
      background: linear-gradient(45deg, #e67e22, #d35400);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(230, 126, 34, 0.4);
    }

    .btn-secondary {
      background: transparent;
      color: #7f8c8d;
      border: 2px solid #bdc3c7;
    }

    .btn-secondary:hover {
      background: #ecf0f1;
      border-color: #95a5a6;
    }
  </style>
</head>

<body>
  <div class="error-container">
    <?= $content ?>
  </div>
</body>

</html>