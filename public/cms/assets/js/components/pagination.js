// ================= Mock Data (chỉ dùng để test frontend) =================
const mockCustomers = Array.from({ length: 55 }, (_, i) => ({
    code: `MS${String(i + 1).padStart(3, '0')}`,
    name: `Khách hàng ${i + 1}`,
    phone: `09876543${String(i).padStart(2, '0')}`,
    email: `customer${i + 1}@gmail.com`
}));

const rowsPerPage = 5;
let currentPage = 1;

// ================= Tạo 1 dòng dữ liệu =================
function createCustomerRow(customer, index) {
    const row = document.createElement("tr");
    row.innerHTML = `
        <td>${index + 1 + (currentPage - 1) * rowsPerPage}</td>
        <td>${customer.code}</td>
        <td>${customer.name}</td>
        <td>${customer.phone}</td>
        <td>${customer.email}</td>
        <td>
            <a href="/petcareweb_admin/pages/customer/update-customer.html">
                <button class="action-btn edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
            </a>
            <button class="action-btn delete">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    `;
    return row;
}

// ================= Hiển thị dữ liệu trang hiện tại =================
function renderCustomerPage(customers, page) {
    const tbody = document.getElementById("customerTableBody");
    if (!tbody) return;

    tbody.innerHTML = "";
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const pageData = customers.slice(start, end);

    pageData.forEach((customer, index) => {
        const row = createCustomerRow(customer, start + index);
        tbody.appendChild(row);
    });
}

// ================= Tạo danh sách số trang cần hiển thị trên giao diện pagination =================
function getPaginationPages(current, total) {
    if (total <= 5) return [...Array(total).keys()].map(i => i + 1);

    if (current <= 3) return [1, 2, 3, 4, '...', total];

    if (current >= total - 2) return [1, '...', total - 3, total - 2, total - 1, total];

    return [1, '...', current - 1, current, current + 1, '...', total];
}

// ================= Hiển thị phân trang =================
function renderPagination(totalItems, currentPage) {
    const totalPages = Math.ceil(totalItems / rowsPerPage);
    const paginationContainer = document.getElementById("pagination");
    if (!paginationContainer) return;

    paginationContainer.innerHTML = "";

    // Nút prev
    const prevBtn = document.createElement("button");
    prevBtn.className = "pagination__btn";
    prevBtn.innerHTML = `<i class="fa-solid fa-angle-left"></i>`;
    if (currentPage === 1) prevBtn.disabled = true;
    prevBtn.addEventListener("click", () => updatePage(currentPage - 1));
    paginationContainer.appendChild(prevBtn);

    // Logic phân trang thông minh (ẩn bớt, hiện ...)
    const pages = getPaginationPages(currentPage, totalPages);


    pages.forEach(p => {
        const btn = document.createElement("button");
        btn.className = "pagination__btn";

        if (p === '...') {
            btn.textContent = '...';
            btn.disabled = true;
        } else {
            btn.textContent = p;
            if (p === currentPage) btn.classList.add("pagination__btn--active");
            btn.addEventListener("click", () => updatePage(p));
        }

        paginationContainer.appendChild(btn);
    });

    // Nút next
    const nextBtn = document.createElement("button");
    nextBtn.className = "pagination__btn";
    nextBtn.innerHTML = `<i class="fa-solid fa-angle-right"></i>`;
    if (currentPage === totalPages) nextBtn.disabled = true;
    nextBtn.addEventListener("click", () => updatePage(currentPage + 1));
    paginationContainer.appendChild(nextBtn);
}

// ================= Thay đổi trang =================
function updatePage(newPage) {
    currentPage = newPage;
    renderCustomerPage(mockCustomers, currentPage);
    renderPagination(mockCustomers.length, currentPage);
}

// ================= Khởi chạy =================
document.addEventListener("DOMContentLoaded", function () {
    updatePage(1);
});
