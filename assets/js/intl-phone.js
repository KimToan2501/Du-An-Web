// B1: Chọn ô input số điện thoại dựa trên ID
const phoneInput = document.querySelector("#phone");

// B2: Khởi tạo thư viện intl-tel-input với cấu hình tuỳ chỉnh
const iti = window.intlTelInput(phoneInput, {
  initialCountry: "vn", // Mặc định hiển thị là Việt Nam
  preferredCountries: ["vn", "us", "jp", "kr"], // Danh sách quốc gia ưu tiên trên cùng
  separateDialCode: true, // Hiển thị mã vùng tách riêng khỏi số điện thoại
  utilsScript:
    "https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/utils.js", // Bắt buộc để dùng format & validate
});

// B3: Gán sự kiện khi người dùng submit form
const form = document.querySelector("#register-form");

form.addEventListener("submit", function (e) {
  e.preventDefault(); // Ngăn không cho form gửi đi ngay

  // B4: Kiểm tra xem số điện thoại người dùng nhập có hợp lệ không
  if (!iti.isValidNumber()) {
    alert("Số điện thoại không hợp lệ! Vui lòng kiểm tra lại.");
    return;
  }

  // B5: Lấy số điện thoại đúng chuẩn quốc tế (ví dụ: +84901234567)
  const fullPhone = iti.getNumber();

  // Hiển thị thử trong console (sau này có thể gửi lên server)
  console.log("Số điện thoại chuẩn quốc tế là:", fullPhone);

  // B6 (Tùy chọn): Gán số vào một input ẩn nếu muốn gửi lên server
  document.querySelector("#phone_full").value = fullPhone;

  // B7 (Tùy chọn): Nếu muốn cho phép form gửi đi
  form.submit();
});
