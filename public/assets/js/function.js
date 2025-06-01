const swAlert = (...props) => Swal.fire(...props);

const loadAjaxStatus = (status) => {
  return status === "start" ? $("body").addClass("loading") : $("body").removeClass("loading");
};

function getQueryFromUrl(key) {
  const params = new URLSearchParams(window.location.search);
  return params.get(key);
}

function setupAvatarPreview(inputId, imgId) {
  var avatarInput = document.getElementById(inputId);
  var avatarImg = document.getElementById(imgId);

  return new Promise((resolve, reject) => {
    if (avatarInput && avatarImg) {
      avatarInput.addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            avatarImg.classList.remove("d-none");
            avatarImg.src = e.target.result;

            resolve(e.target.result);
          };
          reader.readAsDataURL(file);
        }
      });
    }
  });
}

// Logic cho helper formatDate trong handlebars.helper.js
// Bạn cần tích hợp logic này vào hàm formatDate hiện tại của bạn.
// Hàm này nhận vào timestamp (hoặc đối tượng Date) và trả về chuỗi định dạng.

function formatDateTimestamp(timestamp) {
  const now = new Date();
  const date = new Date(timestamp);
  const seconds = Math.floor((now - date) / 1000);

  // Dưới 1 phút
  if (seconds < 60) {
    return seconds <= 1 ? "Vừa xong" : seconds + " giây trước";
  }

  // Dưới 1 giờ
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) {
    return minutes + " phút trước";
  }

  // Dưới 24 giờ (Hiển thị giờ:phút)
  const hours = Math.floor(minutes / 60);
  if (hours < 24) {
    const hoursFormatted = date.getHours().toString().padStart(2, "0");
    const minutesFormatted = date.getMinutes().toString().padStart(2, "0");
    return `${hours} giờ trước`; // Hoặc hiển thị giờ cụ thể: `${hoursFormatted}:${minutesFormatted}`
  }

  // Hôm qua
  const yesterday = new Date(now);
  yesterday.setDate(now.getDate() - 1);
  if (
    date.getDate() === yesterday.getDate() &&
    date.getMonth() === yesterday.getMonth() &&
    date.getFullYear() === yesterday.getFullYear()
  ) {
    const hoursFormatted = date.getHours().toString().padStart(2, "0");
    const minutesFormatted = date.getMinutes().toString().padStart(2, "0");
    return `Hôm qua lúc ${hoursFormatted}:${minutesFormatted}`;
  }

  // Trong cùng năm (Hiển thị Tháng Ngày)
  if (date.getFullYear() === now.getFullYear()) {
    const monthNames = [
      "Tháng 1",
      "Tháng 2",
      "Tháng 3",
      "Tháng 4",
      "Tháng 5",
      "Tháng 6",
      "Tháng 7",
      "Tháng 8",
      "Tháng 9",
      "Tháng 10",
      "Tháng 11",
      "Tháng 12",
    ];
    return `${monthNames[date.getMonth()]} ${date.getDate()} lúc ${date
      .getHours()
      .toString()
      .padStart(2, "0")}:${date.getMinutes().toString().padStart(2, "0")}`;
  }

  // Các năm trước (Hiển thị Ngày Tháng Năm)
  const day = date.getDate().toString().padStart(2, "0");
  const month = (date.getMonth() + 1).toString().padStart(2, "0"); // Tháng bắt đầu từ 0
  const year = date.getFullYear();
  const hoursFormatted = date.getHours().toString().padStart(2, "0");
  const minutesFormatted = date.getMinutes().toString().padStart(2, "0");
  return `${day}/${month}/${year} lúc ${hoursFormatted}:${minutesFormatted}`;
}

// Hàm định dạng thời gian theo kiểu HH:mm
function formatTimeHHMM(timestamp) {
  const date = new Date(timestamp);
  const hours = date.getHours().toString().padStart(2, "0");
  const minutes = date.getMinutes().toString().padStart(2, "0");
  return `${hours}:${minutes}`;
}

function getToday() {
  // Set min date for start_date and end_date to today
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, "0"); // Months are 0-indexed
  const dd = String(today.getDate()).padStart(2, "0");
  const todayFormatted = `${yyyy}-${mm}-${dd}`;

  return todayFormatted;
}
