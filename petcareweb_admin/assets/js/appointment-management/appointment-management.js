document.addEventListener("DOMContentLoaded", function () {
  const tableBody = document.getElementById("appointmentTableBody");
  const appointmentList = JSON.parse(localStorage.getItem("appointmentList")) || [];

  tableBody.innerHTML = ""; // Xóa trống trước khi render mới

  appointmentList.forEach((item, index) => {
    const row = document.createElement("tr");

    row.innerHTML = `
        <td>${index + 1}</td>
        <td>${item.service}</td>
        <td>${item.customerName}<br><span class="admin-table__email">(${item.email})</span></td>
        <td>
          ${item.staff
        ? `${item.staff}<br><span class="admin-table__code">(${item.staffCode || ""})</span>`
        : `<button class="btn btn--warning">Chọn nhân viên</button>`}
        </td>
        <td>${item.price || "0 đ"}</td>
        <td><span class="status ${getStatusClass(item.status)}">${item.status}</span></td>
        <td>${item.date}</td>
        <td>${item.createdAt || "-"}</td>
        <td>
          <button class="btn ${getActionClass(item.status)}" data-action="${getAction(item.status)}">
            ${getActionLabel(item.status)}
          </button>
        </td>
      `;

    tableBody.appendChild(row);
    // 👉 Gắn sự kiện click cho các ô "tên dịch vụ" và "khách hàng"
    const serviceCell = row.querySelector("td:nth-child(2)");
    const customerCell = row.querySelector("td:nth-child(3)");

    [serviceCell, customerCell].forEach((cell) => {
      if (!cell) return;
      cell.style.cursor = "pointer";
      cell.addEventListener("click", () => {
        localStorage.setItem("selectedAppointment", JSON.stringify(item));
        window.location.href = "appointment-detail.html";
      });
    });

  });

  // Các hàm phụ để xử lý trạng thái và nút
  function getStatusClass(status) {
    if (status === "Đang chờ...") return "status--pending";
    if (status === "Đang thực hiện...") return "status--inprogress";
    if (status === "Đã hoàn thành") return "status--success";
    return "";
  }

  function getAction(status) {
    if (status === "Đang chờ...") return "approve";
    if (status === "Đang thực hiện...") return "complete";
    return "";
  }

  function getActionLabel(status) {
    if (status === "Đang chờ...") return "Duyệt";
    if (status === "Đang thực hiện...") return "Đã xác nhận";
    return "Hoàn thành";
  }

  function getActionClass(status) {
    if (status === "Đang chờ...") return "btn--danger";
    if (status === "Đang thực hiện...") return "btn--info";
    return "btn--success";
  }
});
