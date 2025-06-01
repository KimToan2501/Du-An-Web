<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<?php include_partial('admin/breadcrumb', ['breadcrumbs' => $breadcrumbs, 'title' => $title]) ?>

<div class="search-help">
    <small class="text-muted">
        Gợi ý: Bạn có thể tìm kiếm theo thời gian bắt đầu (VD: "08:00"), thời gian kết thúc (VD: "17:00"), hoặc khoảng thời gian (VD: "08:00-09:00")
    </small>
</div>

<!-- Start: Khối chức năng -->
<div class="admin-controls mt-1">
    <a href="<?= base_url('/admin/staff/add/schedule/' . $staff->user_id) ?>" class="btn btn--primary">Thêm lịch</a>

    <form class="admin-controls__search-form" method="GET">
        <div class="search-group">
            <input type="text" name="q" placeholder="Tìm kiếm theo thời gian (VD: 08:00, 09:00-10:00, 14:30)..." value="<?= $searching ?>" class="admin-controls__search-input">
            <button type="submit" class="btn btn--secondary">Tìm kiếm</button>
            <?php if (!empty($searching)): ?>
                <a href="<?= base_url('/admin/staff/schedule/' . $staff->user_id) ?>" class="btn btn--outline">Xóa tìm kiếm</a>
            <?php endif; ?>
        </div>

    </form>
</div>

<!-- Hiển thị kết quả tìm kiếm -->
<?php if (!empty($searching)): ?>
    <div class="search-result-info">
        <div class="alert alert-info">
            <i class="fa-solid fa-search"></i>
            Đang hiển thị kết quả tìm kiếm cho: <strong>"<?= htmlspecialchars($searching) ?>"</strong>
            <?php if (isset($pagination) && $pagination['total'] > 0): ?>
                - Tìm thấy <strong><?= $pagination['total'] ?></strong> kết quả
            <?php else: ?>
                - Không tìm thấy kết quả nào
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<!-- End: Khối chức năng -->

<!-- Start: Bảng dữ liệu -->
<section class="admin-table-wrapper">
    <h3 class="admin-table__title">Danh sách lịch làm việc</h3>

    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Ngày</th>
                <th>Thời gian bắt đầu</th>
                <th>Thời gian kết thúc</th>
                <th>Khoảng thời gian</th>
                <th>Số lượng đặt lịch</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($metadata) && count($metadata) > 0): ?>
                <?php $index = ($pagination['current'] - 1) * 10 + 1 ?>

                <?php foreach ($metadata as $item): ?>
                    <?php $timeSlot = $item->get_time_slot($item->time_slot_id); ?>
                    <tr>
                        <td>
                            <?= $index ?>
                        </td>
                        <td>
                            <span class="date-display"><?= format_date($item->date) ?></span>
                        </td>
                        <td>
                            <span class="time-display"><?= htmlspecialchars($timeSlot->start_time) ?></span>
                        </td>
                        <td>
                            <span class="time-display"><?= htmlspecialchars($timeSlot->end_time) ?></span>
                        </td>
                        <td>
                            <span class="time-range-display">
                                <?= htmlspecialchars($timeSlot->start_time) ?> - <?= htmlspecialchars($timeSlot->end_time) ?>
                            </span>
                        </td>
                        <td>
                            <span class="booking-count"><?= 0 ?></span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?= base_url('/admin/staff/update/schedule/' . $staff->user_id . '/' . $item->date) ?>" title="Chỉnh sửa">
                                    <button class="action-btn edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </a>

                                <button class="action-btn delete delete-item"
                                    data-start-time="<?= $timeSlot->start_time ?>"
                                    data-end-time="<?= $timeSlot->end_time ?>"
                                    data-date="<?= format_date($item->date) ?>"
                                    data-id="<?= $item->staff_schedule_id ?>"
                                    title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <?php $index++ ?>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" align="center">
                        <?php if (!empty($searching)): ?>
                            <div class="no-data-message">
                                <i class="fa-solid fa-search"></i>
                                <p>Không tìm thấy lịch làm việc nào phù hợp với từ khóa "<strong><?= htmlspecialchars($searching) ?></strong>"</p>
                                <a href="<?= base_url('/admin/staff/schedule/' . $staff->user_id) ?>" class="btn btn--primary btn--small">Xem tất cả lịch làm việc</a>
                            </div>
                        <?php else: ?>
                            <div class="no-data-message">
                                <i class="fa-solid fa-calendar-xmark"></i>
                                <p>Chưa có lịch làm việc nào</p>
                                <a href="<?= base_url('/admin/staff/add/schedule/' . $staff->user_id) ?>" class="btn btn--primary btn--small">Thêm lịch làm việc</a>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>

</section>
<!-- End: Bảng dữ liệu -->

<!-- Start: Phân trang -->
<?php if (isset($pagination) && $pagination['total'] > 0): ?>
    <?php include_partial('admin/pagination', ['pagination' => $pagination]) ?>
<?php endif; ?>
<!-- End: Phân trang -->

<?php start_section('scripts') ?>

<script>
    $(document).ready(function() {
        // Xử lý form tìm kiếm
        $('.admin-controls__search-form').on('submit', function(e) {
            const searchInput = $(this).find('input[name="q"]');
            const searchValue = searchInput.val().trim();

            if (searchValue === '') {
                e.preventDefault();
                // Redirect to clear search
                window.location.href = '<?= base_url("/admin/staff/schedule/" . $staff->user_id) ?>';
                return false;
            }
        });

        // Highlight search terms in results
        <?php if (!empty($searching)): ?>
            const searchTerm = '<?= addslashes($searching) ?>';
            highlightSearchTerms(searchTerm);
        <?php endif; ?>

        // Auto-focus search input when page loads
        $('.admin-controls__search-input').focus();
    });

    // Function to highlight search terms
    function highlightSearchTerms(searchTerm) {
        if (!searchTerm) return;

        const searchTermLower = searchTerm.toLowerCase();
        const timeDisplays = document.querySelectorAll('.time-display, .time-range-display');

        timeDisplays.forEach(function(element) {
            const text = element.textContent;
            const textLower = text.toLowerCase();

            if (textLower.includes(searchTermLower)) {
                const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                const highlightedText = text.replace(regex, '<mark>$1</mark>');
                element.innerHTML = highlightedText;
            }
        });
    }

    // Xử lý xóa lịch làm việc
    $('.delete-item').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const date = $(this).data('date');
        const startTime = $(this).data('start-time');
        const endTime = $(this).data('end-time');

        Swal.fire({
            title: `Bạn có chắc chắn muốn xóa lịch làm việc ngày <b>'${date}'</b>, thời gian <b>${startTime}-${endTime}</b> này?`,
            text: "Hành động này không thể hoàn tác!",
            icon: "warning",
            dangerMode: true,
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Xóa",
            cancelButtonText: "Hủy",
        }).then((willDelete) => {
            if (willDelete.isConfirmed) {
                // loadAjaxStatus("start");
                $.ajax({
                    url: `<?= base_url("/admin/staff/schedule/delete/{$staff->user_id}") ?>/${id}`,
                    method: 'DELETE',
                    success: function(res) {
                        swAlert("Thông báo", res.message, "success");
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function(err) {
                        swAlert("Thông báo", err.responseJSON.message || "Đã có lỗi xảy ra", "error");
                    },
                    complete: function() {
                        // loadAjaxStatus("stop");
                    },
                });
            }
        });
    });
</script>

<?php end_section() ?>

<style>
    .search-group {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 5px;
    }

    .search-help {
        margin-top: 5px;
    }

    .search-result-info {
        margin-bottom: 20px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 4px;
        border: 1px solid;
    }

    .alert-info {
        background-color: #e7f3ff;
        border-color: #b8daff;
        color: #0c5460;
    }

    .alert i {
        margin-right: 8px;
    }

    .time-display,
    .time-range-display {
        font-weight: 500;
        color: #2c3e50;
    }

    .date-display {
        font-weight: 500;
        color: #34495e;
    }

    .booking-count {
        display: inline-block;
        background: #e9ecef;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        padding: 8px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .action-btn.edit {
        background-color: #28a745;
        color: white;
    }

    .action-btn.edit:hover {
        background-color: #218838;
    }

    .action-btn.delete {
        background-color: #dc3545;
        color: white;
    }

    .action-btn.delete:hover {
        background-color: #c82333;
    }

    .no-data-message {
        padding: 40px 20px;
        text-align: center;
        color: #6c757d;
    }

    .no-data-message i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .no-data-message p {
        margin-bottom: 20px;
        font-size: 16px;
    }

    .btn--small {
        padding: 8px 16px;
        font-size: 14px;
    }

    mark {
        background-color: #fff3cd;
        padding: 2px 4px;
        border-radius: 2px;
        font-weight: bold;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .search-group {
            flex-direction: column;
            align-items: stretch;
        }

        .admin-controls__search-input {
            margin-bottom: 10px;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<!-- End: Tiêu đề trang -->