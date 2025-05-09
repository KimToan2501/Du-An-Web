document.addEventListener("DOMContentLoaded", function () {
    const data = JSON.parse(localStorage.getItem("selectedAppointment"));
    if (!data) return;

    document.getElementById("fullName").value = data.customerName || "";
    document.getElementById("email").value = data.email || "";
    document.getElementById("phone").value = data.phone || "";

    document.getElementById("datepicker").value = `${data.date}`;
    // document.getElementById("datepicker").value = `${data.date} ${data.time}`;

    document.getElementById("staff").innerHTML = `<option selected>${data.staff || ""}</option>`;
    document.getElementById("service").innerHTML = `<option selected>${data.service || ""}</option>`;

    // 👉 Xử lý mở modal khi bấm "Xóa lịch hẹn"
    const deleteButton = document.querySelector(".btn.btn--danger");
    const modal = document.getElementById("approvalModal");
    const modalTitle = modal.querySelector(".custom-modal__title");
    const modalMessage = modal.querySelector(".custom-modal__message");
    const customerNameDisplay = modal.querySelector("#customerName");

    // 👉 Mở modal
    deleteButton.addEventListener("click", function () {
        modal.classList.add("is-open");
        modalTitle.textContent = "Xác nhận xóa lịch hẹn";
        modalMessage.innerHTML = `
        Bạn muốn xóa lịch hẹn của khách hàng 
        <strong id="customerName">“${data.customerName}”</strong>
    `;
    });

    // 👉 Đóng modal khi bấm Hủy hoặc click ra ngoài
    document.getElementById("cancelBtn").addEventListener("click", () => {
        modal.classList.remove("is-open");
    });

    modal.querySelector(".custom-modal__overlay").addEventListener("click", () => {
        modal.classList.remove("is-open");
    });

    const confirmBtn = document.getElementById("confirmBtn");

    confirmBtn.addEventListener("click", function () {
        // 1. Lấy lại danh sách tất cả lịch hẹn
        let list = JSON.parse(localStorage.getItem("appointmentList")) || [];

        // 2. Lọc bỏ lịch hẹn đang được xem
        const newList = list.filter(item => {
            return !(item.email === data.email && item.date === data.date && item.service === data.service);
        });

        // 3. Cập nhật lại danh sách mới vào localStorage
        localStorage.setItem("appointmentList", JSON.stringify(newList));

        // 4. Xóa lịch hẹn đang xem
        localStorage.removeItem("selectedAppointment");

        // 5. Chuyển về trang danh sách
        window.location.href = "appointment-management.html";
    });

});

