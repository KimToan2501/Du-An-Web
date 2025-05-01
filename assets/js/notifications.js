document.addEventListener('DOMContentLoaded', function () {
  // === 1. Hiệu ứng click các nút trong mỗi thông báo ===
  const buttons = document.querySelectorAll('.notification-btn');
  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const label = btn.textContent.trim();
      if (label === 'Xác nhận') {
        btn.textContent = '✓ Đã xác nhận';
        btn.disabled = true;
        btn.style.backgroundColor = '#28a745';
        btn.style.color = '#fff';
        btn.style.borderColor = '#28a745';
      } else if (label === 'Xem chi tiết') {
        alert('Chuyển đến trang chi tiết đơn hàng!');
      }
    });
  });

  // === 2. PHÂN TRANG - PAGINATION ===

  // Chọn tất cả các item thông báo (mỗi dòng)
  const notifications = document.querySelectorAll('.notification-item');

  // Chọn các nút số trang (trừ prev & next)
  const paginationLinks = document.querySelectorAll('.pagination a:not(.prev):not(.next)');

  // Chọn nút chuyển trang ← và →
  const prevBtn = document.querySelector('.pagination .prev');
  const nextBtn = document.querySelector('.pagination .next');

  const itemsPerPage = 5; // Số lượng hiển thị mỗi trang
  let currentPage = 1;     // Trang hiện tại mặc định là 1

  // Hàm hiển thị các item thông báo của trang hiện tại
  function showPage(pageNumber) {
    const totalPages = Math.ceil(notifications.length / itemsPerPage);

    // Ràng buộc trang không vượt quá giới hạn
    if (pageNumber < 1) pageNumber = 1;
    if (pageNumber > totalPages) pageNumber = totalPages;

    currentPage = pageNumber;

    // Tính vị trí bắt đầu và kết thúc của item trong danh sách
    const start = (pageNumber - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    // Hiển thị đúng các item nằm trong khoảng từ start đến end
    notifications.forEach((item, index) => {
      item.style.display = (index >= start && index < end) ? 'flex' : 'none';
    });

    // Cập nhật trạng thái active cho pagination
    paginationLinks.forEach(link => {
      const page = parseInt(link.textContent.trim());
      if (page === pageNumber) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  }

  // Sự kiện click vào các số trang
  paginationLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const page = parseInt(link.textContent.trim());
      if (!isNaN(page)) {
        showPage(page);
      }
    });
  });

  // Sự kiện click nút ← để lùi trang
  if (prevBtn) {
    prevBtn.addEventListener('click', function (e) {
      e.preventDefault();
      showPage(currentPage - 1);
    });
  }

  // Sự kiện click nút → để sang trang
  if (nextBtn) {
    nextBtn.addEventListener('click', function (e) {
      e.preventDefault();
      showPage(currentPage + 1);
    });
  }

  // Gọi hàm để hiển thị trang đầu tiên khi vừa load xong
  showPage(1);
});
