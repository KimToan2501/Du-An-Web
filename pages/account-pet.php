<?php
require_once 'connect.php';
session_start();

$user_id = 1;

// Truy vấn thông tin người dùng
$sql = "SELECT 
           p.name,
           p.type,
           p.age,
           p.breed
        FROM pets p
        
        WHERE p.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$pets = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Audiowide&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
        rel="stylesheet">
    <!-- CSS dùng chung -->
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">

    <link rel="stylesheet" href="../assets/css/account-pet.css">
    <title>Account - Pet</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .pet-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .pet-table th,
        .pet-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .pet-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pet-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .pet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .add-pet {
            background-color: #6b21a8;
            color: white;
        }

        .update-pet {
            background-color: #f0f0f0;
            color: #333;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .edit-icon {
            width: 16px;
            height: 16px;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 20px 0;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background-color: #e9ecef;
        }

        .pagination .active a {
            background-color: #6b21a8;
            color: white;
            border-color: #6b21a8;
        }

        .pagination .disabled a,
        .pagination .disabled span {
            color: #6c757d;
            cursor: not-allowed;
            border-color: #ddd;
        }
    </style>
</head>

<body style="background-color: #FBF6FF;">
    <!-- Start: Header -->
    <!-- Start: Header -->
    <header id="header">
        <div class="pawspa__container pawspa__flex-between">
            <!-- Start: Logo -->
            <a href="/index.html" id="pawspa-logo" aria-label="Go to homepage" class="pawspa-header__logo">
                <img src="../assets/images/icons/Union.svg" alt="Logo" class="pawspa-logo__image">
                <span class="pawspa-logo__text">Pawspa</span>
            </a>
            <!-- End: Logo -->

            <!-- Start: Navigation -->
            <nav id="pawspa-nav">
                <ul class="pawspa-nav__list">
                    <li class="pawspa-nav__item">
                        <a href="/index.html">Trang chủ</a>
                    </li>
                    <li class="pawspa-nav__item">
                        <a href="#">Dịch vụ</a>
                    </li>
                    <li class="pawspa-nav__item">
                        <a href="#">Blog/Tin tức</a>
                    </li>
                    <li class="pawspa-nav__item">
                        <a href="#">Giới thiệu</a>
                    </li>
                    <li class="pawspa-nav__item">
                        <a href="#">Liên lạc</a>
                    </li>
                </ul>
            </nav>
            <!-- End: Navigation -->

            <!-- Start: Icon + Action -->
            <div class="pawspa-header__actions">
                <a href="#" class="pawspa-icon__link" aria-label="Notifications">
                    <img src="../assets/images/icons/noti.svg" alt="Notify" class="pawspa-icon__image">
                </a>
                <a href="#" class="pawspa-icon__link" aria-label="Cart">
                    <img src="../assets/images/icons/cart.svg" alt="Cart" class="pawspa-icon__image">
                </a>
                <div class="avatar-wrapper">
                    <img src="../assets/images/avatar.png" alt="Avatar" class="avatar-image">
                </div>
            </div>
            <!-- End: Icon + Action -->
        </div>
    </header>
    <!-- End: Header -->

    <main class="main-wrapper">
        <div class="container">
            <aside class="sidebar">
                <div class="user-info">
                    <img src="../assets/images/avatar.png" alt="Avatar" class="user-avatar">
                    <div class="user-meta">
                        <div class="user-name">Katy Nguyen</div>
                        <div class="user-role">Dashboard <span class="role-type">User</span></div>
                        <img src="../ArrowsCounterClockwise.png" alt="Refresh" class="refresh-icon">
                    </div>
                </div>
                <nav class="user-nav">
                    <ul>
                        <li class="active"><a href="/pages/account.html">Tài khoản của tôi</a></li>
                        <li><a href="/pages/booking.html">Đặt lịch</a></li>
                        <li><a href="/pages/notifications.html">Thông báo</a></li>
                    </ul>
                </nav>
            </aside>
        </div>

        <section class="account-main">
            <div class="account-tabs">
                <button class="tab">
                    <img src="../assets/images/account/user gear.svg" alt="Chi tiết tài khoản" class="tab-icon">
                    <a href="/pages/account-detail.php">Chi tiết tài khoản</a>
                </button>
                <button class="tab active">
                    <img src="../assets/images/account/paw.svg" alt="Thú cưng của bạn" class="tab-icon">
                    <a href="/pages/account-pet.php" style="color: #999">Thú cưng của bạn</a>
                </button>
            </div>
            <div class="pet-container">
                <div class="pet-header">
                    <h2>Thú Cưng Của Bạn</h2>
                    <div class="pet-actions">
                        <button class="btn add-pet" style="width: 150px;">
                            Thêm thú cưng <span>+</span>
                        </button>
                        <button class="btn update-pet" style="width: 200px;">
                            Cập nhật về thú cưng <img src="../assets/images/account/PencilSimpleLine.svg" alt="edit"
                                class="edit-icon">
                        </button>
                    </div>
                </div>
                <table class="pet-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Tên thú cưng</th>
                            <th>Phân loại</th>
                            <th>Tuổi</th>
                            <th>Giống loài</th>
                        </tr>
                    </thead>
                    <tbody id="pet-table-body">
                        <!-- Dữ liệu sẽ được render vào đây bởi JavaScript -->
                    </tbody>
                </table>
                <ul class="pagination"></ul>
            </div>
        </section>
    </main>

    <!-- Start: Footer -->
    <footer id="footer">
        <div class="pawspa__container pawspa__flex-between">
            <div class="pawspa-footer__info">
                <img src="../assets/images/icons/Union.svg" alt="Logo Pawspa" class="pawspa-footer__logo">
                <p class="pawspa-footer__description">
                    Chào mừng đến với Cuddle & Care Pets! Chúng tôi cung cấp các dịch vụ chăm
                    sóc và tư vấn sức khỏe cho thú cưng của bạn.
                </p>
                <div class="pawspa-footer__contact">
                    <div class="pawspa-footer__contact-item">
                        <img src="../assets/images/icons/letter.svg" alt="Email" class="pawspa-footer__contact-icon">
                        <a href="mailto:chaumlp@gmail.com" class="pawspa-footer__contact-address">chaumlp@gmail.com</a>
                    </div>
                    <div class="pawspa-footer__contact-item">
                        <img src="../assets/images/icons/phone.svg" alt="Phone" class="pawspa-footer__contact-icon">
                        <a href="tel:0345663153" class="pawspa-footer__contact-address">0345663153</a>
                    </div>
                </div>
                <div class="pawspa-footer__social">
                    <a href="#" title="Instagram">
                        <img src="../assets/images/icons/social-media/ins.svg" alt="Instagram"
                            class="pawspa-footer__social-icon">
                    </a>
                    <a href="#" title="Facebook">
                        <img src="../assets/images/icons/social-media/fb.svg" alt="Facebook"
                            class="pawspa-footer__social-icon">
                    </a>
                    <a href="#" title="LinkedIn">
                        <img src="../assets/images/icons/social-media/link.svg" alt="LinkedIn"
                            class="pawspa-footer__social-icon">
                    </a>
                </div>
            </div>

            <div class="pawspa-footer__image">
                <div>
                    <div>
                        <img src="../assets/images/footer/Rectangle 170.svg" alt="" aria-hidden="true">
                    </div>
                </div>
            </div>

            <div class="pawspa-footer__category">
                <div class="pawspa-footer__category-item">
                    <h3 class="pawspa-footer__category-title">Dịch vụ</h3>
                    <ul class="pawspa-footer__category-list">
                        <li class="pawspa-footer__category-list-item">
                            <a href="#" class="pawspa-footer__category-item-link">Spa</a>
                        </li>
                        <li class="pawspa-footer__category-list-item">
                            <a href="#" class="pawspa-footer__category-item-link">Cắt tỉa lông</a>
                        </li>
                        <li class="pawspa-footer__category-list-item">
                            <a href="#" class="pawspa-footer__category-item-link">Khách sạn lưu trú</a>
                        </li>
                    </ul>
                </div>
                <div class="pawspa-footer__category-item">
                    <h3 class="pawspa-footer__category-title">Nền tảng</h3>
                    <ul class="pawspa-footer__category-list">
                        <li class="pawspa-footer__category-list-item">
                            <a href="#" class="pawspa-footer__category-item-link">Dịch vụ</a>
                        </li>
                        <li class="pawspa-footer__category-list-item">
                            <a href="#" class="pawspa-footer__category-item-link">Blog/Tin tức</a>
                        </li>
                        <li class="pawspa-footer__category-list-item">
                            <a href="#" class="pawspa-footer__category-item-link">Giới thiệu</a>
                        </li>
                        <li class="pawspa-footer__category-list-item">
                            <a href="#" class="pawspa-footer__category-item-link">Liên lạc</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="pawspa-footer__image--left">
            <img src="../assets/images/footer/footer-01.svg" alt="">
        </div>
        <div class="pawspa-footer__image--right">
            <img src="../assets/images/footer/footer-02.svg" alt="">
        </div>
    </footer>
    <!-- End: Footer -->

    <script src="../assets/js/active-link.js"></script>
    <script src="../assets/js/account-pet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paginationContainer = document.querySelector('.pagination');
            const petTableBody = document.querySelector('#pet-table-body');
            const petsPerPage = 3;
            let currentPage = 1;

            // Lấy dữ liệu pets từ PHP dưới dạng JSON
            const petData = <?php echo json_encode($pets); ?>;

            // Thêm avatar mặc định cho mỗi pet (bạn có thể bổ sung trường avatar trong DB nếu muốn)
            petData.forEach(pet => {
                pet.avatar = '/assets/images/account/default-pet.png'; // avatar mặc định
            });

            const totalPages = Math.ceil(petData.length / petsPerPage);

            function showPage(pageNumber) {
                currentPage = pageNumber;
                const startIndex = (pageNumber - 1) * petsPerPage;
                const endIndex = startIndex + petsPerPage;
                const currentPets = petData.slice(startIndex, endIndex);

                // Render pet table
                petTableBody.innerHTML = '';
                currentPets.forEach(pet => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>></td>
                <td>
                    <div class="pet-info">
                        <img src="${pet.avatar}" alt="${pet.name}" class="pet-avatar">
                        ${pet.name}
                    </div>
                </td>
                <td>${pet.type}</td>
                <td>${pet.age}</td>
                <td>${pet.breed}</td>
            `;
                    petTableBody.appendChild(row);
                });

                updatePagination(pageNumber);
                document.querySelector('.container').scrollIntoView({ behavior: 'smooth' });
            }

            function updatePagination(activePage) {
                paginationContainer.innerHTML = '';

                // Previous button
                const prevLi = document.createElement('li');
                const prevButton = document.createElement('a');
                prevButton.href = '#';
                prevButton.textContent = '«';
                if (activePage === 1) {
                    prevLi.classList.add('disabled');
                } else {
                    prevButton.addEventListener('click', function (e) {
                        e.preventDefault();
                        if (activePage > 1) showPage(activePage - 1);
                    });
                }
                prevLi.appendChild(prevButton);
                paginationContainer.appendChild(prevLi);

                // Always show first page
                paginationContainer.appendChild(createPageLink(1, activePage));

                // Ellipsis if needed
                if (activePage > 4) {
                    paginationContainer.appendChild(createEllipsis());
                }

                let startPage = Math.max(2, activePage - 2);
                let endPage = Math.min(totalPages - 1, activePage + 2);

                for (let i = startPage; i <= endPage; i++) {
                    paginationContainer.appendChild(createPageLink(i, activePage));
                }

                if (activePage < totalPages - 3) {
                    paginationContainer.appendChild(createEllipsis());
                }

                if (totalPages > 1) {
                    paginationContainer.appendChild(createPageLink(totalPages, activePage));
                }

                // Next button
                const nextLi = document.createElement('li');
                const nextButton = document.createElement('a');
                nextButton.href = '#';
                nextButton.textContent = '»';
                if (activePage === totalPages) {
                    nextLi.classList.add('disabled');
                } else {
                    nextButton.addEventListener('click', function (e) {
                        e.preventDefault();
                        if (activePage < totalPages) showPage(activePage + 1);
                    });
                }
                nextLi.appendChild(nextButton);
                paginationContainer.appendChild(nextLi);
            }

            function createPageLink(pageNumber, activePage) {
                const pageLi = document.createElement('li');
                const pageLink = document.createElement('a');
                pageLink.href = '#';
                pageLink.textContent = pageNumber;
                if (pageNumber === activePage) {
                    pageLi.classList.add('active');
                }
                pageLink.addEventListener('click', function (e) {
                    e.preventDefault();
                    showPage(pageNumber);
                });
                pageLi.appendChild(pageLink);
                return pageLi;
            }

            function createEllipsis() {
                const ellipsisLi = document.createElement('li');
                const ellipsisSpan = document.createElement('span');
                ellipsisSpan.textContent = '...';
                ellipsisLi.classList.add('disabled');
                ellipsisLi.appendChild(ellipsisSpan);
                return ellipsisLi;
            }

            showPage(1);
        });
    </script>

</body>

</html>
<?php
$conn->close();
?>