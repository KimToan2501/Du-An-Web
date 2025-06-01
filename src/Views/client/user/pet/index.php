<?php start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/account.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/account-pet.css') ?>">
<?php end_section(); ?>

<div class="container">
  <div class="row">
    <!-- Sidebar -->
    <?php include_partial('client/sidebar-profile') ?>

    <!-- Main Content -->
    <div class="col-md-9 p-4">
      <!-- Tab Navigation -->
      <?php include_partial('client/tab-profile') ?>

      <!-- Pets Tab Content -->
      <div id="pets-tab" class="tab-content">
        <!-- Pets Table -->
        <div class="pets-table-card">
          <div class="pets-header p-4 d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-purple">Thú Cưng Của Bạn</h3>
            <div>
              <a href="<?= base_url('/user/pets/create') ?>" class="btn btn-outline-purple me-2">
                <i class="fas fa-plus"></i> Thêm thú cưng
              </a>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table pets-table">
              <thead>
                <tr>
                  <th>Tên thú cưng</th>
                  <th>Phân loại</th>
                  <th>Tuổi</th>
                  <th>Giống loài</th>
                  <th></th>
                </tr>
              </thead>

              <tbody>
                <?php if (!empty($metadata)): ?>
                  <?php $index = ($pagination['current'] - 1) * 1 + 1 ?>
                  <?php foreach ($metadata as $item): ?>
                    <tr class="pet-row" data-pet-id="<?= $item->pet_id ?>">
                      <td>
                        <div class="d-flex align-items-center">
                          <img src="<?= base_url($item->avatar_url ?: 'assets/images/default-pet.jpg') ?>"
                            alt="<?= htmlspecialchars($item->name) ?>" class="pet-avatar me-3">
                          <span class="pet-name">
                            <?= htmlspecialchars($item->name) ?>
                          </span>
                        </div>
                      </td>

                      <td>
                        <span class="pet-category <?= $item->type ?>">
                          <?= $item->getTypeName($item->type) ?>
                        </span>
                      </td>

                      <td class="pet-age">
                        <?= $item->age ?> (<?= $item->getAgeUnitName($item->age_unit) ?>)
                      </td>

                      <td class="pet-breed">
                        <?= htmlspecialchars($item->breed) ?>
                      </td>

                      <td>
                        <button class="btn pet-detail-btn" data-pet-id="<?= $item->pet_id ?>">
                          <i class="fas fa-chevron-right"></i>
                        </button>
                      </td>
                    </tr>

                    <tr class="pet-detail-row" id="detail-<?= $item->pet_id ?>">
                      <td colspan="5">
                        <div class="pet-detail-content">
                          <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="text-purple mb-0">
                              <i class="fas fa-paw me-2"></i>
                              Chi tiết thú cưng: <?= htmlspecialchars($item->name) ?>
                            </h4>
                            <div>
                              <button class="btn edit-btn me-2" onclick="editPet(<?= $item->pet_id ?>)">
                                <i class="fas fa-edit me-2"></i>Chỉnh sửa
                              </button>
                              <button class="btn close-btn" onclick="closePetDetail(<?= $item->pet_id ?>)">
                                <i class="fas fa-times me-1"></i>Đóng
                              </button>
                              <button class="btn delete-btn" data-pet-id="<?= $item->pet_id ?>" data-name="<?= $item->name ?>">
                                <i class="fas fa-times me-1"></i>Xoá
                              </button>
                            </div>
                          </div>

                          <div class="detail-section">
                            <h5><i class="fas fa-info-circle"></i>Thông tin cơ bản</h5>
                            <div class="detail-grid">
                              <div class="detail-item">
                                <label>Tên</label>
                                <span><?= htmlspecialchars($item->name) ?></span>
                              </div>
                              <div class="detail-item">
                                <label>Loại</label>
                                <span><?= $item->getTypeName($item->type) ?></span>
                              </div>
                              <div class="detail-item">
                                <label>Giống</label>
                                <span><?= htmlspecialchars($item->breed) ?></span>
                              </div>
                              <div class="detail-item">
                                <label>Tuổi</label>
                                <span><?= $item->age ?> (<?= $item->getAgeUnitName($item->age_unit) ?>)</span>
                              </div>
                              <div class="detail-item">
                                <label>Giới tính</label>
                                <span><?= $item->getGenderName($item->gender) ?></span>
                              </div>
                              <div class="detail-item">
                                <label>Màu sắc</label>
                                <span><?= htmlspecialchars($item->color) ?></span>
                              </div>
                            </div>
                          </div>

                          <div class="detail-section">
                            <h5><i class="fas fa-weight"></i>Thông số vật lý</h5>
                            <div class="detail-grid">
                              <div class="detail-item">
                                <label>Cân nặng</label>
                                <span><?= $item->weight ?> kg</span>
                              </div>
                              <div class="detail-item">
                                <label>Kích thước</label>
                                <span><?= $item->getSizeName($item->size) ?></span>
                              </div>
                            </div>
                          </div>

                          <div class="detail-section">
                            <h5><i class="fas fa-notes-medical"></i>Ghi chú y tế</h5>
                            <div class="detail-item">
                              <span><?= empty($item->medical_notes) ? 'Không có ghi chú' : htmlspecialchars($item->medical_notes) ?></span>
                            </div>
                          </div>

                          <div class="detail-section">
                            <h5><i class="fas fa-heart"></i>Ghi chú hành vi</h5>
                            <div class="detail-item">
                              <span><?= empty($item->behavioral_notes) ? 'Không có ghi chú' : htmlspecialchars($item->behavioral_notes) ?></span>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>

                    <?php $index++ ?>
                  <?php endforeach ?>
                <?php else: ?>
                  <tr class="pet-row">
                    <td colspan="5" class="text-center py-4">
                      <div class="empty-state">
                        <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Bạn chưa có thú cưng nào</p>
                        <a href="<?= base_url('/user/pets/create') ?>" class="btn btn-outline-purple">
                          <i class="fas fa-plus me-2"></i>Thêm thú cưng đầu tiên
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endif ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <?php if (!empty($metadata) && $pagination['last'] > 1): ?>
            <div class="p-4 pagination-wrapper d-flex justify-content-between align-items-center mt-4">
              <!-- Pagination Info -->
              <div class="pagination-info text-muted">
                Hiển thị <?= ($pagination['current'] - 1) * 1 + 1 ?>
                đến <?= min($pagination['current'] * 1, $pagination['total']) ?>
                trong tổng số <?= $pagination['total'] ?> thú cưng
              </div>

              <!-- Pagination Links -->
              <nav aria-label="Pet pagination">
                <ul class="pagination custom-pagination mb-0">
                  <!-- Previous Page -->
                  <?php if ($pagination['current'] > 1): ?>
                    <li class="page-item">
                      <a class="page-link" href="<?= base_url('/user/pets?page=' . ($pagination['current'] - 1)) ?>" aria-label="Previous">
                        <i class="fas fa-chevron-left"></i>
                      </a>
                    </li>
                  <?php else: ?>
                    <li class="page-item disabled">
                      <span class="page-link" aria-label="Previous">
                        <i class="fas fa-chevron-left"></i>
                      </span>
                    </li>
                  <?php endif ?>

                  <!-- Page Numbers -->
                  <?php
                  $start = max(1, $pagination['current'] - 2);
                  $end = min($pagination['last'], $pagination['current'] + 2);

                  // Adjust start if we're near the end
                  if ($end - $start < 4 && $pagination['last'] > 5) {
                    $start = max(1, $end - 4);
                  }

                  // Adjust end if we're near the start
                  if ($end - $start < 4 && $pagination['last'] > 5) {
                    $end = min($pagination['last'], $start + 4);
                  }
                  ?>

                  <!-- First page if not in range -->
                  <?php if ($start > 1): ?>
                    <li class="page-item">
                      <a class="page-link" href="<?= base_url('/user/pets?page=1') ?>">1</a>
                    </li>
                    <?php if ($start > 2): ?>
                      <li class="page-item disabled">
                        <span class="page-link">...</span>
                      </li>
                    <?php endif ?>
                  <?php endif ?>

                  <!-- Page range -->
                  <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i == $pagination['current'] ? 'active' : '' ?>">
                      <?php if ($i == $pagination['current']): ?>
                        <span class="page-link"><?= $i ?></span>
                      <?php else: ?>
                        <a class="page-link" href="<?= base_url('/user/pets?page=' . $i) ?>"><?= $i ?></a>
                      <?php endif ?>
                    </li>
                  <?php endfor ?>

                  <!-- Last page if not in range -->
                  <?php if ($end < $pagination['last']): ?>
                    <?php if ($end < $pagination['last'] - 1): ?>
                      <li class="page-item disabled">
                        <span class="page-link">...</span>
                      </li>
                    <?php endif ?>
                    <li class="page-item">
                      <a class="page-link" href="<?= base_url('/user/pets?page=' . $pagination['last']) ?>"><?= $pagination['last'] ?></a>
                    </li>
                  <?php endif ?>

                  <!-- Next Page -->
                  <?php if ($pagination['current'] < $pagination['last']): ?>
                    <li class="page-item">
                      <a class="page-link" href="<?= base_url('/user/pets?page=' . ($pagination['current'] + 1)) ?>" aria-label="Next">
                        <i class="fas fa-chevron-right"></i>
                      </a>
                    </li>
                  <?php else: ?>
                    <li class="page-item disabled">
                      <span class="page-link" aria-label="Next">
                        <i class="fas fa-chevron-right"></i>
                      </span>
                    </li>
                  <?php endif ?>
                </ul>
              </nav>
            </div>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php start_section('scripts') ?>
<script>
  $('.delete-btn').on('click', function(e) {
    e.preventDefault();
    const id = $(this).data('pet-id');
    const name = $(this).data('name');

    Swal.fire({
      title: `Bạn có chắc chắn muốn xóa thú cưng <b>'${name}'</b> này?`,
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
        loadAjaxStatus("start");
        $.ajax({
          url: `<?= base_url("/user/pets/") ?>/${id}`,
          method: 'DELETE',
          success: function(res) {
            swAlert("Thông báo", res.message, "success");
            setTimeout(() => {
              window.location.reload();
            }, 1500);
          },
          error: function(err) {
            swAlert("Thông báo", err.responseJSON.message, "error");
          },
          complete: function() {
            loadAjaxStatus("stop");
          },
        });
      }
    });
  });

  // Toggle pet detail accordion
  document.querySelectorAll('.pet-detail-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      const petId = this.getAttribute('data-pet-id');
      const detailRow = document.getElementById(`detail-${petId}`);
      const parentRow = this.closest('.pet-row');

      // Close all other details
      document.querySelectorAll('.pet-detail-row').forEach(row => {
        if (row.id !== `detail-${petId}`) {
          row.classList.remove('show');
        }
      });

      // Remove active state from all buttons and rows
      document.querySelectorAll('.pet-detail-btn').forEach(b => {
        if (b !== this) {
          b.classList.remove('active');
        }
      });

      document.querySelectorAll('.pet-row').forEach(row => {
        if (row !== parentRow) {
          row.classList.remove('active');
        }
      });

      // Toggle current detail
      if (detailRow.classList.contains('show')) {
        detailRow.classList.remove('show');
        this.classList.remove('active');
        parentRow.classList.remove('active');
      } else {
        detailRow.classList.add('show');
        this.classList.add('active');
        parentRow.classList.add('active');
      }
    });
  });

  // Close pet detail function
  function closePetDetail(petId) {
    const detailRow = document.getElementById(`detail-${petId}`);
    const btn = document.querySelector(`[data-pet-id="${petId}"]`);
    const parentRow = btn.closest('.pet-row');

    detailRow.classList.remove('show');
    btn.classList.remove('active');
    parentRow.classList.remove('active');
  }

  // Edit pet function
  function editPet(petId) {
    // Redirect to edit page
    window.location.href = `<?= base_url('/user/pets/edit/') ?>${petId}`;
  }

  // Close detail when clicking on pet row (optional)
  document.querySelectorAll('.pet-row').forEach(row => {
    row.addEventListener('click', function(e) {
      if (e.target.closest('.pet-detail-btn')) return;

      const petId = this.getAttribute('data-pet-id');
      const btn = this.querySelector('.pet-detail-btn');
      if (btn && !btn.classList.contains('active')) {
        btn.click();
      }
    });
  });

  // Show loading state when navigating
  document.querySelectorAll('.pagination .page-link').forEach(link => {
    link.addEventListener('click', function() {
      // Add loading state
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    });
  });

  // URL state management for pagination
  function updateURL(page) {
    const url = new URL(window.location);
    if (page === 1) {
      url.searchParams.delete('page');
    } else {
      url.searchParams.set('page', page);
    }
    window.history.replaceState({}, '', url);
  }

  // Handle pagination with AJAX (optional enhancement)
  function loadPage(page) {
    const url = `<?= base_url('/user/pets') ?>?page=${page}`;

    // Show loading
    document.querySelector('.pets-table tbody').innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-4">
          <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
          <p class="text-muted mt-2">Đang tải...</p>
        </td>
      </tr>
    `;

    // Fetch new data
    fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.text())
      .then(html => {
        // Parse response and update table
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContent = doc.querySelector('.pets-table-card');

        if (newContent) {
          document.querySelector('.pets-table-card').innerHTML = newContent.innerHTML;
          // Reinitialize event listeners
          initializePetEvents();
        }
      })
      .catch(error => {
        console.error('Error loading page:', error);
        location.reload(); // Fallback to full page reload
      });
  }

  // Initialize pet events after AJAX load
  function initializePetEvents() {
    // Reinitialize all the event listeners above
    // This would be called after AJAX pagination
  }
</script>

<style>
  .empty-state {
    padding: 2rem;
  }

  .pagination-info {
    font-size: 0.875rem;
  }

  .custom-pagination .page-link {
    border: 1px solid #dee2e6;
    color: #6c757d;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
  }

  .custom-pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
  }

  .custom-pagination .page-item.active .page-link {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
  }

  .custom-pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
    opacity: 0.5;
  }

  .pet-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
  }

  @media (max-width: 768px) {
    .pagination-wrapper {
      flex-direction: column;
      gap: 1rem;
    }

    .pagination-info {
      text-align: center;
    }

    .custom-pagination {
      justify-content: center;
    }
  }
</style>
<?php end_section() ?>