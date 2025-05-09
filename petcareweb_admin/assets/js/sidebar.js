/**
 * Lấy tên file cuối cùng từ đường dẫn (VD: /pages/customer/add-customer.html → add-customer.html)
 * @param {string} path - Đường dẫn URL hoặc href.
 * @returns {string} - Tên file.
 */
function extractFileName(path = "") {
    return path.split("/").pop();
}

/**
 * Tô đậm và mở rộng menu con nếu file hiện tại khớp với sublink.
 * @param {string} currentFile - Tên file hiện tại đang được mở.
 */
function highlightActiveSublinks(currentFile) {
    const submenuItems = document.querySelectorAll(".admin-sidebar__item.has-submenu");

    submenuItems.forEach(menuItem => {
        const subLinks = menuItem.querySelectorAll(".admin-sidebar__sublink");

        subLinks.forEach(link => {
            const hrefFile = extractFileName(link.getAttribute("href"));
            if (hrefFile === currentFile) {
                link.classList.add("admin-sidebar__sublink--active");
                menuItem.classList.add("admin-sidebar__item--active");
            }
        });
    });
}

/**
 * Tô đậm menu cha nếu không có submenu.
 * @param {string} currentFile - Tên file hiện tại đang được mở.
 */
function highlightActiveMainLinks(currentFile) {
    const mainLinks = document.querySelectorAll(".admin-sidebar__item > a.admin-sidebar__link");

    mainLinks.forEach(link => {
        const href = link.getAttribute("href");
        if (!href) return;

        const hrefFile = extractFileName(href);
        if (hrefFile === currentFile) {
            link.closest(".admin-sidebar__item").classList.add("admin-sidebar__item--active");
        }
    });
}

// ========== Chạy khi trang tải xong ==========
document.addEventListener("DOMContentLoaded", function () {
    const currentFile = extractFileName(window.location.pathname);
    highlightActiveSublinks(currentFile);
    highlightActiveMainLinks(currentFile);
});
