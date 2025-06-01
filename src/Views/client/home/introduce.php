<!-- Pet Knowledge Section -->
<?php start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('/assets/css/introduce.css') ?>">
<?php end_section(); ?>

<section class="pet-knowledge-section">
  <div class="pet-top-gallery">
    <img src="<?= base_url('/assets/images/pic/Rectangle 182.svg') ?>" alt="Pet 1" class="pet-img rounded">
    <img src="<?= base_url('/assets/images/pic/Rectangle 183.svg') ?>" alt="Pet 2" class="pet-img rounded-xl">
    <img src="<?= base_url('/assets/images/pic/Rectangle 184.svg') ?>" alt="Pet 3" class="pet-img rounded">
  </div>
  <div class="pet-knowledge-content">
    <div class="pet-knowledge-left">
      <img src="<?= base_url('/assets/images/pic/pngwing.com (2).svg') ?>" alt="Dog and cat" class="pet-large-img">
    </div>
    <div class="pet-knowledge-right">
      <h2 class="pet-title">Bạn có thực sự hiểu biết về thú cưng của bạn?</h2>
      <ul class="pet-list">
        <li>Hiểu về thú cưng không chỉ dừng lại ở việc biết giống loài hay cho ăn hằng ngày, mà còn là sự thấu hiểu những hành vi, nhu cầu và sở thích riêng của chúng.</li>
        <li>Mỗi thú cưng đều có cá tính và cách giao tiếp khác nhau—một chú chó vẫy đuôi khi vui mừng, một chú mèo rừ rừ thể hiện sự thoải mái, hay một chú chim cất tiếng hót đầy hứng khởi.</li>
        <li>Bằng cách quan sát và lắng nghe những tín hiệu này, bạn có thể chăm sóc thú cưng tốt hơn cả về mặt thể chất lẫn tinh thần. Điều đó cũng đồng nghĩa với việc theo dõi sức khỏe, nhận biết dấu hiệu bệnh tật và đảm bảo chúng được chăm sóc thú y kịp thời.</li>
        <li>Xây dựng một mối quan hệ bền chặt thông qua vui chơi, huấn luyện và tình yêu thương sẽ giúp cả bạn và thú cưng có một cuộc sống hạnh phúc, khỏe mạnh và tràn đầy niềm vui.</li>
      </ul>
    </div>
  </div>

  <!-- Company Info Section -->
  <div class="company-info">
    <div class="company-left">
      <h2 class="company-title">CÔNG TY CỦA CHÚNG TÔI</h2>
      <div class="company-about">
        <h4>Về chúng tôi</h4>
        <p>Với 10 năm kinh nghiệm trong lĩnh vực chăm sóc thú cưng cùng đội ngũ bác sĩ thú y đầy chuyên môn, chúng tôi tự tin mang đến sự bảo vệ tối ưu cho thú cưng của bạn, từ phòng tránh bệnh tật đến chế độ dinh dưỡng và khẩu phần ăn hàng ngày.</p>
        <p>Yêu thương và nuông chiều thú cưng là chưa đủ, sức khỏe của chúng cũng cần được quan tâm đúng mức. Hãy để chúng tôi trở thành nơi bạn có thể hoàn toàn tin tưởng trong hành trình chăm sóc người bạn nhỏ của mình!</p>
      </div>
      <div class="company-consult">
        <h4>Tư vấn</h4>
        <p>Mỗi loài thú cưng đều có môi trường sống và tập tính khác nhau, vì vậy hãy gặp gỡ và tham khảo ý kiến trực tiếp từ chuyên gia động vật để có sự chăm sóc tốt nhất. 🐾</p>
      </div>
      <h4>Dịch vụ của chúng tôi</h4>
      <div class="service-cards">
        <div class="service-card service-green">
          <div class="service-info">
            <h5>Kiểm tra sức khỏe tổng quát</h5>
            <p>Kiểm soát sức khỏe định kỳ và tư vấn quan trọng</p>
            <button class="read-more-btn">Read More</button>
          </div>
          <img src="<?= base_url('/assets/images/pic/pngwing.com (8).svg') ?>" alt="Service 1">
        </div>
        <div class="service-card service-purple">
          <div class="service-info">
            <h5>Spa</h5>
            <p>Thư giãn cho cún cưng, chăm sóc toàn diện một cách nhẹ nhàng...</p>
            <button class="read-more-btn">Read More</button>
          </div>
          <img src="<?= base_url('/assets/images/pic/pngwing.com (8) (1).svg') ?>" alt="Service 2">
        </div>
      </div>
    </div>
    <div class="company-right">
      <div class="company-dog-circle">
        <img src="<?= base_url('/assets/images/pic/image 3.svg') ?>" alt="Big dog" class="big-dog-img">
      </div>
    </div>
  </div>
</section>