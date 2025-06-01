<?php start_section('title') ?>
<?= $title ?>
<?php end_section() ?>

<!-- Start: Main -->
<main class="flex-grow">
  <!-- Progress Header -->
  <div class="progress-container">
    <div class="container">
      <?php include_partial('client/stepper-cart') ?>
      <div class="row align-items-center mt-3">
        <div class="col-md-8">
          <h2 class="mb-1"><i class="fas fa-calendar-alt me-2"></i>Chọn Nhân Viên & Lịch Hẹn</h2>
          <p class="mb-0">Bước 3/4 - Vui lòng chọn nhân viên và thời gian phù hợp</p>
        </div>
        <div class="col-md-4 text-end">
          <a href="<?= base_url('/cart/info') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="container my-4">
    <!-- Service Summary -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title text-primary mb-3">
              <i class="fas fa-list-check me-2"></i>Dịch vụ đã chọn
            </h5>
            <div class="schedule-info">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <p class="mb-0"><strong>Tổng số dịch vụ cần đặt lịch:</strong>
                    <span class="badge bg-primary fs-6" id="total-services">
                      <?php
                      $totalQuantity = 0;
                      foreach ($selected_services as $service) {
                        $totalQuantity += $service['quantity'];
                      }
                      echo $totalQuantity;
                      ?>
                    </span>
                  </p>
                  <small class="text-muted">Bạn cần chọn đúng số lượng lịch hẹn tương ứng với số lượng dịch vụ</small>
                </div>
                <div class="col-md-4 text-end">
                  <span class="badge bg-info fs-6">
                    <i class="fas fa-calendar me-1"></i>
                    <?= format_day_vn($dateSelected) ?>
                  </span>
                </div>
              </div>
            </div>
            <div class="row">
              <?php foreach ($selected_services as $service): ?>
                <div class="col-md-6 mb-2">
                  <div class="d-flex align-items-center">
                    <img src="<?= show_pet_avatar($service['image']) ?>"
                      alt="<?= htmlspecialchars($service['name']) ?>"
                      class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="flex-grow-1">
                      <h6 class="mb-0"><?= htmlspecialchars($service['name']) ?></h6>
                      <small class="text-muted">
                        <?= format_price($service['price_new']) ?>
                      </small>
                    </div>
                    <span class="badge bg-primary">
                      SL: <?= $service['quantity'] ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Staff List -->
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="text-dark">
            <i class="fas fa-users me-2 text-primary"></i>Danh sách nhân viên
          </h4>
          <div class="d-flex align-items-center gap-3">
            <span class="text-muted">
              <i class="fas fa-info-circle me-1"></i>
              Chọn lịch hẹn phù hợp
            </span>
          </div>
        </div>

        <?php if (empty($staff_schedules)): ?>
          <!-- No Staff Available -->
          <div class="text-center py-5">
            <div class="card no-schedule-card">
              <div class="card-body py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted mb-3">Không có nhân viên nào có lịch trong ngày này</h4>
                <p class="text-muted mb-4">Vui lòng chọn ngày khác hoặc quay lại trang trước để thay đổi thông tin.</p>
                <a href="<?= base_url('/cart/info') ?>" class="btn btn-outline-primary">
                  <i class="fas fa-arrow-left me-2"></i>Quay lại chọn ngày khác
                </a>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="row" id="staff-list">
            <?php
            // Group schedules by staff
            $staffGroups = [];
            foreach ($staff_schedules as $schedule) {
              if (!isset($staffGroups[$schedule->account_id])) {
                $staffGroups[$schedule->account_id] = [
                  'staff' => $schedule->staff,
                  'schedules' => []
                ];
              }
              $staffGroups[$schedule->account_id]['schedules'][] = $schedule;
            }
            ?>

            <?php foreach ($staffGroups as $staffGroup): ?>
              <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card staff-card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                      <img src="<?= show_avatar($staffGroup['staff']->avatar_url) ?>"
                        alt="<?= htmlspecialchars($staffGroup['staff']->name) ?>"
                        class="rounded-circle staff-avatar me-3">
                      <div class="flex-grow-1">
                        <h5 class="card-title mb-1"><?= htmlspecialchars($staffGroup['staff']->name) ?></h5>
                        <div class="rating-stars mb-1">
                          <?php
                          $rating = floatval($staffGroup['staff']->rating ?? 0);
                          $fullStars = floor($rating);
                          $hasHalfStar = ($rating - $fullStars) >= 0.5;

                          for ($i = 0; $i < $fullStars; $i++): ?>
                            <i class="fas fa-star"></i>
                          <?php endfor; ?>

                          <?php if ($hasHalfStar): ?>
                            <i class="fas fa-star-half-alt"></i>
                          <?php endif; ?>

                          <?php for ($i = $fullStars + ($hasHalfStar ? 1 : 0); $i < 5; $i++): ?>
                            <i class="far fa-star"></i>
                          <?php endfor; ?>

                          <span class="ms-1 text-muted">(<?= number_format($rating, 1) ?>)</span>
                        </div>
                        <small class="text-muted">
                          <i class="fas fa-user-tie me-1"></i>Nhân viên chuyên nghiệp
                        </small>
                      </div>
                    </div>

                    <div class="mb-3">
                      <h6 class="text-primary mb-2">
                        <i class="fas fa-clock me-1"></i>Lịch có sẵn (<?= count($staffGroup['schedules']) ?> slot)
                      </h6>
                      <div class="available-slots">
                        <?php
                        // Sort schedules by time
                        usort($staffGroup['schedules'], function ($a, $b) {
                          return strcmp($a->time_slot->start_time, $b->time_slot->start_time);
                        });

                        foreach ($staffGroup['schedules'] as $schedule):
                          if ($schedule->is_available): ?>
                            <span class="badge time-slot-badge"
                              data-staff-id="<?= $schedule->account_id ?>"
                              data-schedule-id="<?= $schedule->staff_schedule_id ?>"
                              data-time-slot-id="<?= $schedule->time_slot_id ?>"
                              data-staff-name="<?= htmlspecialchars($staffGroup['staff']->name) ?>"
                              data-time-range="<?= $schedule->time_slot->start_time ?> - <?= $schedule->time_slot->end_time ?>">
                              <i class="fas fa-clock me-1"></i>
                              <?= date('H:i', strtotime($schedule->time_slot->start_time)) ?> -
                              <?= date('H:i', strtotime($schedule->time_slot->end_time)) ?>
                            </span>
                        <?php endif;
                        endforeach; ?>
                      </div>
                    </div>

                    <div class="staff-info">
                      <small class="text-muted d-block">
                        <i class="fas fa-envelope me-1"></i>
                        <?= htmlspecialchars($staffGroup['staff']->email) ?>
                      </small>
                      <?php if (!empty($staffGroup['staff']->phone)): ?>
                        <small class="text-muted d-block">
                          <i class="fas fa-phone me-1"></i>
                          <?= htmlspecialchars($staffGroup['staff']->phone) ?>
                        </small>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Selected Appointments Summary -->
          <div class="row mt-4" id="selected-summary" style="display: none;">
            <div class="col-12">
              <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                  <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>Lịch hẹn đã chọn
                  </h5>
                </div>
                <div class="card-body">
                  <div id="selected-appointments-list">
                    <!-- Selected appointments will be displayed here -->
                  </div>
                  <div class="error-message" id="validation-error">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <span id="error-text"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Fixed Selection Counter and Next Button -->
  <button class="selected-count" id="next-step-btn" style="display: none;" onclick="proceedToNext()">
    <div class="d-flex align-items-center">
      <div class="me-3">
        <i class="fas fa-calendar-check fa-lg"></i>
      </div>
      <div>
        <div class="fw-bold">
          <span id="selected-count">0</span>/<span id="required-count"><?= $totalQuantity ?></span> đã chọn
        </div>
        <small>Tiếp tục →</small>
      </div>
    </div>
  </button>
</main>

<?php start_section('scripts') ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const totalServices = <?= $totalQuantity ?>;
    let selectedAppointments = [];

    // Handle time slot selection
    document.querySelectorAll('.time-slot-badge').forEach(badge => {
      badge.addEventListener('click', function() {
        if (this.classList.contains('disabled')) return;

        const scheduleId = this.dataset.scheduleId;
        const staffId = this.dataset.staffId;
        const timeSlotId = this.dataset.timeSlotId;
        const staffName = this.dataset.staffName;
        const timeRange = this.dataset.timeRange;

        if (this.classList.contains('selected')) {
          // Remove selection
          this.classList.remove('selected');
          selectedAppointments = selectedAppointments.filter(apt => apt.scheduleId !== scheduleId);
        } else {
          // Check if already selected for this staff
          if (selectedAppointments.length > 0 && !selectedAppointments.some(apt => apt.staffId === staffId)) {
            showValidationError('Bạn chỉ có thể chọn một lịch cho một nhân viên!');
            swAlert('Thông báo', 'Bạn chỉ có thể chọn một lịch cho một nhân viên!', 'warning');
            return;
          }

          // Add selection if not at limit
          if (selectedAppointments.length < totalServices) {
            this.classList.add('selected');
            selectedAppointments.push({
              scheduleId: scheduleId,
              staffId: staffId,
              timeSlotId: timeSlotId,
              staffName: staffName,
              timeRange: timeRange
            });
          } else {
            showValidationError('Bạn chỉ có thể chọn tối đa ' + totalServices + ' lịch hẹn!');
            return;
          }
        }

        updateSelectionDisplay();
        validateSelection();
      });
    });

    function updateSelectionDisplay() {
      const selectedCount = selectedAppointments.length;
      const nextBtn = document.getElementById('next-step-btn');
      const selectedSummary = document.getElementById('selected-summary');
      const appointmentsList = document.getElementById('selected-appointments-list');

      // Update counter
      document.getElementById('selected-count').textContent = selectedCount;

      if (selectedCount > 0) {
        nextBtn.style.display = 'block';
        selectedSummary.style.display = 'block';

        // Update appointments list
        appointmentsList.innerHTML = selectedAppointments.map((apt, index) => `
                <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">${index + 1}</span>
                        <div>
                            <strong>${apt.staffName}</strong><br>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>${apt.timeRange}
                            </small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeAppointment('${apt.scheduleId}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');
      } else {
        nextBtn.style.display = 'none';
        selectedSummary.style.display = 'none';
      }

      // Update disabled state for remaining slots
      updateSlotStates();
    }

    function updateSlotStates() {
      const allSlots = document.querySelectorAll('.time-slot-badge');
      const isAtLimit = selectedAppointments.length >= totalServices;

      allSlots.forEach(slot => {
        if (!slot.classList.contains('selected')) {
          if (isAtLimit) {
            slot.classList.add('disabled');
          } else {
            slot.classList.remove('disabled');
          }
        }
      });
    }

    function validateSelection() {
      const selectedCount = selectedAppointments.length;
      const errorElement = document.getElementById('validation-error');
      const errorText = document.getElementById('error-text');

      if (selectedCount === 0) {
        hideValidationError();
        return false;
      }

      if (selectedCount !== totalServices) {
        showValidationError(`Bạn cần chọn đúng ${totalServices} lịch hẹn. Hiện tại đã chọn ${selectedCount}.`);
        return false;
      }

      hideValidationError();
      return true;
    }

    function showValidationError(message) {
      const errorElement = document.getElementById('validation-error');
      const errorText = document.getElementById('error-text');
      errorText.textContent = message;
      errorElement.style.display = 'block';

      // Auto hide after 3 seconds
      setTimeout(() => {
        hideValidationError();
      }, 3000);
    }

    function hideValidationError() {
      const errorElement = document.getElementById('validation-error');
      errorElement.style.display = 'none';
    }

    // Global function for removing appointments
    window.removeAppointment = function(scheduleId) {
      // Remove from selected list
      selectedAppointments = selectedAppointments.filter(apt => apt.scheduleId !== scheduleId);

      // Remove selected class from badge
      const badge = document.querySelector(`[data-schedule-id="${scheduleId}"]`);
      if (badge) {
        badge.classList.remove('selected');
      }

      updateSelectionDisplay();
      validateSelection();
    };

    // Global function for proceeding to next step
    window.proceedToNext = function() {
      if (!validateSelection()) {
        swAlert({
          title: 'Lỗi',
          text: 'Vui lòng chọn đúng số lượng lịch hẹn trước khi tiếp tục!',
          icon: 'error',
          timer: 3000,
          showConfirmButton: false
        })

        showValidationError('Vui lòng chọn đúng số lượng lịch hẹn trước khi tiếp tục!');
        return;
      }

      // Show loading
      const nextBtn = document.getElementById('next-step-btn');
      const originalContent = nextBtn.innerHTML;
      nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
      nextBtn.disabled = true;

      // Prepare data to send
      const appointmentData = {
        selected_appointments: selectedAppointments,
        date: '<?= $dateSelected ?>',
        total_required: totalServices,
        staff_id: parseInt(selectedAppointments[0].staffId),
      };

      console.log(appointmentData);

      loadAjaxStatus('start')

      // Send to server
      $.ajax({
        url: '<?= base_url('/cart/save-staff-schedule') ?>',
        type: 'POST',
        data: JSON.stringify(appointmentData),
        contentType: 'application/json',
        dataType: 'json',
        success: function(data) {
          swAlert('Thông báo', data.message, 'success')

          setTimeout(function() {
            // Redirect to the next step: review booking
            window.location.href = data.redirect_url || '<?= base_url('/cart/finished')?>';
          }, 2000); // Redirect after 2 seconds
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
          showValidationError('Có lỗi xảy ra, vui lòng thử lại!');
        },
        complete: function() {
          // Re-enable button
          loadAjaxStatus('stop')
          nextBtn.innerHTML = originalContent;
          nextBtn.disabled = false;
        }
      })
    };
  });
</script>
<?php end_section() ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cart.css') ?>">
<?php end_section(); ?>