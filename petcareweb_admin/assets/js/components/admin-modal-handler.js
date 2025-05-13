document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("approvalModal");
    const modalTitle = modal.querySelector(".custom-modal__title");
    const nameEl = document.getElementById("modalTargetName");
    const labelEl = modal.querySelector(".modal-action-label");
    const suffixEl = modal.querySelector(".modal-action-suffix");
    const cancelBtn = document.getElementById("cancelBtn");
    const confirmBtn = document.getElementById("confirmBtn");

    let currentRow = null; // Biến để nhớ dòng được chọn
    let currentAction = null;

    // 👉 Bắt tất cả nút có chữ "Duyệt"
    const actionButtons = document.querySelectorAll(
        ".btn.btn--danger, .btn.btn--info, .action-btn.delete"
    );

    actionButtons.forEach((btn) => {
        btn.addEventListener("click", function () {
            const row = this.closest("tr");
            const tbody = row.closest("tbody");
            const tableType = tbody.dataset.type;

            const nameCell = getNameCell(row, tableType);
            const itemName =
                nameCell?.childNodes[0]?.textContent.trim() || "Không rõ";

            currentRow = row;
            currentAction = this.dataset.action || "approve";

            renderModalContent(currentAction, itemName);
            modal.classList.add("is-open");
        });
    });

    // 👉 Bấm nút Hủy thì đóng modal
    cancelBtn.addEventListener("click", () => modal.classList.remove("is-open"));
    modal.querySelector(".custom-modal__overlay").addEventListener("click", () =>
        modal.classList.remove("is-open")
    );

    // 👉 Bấm ra ngoài overlay cũng đóng modal
    const overlay = document.querySelector("#approvalModal .custom-modal__overlay");

    overlay.addEventListener("click", function () {
        modal.classList.remove("is-open");
    });

    // 👉 Xử lý khi bấm nút "Duyệt" trong popup
    confirmBtn.addEventListener("click", function () {
        if (!currentRow || !currentAction) return;

        if (currentAction === "approve") {
            const statusCell = currentRow.querySelector("td:nth-child(6)");
            const actionCell = currentRow.querySelector("td:nth-child(9)");
            statusCell.innerHTML = `<span class="status status--inprogress">Đang thực hiện...</span>`;
            actionCell.innerHTML = `<button class="btn btn--info" data-action="complete">Đã xác nhận</button>`;
        } else if (currentAction === "complete") {
            const statusCell = currentRow.querySelector("td:nth-child(6)");
            const actionCell = currentRow.querySelector("td:nth-child(9)");
            statusCell.innerHTML = `<span class="status status--success">Đã hoàn thành</span>`;
            actionCell.innerHTML = `<button class="btn btn--success">Hoàn thành</button>`;
        } else if (currentAction === "delete") {
            currentRow.remove();
        }

        // Đóng modal
        modal.classList.remove("is-open");
    });

    // ========== HÀM HỖ TRỢ ==========

    function getNameCell(row, type) {
        const map = {
            appointment: 3,
            customer: 3,
            staff: 3,
            service: 2,
        };
        const index = map[type] || 2;
        return row.querySelector(`td:nth-child(${index})`);
    }

    function renderModalContent(action, itemName) {
        const sectionTitle =
            document.querySelector("h3")?.textContent.toLowerCase() || "";

        // reset mặc định
        labelEl.textContent = "";
        suffixEl.textContent = "";

        if (action === "approve") {
            modalTitle.textContent = "Xác nhận lịch hẹn";
            labelEl.textContent = "Tiến hành thực hiện dịch vụ của khách hàng ";
            nameEl.textContent = `“${itemName}”`;
            suffixEl.textContent = "";
        } else if (action === "complete") {
            modalTitle.textContent = "Xác nhận đã hoàn thành";
            labelEl.textContent = "Đã hoàn thành thực hiện dịch vụ của khách hàng ";
            nameEl.textContent = `“${itemName}”`;
            suffixEl.textContent = "";
        } else if (action === "delete") {
            if (sectionTitle.includes("khách hàng")) {
                modalTitle.textContent = "Xác nhận xóa khách hàng";
                labelEl.textContent = "Bạn muốn xóa khách hàng ";
            } else if (sectionTitle.includes("nhân viên")) {
                modalTitle.textContent = "Xác nhận xóa nhân viên";
                labelEl.textContent = "Bạn muốn xóa nhân viên ";
            } else if (sectionTitle.includes("dịch vụ")) {
                modalTitle.textContent = "Xác nhận xóa dịch vụ";
                labelEl.textContent = "Bạn muốn xóa dịch vụ ";
            } else {
                modalTitle.textContent = "Xác nhận xóa";
                labelEl.textContent = "Bạn muốn xóa mục ";
            }
            nameEl.textContent = `“${itemName}”`;
            suffixEl.textContent = "?";
        }
    }

});