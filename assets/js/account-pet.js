
$(document).ready(function () {
    const pets = [
        { name: "Webb", type: "Mèo", age: 2, breed: "Mèo anh", avatar: "../assets/images/account/webb.png" },
        { name: "Rob", type: "Chó", age: 4, breed: "Chó chăn cừu", avatar: "../assets/images/account/rob.png" },
        { name: "Miles", type: "Chim", age: 5, breed: "Chim két", avatar: "../assets/images/account/miles.png" },
        { name: "Luna", type: "Mèo", age: 3, breed: "Mèo Ba Tư", avatar: "../assets/images/account/webb.png" },
        { name: "Max", type: "Chó", age: 6, breed: "Husky", avatar: "../assets/images/account/rob.png" },
        { name: "Tweety", type: "Chim", age: 1, breed: "Vẹt", avatar: "../assets/images/account/miles.png" }
    ];

    const itemsPerPage = 3;
    const totalPages = Math.ceil(pets.length / itemsPerPage);
    let currentPage = 1;

    function renderPets(page) {
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const petsToShow = pets.slice(start, end);

        let tableHtml = `
            <thead>
                <tr>
                    <th></th>
                    <th>Tên thú cưng</th>
                    <th>Phân loại</th>
                    <th>Tuổi</th>
                    <th>Giống loài</th>
                </tr>
            </thead>
            <tbody>
        `;
        petsToShow.forEach(pet => {
            tableHtml += `
                <tr>
                    <td>&gt;</td>
                    <td>
                        <div class="pet-info">
                            <img src="${pet.avatar}" alt="${pet.name}" class="pet-avatar">
                            ${pet.name}
                        </div>
                    </td>
                    <td>${pet.type}</td>
                    <td>${pet.age}</td>
                    <td>${pet.breed}</td>
                </tr>
            `;
        });
        tableHtml += `</tbody>`;

        $(".pet-table").html(tableHtml);
    }

    function renderPagination() {
        let paginationHtml = `
            <a href="#" class="prev">&lt;</a>
        `;
        for (let i = 1; i <= totalPages; i++) {
            paginationHtml += `<a href="#" class="${i === currentPage ? "active" : ""}">${i}</a>`;
        }
        paginationHtml += `<a href="#" class="next">&gt;</a>`;

        $(".pagination").html(paginationHtml);
        updatePagination();
    }

    function updatePagination() {
        $(".pagination a").removeClass("disabled active");
        $(`.pagination a:contains('${currentPage}')`).addClass("active");

        if (currentPage === 1) {
            $(".pagination .prev").addClass("disabled");
        }
        if (currentPage === totalPages) {
            $(".pagination .next").addClass("disabled");
        }
    }

    // Uỷ quyền sự kiện click
    $(document).on("click", ".pagination a", function (e) {
        e.preventDefault();
        const $this = $(this);
        if ($this.hasClass("disabled") || $this.hasClass("active")) return;

        if ($this.hasClass("prev")) {
            if (currentPage > 1) currentPage--;
        } else if ($this.hasClass("next")) {
            if (currentPage < totalPages) currentPage++;
        } else {
            currentPage = parseInt($this.text());
        }

        renderPets(currentPage);
        renderPagination();
    });

    // Gọi khi load trang
    renderPets(currentPage);
    renderPagination();
});
