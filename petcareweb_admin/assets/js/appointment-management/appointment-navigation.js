// appointment-navigation.js

function attachClickHandlers() {
  const rows = document.querySelectorAll("tbody[data-type='appointment'] tr");

  rows.forEach((row) => {
    const serviceCell = row.querySelector("td:nth-child(2)");
    const customerCell = row.querySelector("td:nth-child(3)");

    [serviceCell, customerCell].forEach((cell) => {
      if (!cell) return;
      cell.style.cursor = "pointer";
      cell.addEventListener("click", () => {
        const rowData = extractAppointmentData(row);
        localStorage.setItem("selectedAppointment", JSON.stringify(rowData));
        window.location.href = "appointment-detail.html";
      });
    });
  });
}

// 👉 Dùng khi cần render ban đầu (nếu bảng đã có sẵn trong HTML)
document.addEventListener("DOMContentLoaded", function () {
  attachClickHandlers();
});

// 👉 Hàm tách riêng để gọi lại sau khi render bảng bằng JS
function extractAppointmentData(row) {
  const tds = row.querySelectorAll("td");

  const customerCell = tds[2];
  const customerName = customerCell.childNodes[0]?.textContent.trim() || "";
  const email = customerCell.querySelector("span")?.textContent.replace(/[()]/g, "").trim() || "";

  const staffRaw = tds[3].textContent.trim();
  const staff = staffRaw.includes("Chọn nhân viên") ? "" : staffRaw;

  const service = tds[1].textContent.trim();
  const price = tds[4].textContent.trim();
  const status = tds[5].textContent.trim();
  const date = tds[6].textContent.trim();
  // const time = tds[7].textContent.trim();

  return {
    customerName,
    email,
    phone: mapPhoneFromEmail(email),
    staff,
    service,
    price,
    status,
    date
    // time
  };
}

function mapPhoneFromEmail(email) {
  const phoneBook = {
    "a@gmail.com": "0984638214",
    "anh@gmail.com": "0984638215",
    "thanh@gmail.com": "0984638216"
  };
  return phoneBook[email] || "";
}
