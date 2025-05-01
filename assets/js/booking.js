// booking.js
document.addEventListener('DOMContentLoaded', function () {
  // === 1. Chức năng lọc theo tab trạng thái ===

  const tabList = document.querySelectorAll('.booking-tabs li'); // các tab trạng thái
  const dataRows = document.querySelectorAll('.booking-row:not(.booking-header)'); // các dòng dữ liệu (không bao gồm header)

  tabList.forEach(function (tab) {
    tab.addEventListener('click', function () {
      // Bỏ class 'active' ở tất cả các tab
      tabList.forEach(t => t.classList.remove('active'));
      tab.classList.add('active'); // đánh dấu tab được chọn

      const selectedStatus = tab.textContent.trim(); // Lấy tên trạng thái đang chọn

      // Lặp qua từng dòng đơn hàng để kiểm tra trạng thái
      dataRows.forEach(function (row) {
        const badge = row.querySelector('.status'); // lấy ô trạng thái trong dòng đó
        const rowStatus = badge ? badge.textContent.trim() : '';

        // Hiển thị dòng nếu trạng thái trùng, hoặc nếu chọn "Tất cả"
        if (selectedStatus === 'Tất cả' || rowStatus === selectedStatus) {
          row.style.display = 'grid';
        } else {
          row.style.display = 'none';
        }
      });
    });
  });

  // === 2. Chức năng tìm kiếm theo từ khoá ===

  const searchBox = document.querySelector('.search-input');

  if (searchBox) {
    searchBox.addEventListener('input', function () {
      const keyword = searchBox.value.toLowerCase(); // chuyển từ khoá sang chữ thường

      dataRows.forEach(function (row) {
        const text = row.textContent.toLowerCase(); // toàn bộ dòng dữ liệu thành chữ thường
        const match = text.includes(keyword); // kiểm tra có chứa từ khoá hay không
        row.style.display = match ? 'grid' : 'none'; // ẩn hoặc hiện dòng
      });
    });
  }

  // === 3. Chức năng chuyển trang pagination ===
  const rows = document.querySelectorAll('.booking-row:not(.booking-header)');
  const paginationLinks = document.querySelectorAll('.pagination a:not(.prev):not(.next)');
  const prevBtn = document.querySelector('.pagination .prev');
  const nextBtn = document.querySelector('.pagination .next');
  const rowsPerPage = 8;
  let currentPage = 1;

  // hàm chính để hiển thị dữ liệu của một trang cụ thể (1, 2, 3…)
  function showPage(pageNumber) {
    const totalPages = Math.ceil(rows.length / rowsPerPage); //Tính tổng số trang cần có.
    // Math.ceil(...): làm tròn lên, ví dụ: 17 dòng → cần 3 trang (8 + 8 + 1).
    if (pageNumber < 1) pageNumber = 1;
    if (pageNumber > totalPages) pageNumber = totalPages;
    currentPage = pageNumber;

    const start = (pageNumber - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach((row, index) => {
      row.style.display = (index >= start && index < end) ? 'grid' : 'none';
    });

    // Update active link
    paginationLinks.forEach(link => {
      const page = parseInt(link.textContent.trim());
      if (page === pageNumber) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  }

  paginationLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault(); //Không cho <a> nhảy trang mặc định
      const page = parseInt(link.textContent.trim()); //parseInt(...):	Chuyển nội dung nút từ "2" → số 2
      if (!isNaN(page)) {
        showPage(page);
      }
    });
  });

  if (prevBtn) {
    prevBtn.addEventListener('click', function (e) {
      e.preventDefault();
      showPage(currentPage - 1);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', function (e) {
      e.preventDefault();
      showPage(currentPage + 1);
    });
  }

  showPage(1); // Mặc định hiện trang đầu tiên
});
