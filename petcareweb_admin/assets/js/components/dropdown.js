document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll(".admin-dropdown");

    dropdowns.forEach(dropdown => {
        const selected = dropdown.querySelector(".admin-dropdown__selected");
        const list = dropdown.querySelector(".admin-dropdown__list");
        const items = dropdown.querySelectorAll(".admin-dropdown__item");

        // Toggle dropdown
        dropdown.addEventListener("click", function (e) {
            e.stopPropagation();
            closeAllDropdowns(); // đóng các dropdown khác
            dropdown.classList.toggle("is-open");
        });

        // Handle item selection
        items.forEach(item => {
            item.addEventListener("click", function (e) {
                e.stopPropagation();

                const code = this.querySelector(".admin-dropdown__code")?.textContent || "";
                const name = this.querySelector(".admin-dropdown__name")?.textContent || "";
                const price = this.querySelector(".admin-dropdown__price")?.textContent || "";

                let fullText = "";
                if (code && name) fullText = `${code} - ${name}`;
                else if (name && price) fullText = `${name} - ${price}`;
                else fullText = name || code || price;

                selected.textContent = fullText;
                dropdown.dataset.selectedId = this.dataset.id || "";
                dropdown.classList.remove("is-open");
            });
        });
    });

    // Click ngoài thì đóng dropdown
    document.addEventListener("click", function () {
        closeAllDropdowns();
    });

    function closeAllDropdowns() {
        document.querySelectorAll(".admin-dropdown").forEach(d => d.classList.remove("is-open"));
    }
});
