<?php
// header.php - sử dụng Auth Singleton
use App\Core\Auth;
use App\Models\Pet;

$auth = Auth::getInstance();
$user = $auth->user();

// Lấy danh sách pets của user hiện tại
$pets = Pet::getMyPets($user['user_id']);
?>

<?php start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cart.css') ?>">
<style>
  .pet-selection-container {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
  }

  .pet-card {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .pet-card:hover {
    border-color: #6f42c1;
    background-color: #f8f9fa;
  }

  .pet-card.selected {
    border-color: #6f42c1;
    background-color: #f3f0ff;
  }

  .pet-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
    border: 2px solid #e0e0e0;
  }

  .pet-info {
    flex: 1;
  }

  .pet-name {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
  }

  .pet-details {
    font-size: 0.9em;
    color: #666;
  }

  .no-pets-message {
    text-align: center;
    padding: 30px;
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
  }

  .no-pets-icon {
    font-size: 3em;
    color: #6c757d;
    margin-bottom: 15px;
  }

  .error-message {
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 5px;
    display: none;
  }

  .multiple-pets-container {
    margin-top: 15px;
  }

  .selected-pets-list {
    margin-top: 10px;
  }

  .selected-pet-tag {
    display: inline-block;
    background-color: #6f42c1;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    margin-right: 5px;
    margin-bottom: 5px;
    font-size: 0.875em;
  }

  .selected-pet-tag .remove-pet {
    margin-left: 8px;
    cursor: pointer;
    font-weight: bold;
  }
</style>
<?php end_section(); ?>

<!-- Start: Main -->
<main class="container mt-4">
  <!-- Progress Steps -->
  <div class="progress-container">
    <div class="container-xl">
      <?php include_partial('client/stepper-cart') ?>
    </div>
  </div>

  <div class="bg-body step-2-content active">
    <div class="form-container">
      <h5 class="text-purple text-font">Thông Tin</h5>
      <form id="stepForm">
        <!-- Phần Nhập Thông Tin -->
        <div class="form-section mt-2">
          <h6>Thông tin của bạn</h6>
          <div class="mb-3">
            <label class="form-label"><b>1. Họ và tên</b></label>
            <input readonly type="text" class="form-control" id="name" placeholder="Hãy nhập họ tên của bạn" value="<?= $user['name'] ?>">
          </div>
          <div class="mb-3">
            <label class="form-label"><b>2. Số điện thoại</b></label>
            <input readonly type="text" class="form-control" id="phone" placeholder="Hãy nhập số điện thoại của bạn" value="<?= $user['phone'] ?>">
          </div>
          <div class="mb-3">
            <label class="form-label"><b>3. Địa chỉ</b></label>
            <input readonly type="text" class="form-control" id="address" placeholder="Hãy nhập địa chỉ của bạn" value="<?= $user['address'] ?>">
          </div>

          <!-- Phần Chọn Thú Cưng -->
          <div class="mb-3">
            <label class="form-label"><b>4. Chọn thú cưng</b></label>

            <?php if (empty($pets)): ?>
              <!-- Hiển thị khi không có pets -->
              <div class="no-pets-message">
                <div class="no-pets-icon">
                  <i class="fas fa-paw"></i>
                </div>
                <h6>Bạn chưa có thú cưng nào</h6>
                <p class="text-muted mb-3">Vui lòng thêm thông tin thú cưng của bạn trước khi đặt lịch dịch vụ.</p>
                <a href="<?= base_url('/user/pets/create') ?>" class="btn btn-purple">
                  <i class="fas fa-plus"></i> Thêm thú cưng mới
                </a>
              </div>
            <?php else: ?>
              <!-- Hiển thị danh sách pets -->
              <div class="pet-selection-container">
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="allowMultiplePets">
                    <label class="form-check-label" for="allowMultiplePets">
                      Chọn nhiều thú cưng
                    </label>
                  </div>
                </div>

                <div id="petsContainer">
                  <?php foreach ($pets as $pet): ?>
                    <div class="pet-card" data-pet-id="<?= $pet->pet_id ?>" onclick="selectPet(<?= $pet->pet_id ?>)">
                      <input type="radio" name="selected_pet" value="<?= $pet->pet_id ?>" style="display: none;">
                      <input type="checkbox" name="selected_pets[]" value="<?= $pet->pet_id ?>" style="display: none;">

                      <img src="<?= base_url($pet->avatar_url ?: 'assets/images/default-pet.jpg') ?>" alt="<?= htmlspecialchars($pet->name) ?>" class="pet-avatar">

                      <div class="pet-info">
                        <div class="pet-name"><?= htmlspecialchars($pet->name) ?></div>
                        <div class="pet-details">
                          <?= $pet->getTypeName($pet->type) ?>
                          <?php if ($pet->breed): ?>
                            - <?= htmlspecialchars($pet->breed) ?>
                          <?php endif; ?>
                          <?php if ($pet->age): ?>
                            - <?= $pet->age ?> <?= $pet->getAgeUnitName($pet->age_unit) ?>
                          <?php endif; ?>
                          <?php if ($pet->weight): ?>
                            - <?= $pet->weight ?>kg
                          <?php endif; ?>
                          <?php if ($pet->gender): ?>
                            - <?= $pet->getGenderName($pet->gender) ?>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div class="pet-selection-indicator">
                        <i class="fas fa-check-circle text-purple" style="display: none;"></i>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <!-- Hiển thị pets đã chọn khi chế độ multiple -->
                <div id="selectedPetsDisplay" class="selected-pets-list" style="display: none;">
                  <label class="form-label"><b>Thú cưng đã chọn:</b></label>
                  <div id="selectedPetsTags"></div>
                </div>

                <div class="error-message" id="petSelectionError">
                  Vui lòng chọn ít nhất một thú cưng
                </div>
              </div>

              <!-- Link thêm pets mới -->
              <div class="text-center mt-2">
                <a href="<?= base_url('/user/pets/create') ?>" class="btn btn-outline-purple btn-sm">
                  <i class="fas fa-plus"></i> Thêm thú cưng mới
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Phần Hình Thức Thanh Toán -->
        <div class="form-section">
          <h6>Hình Thức Thanh Toán</h6>
          <div class="mb-2">
            <div class="payment-method mb-0">
              <label>
                <input type="radio" name="paymentMethod" value="vnpay">
                <img src="<?= base_url('/assets/images/VNPAY.webp') ?>" alt="VNPay">
              </label>
              <label>
                <input type="radio" name="paymentMethod" value="cash">
                <i class="fas fa-money-bill-wave"></i>
                <b>Tiền mặt</b>
              </label>
            </div>
            <div class="error-message" id="paymentSelectionError">
              Vui lòng chọn hình thức thanh toán
            </div>

            <div id="vnpayDetails" class="payment-details">
              <p>Thanh toán qua VNPay. Bạn sẽ được chuyển đến trang thanh toán VNPay để hoàn tất giao dịch.</p>
            </div>

            <div id="cashDetails" class="payment-details">
              <p>Thanh toán bằng tiền mặt tại quầy khi nhận dịch vụ.</p>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label"><b>5. Thời gian đặt lịch</b></label>
            <div class="date-time-inputs">
              <div class="input-group">
                <input type="date" class="form-control p-2" id="date" aria-label="Ngày đặt lịch" min="<?= date('Y-m-d') ?>">
              </div>
            </div>
            <div class="error-message" id="dateSelectionError">
              Vui lòng chọn ngày đặt lịch
            </div>
          </div>
        </div>

        <!-- Nút Trở Về và Tiếp Theo -->
        <div class="button-group">
          <a href="/cart" type="button" class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center gap-2">
            <i class="fas fa-arrow-left"></i> Trở lại
          </a>
          <button type="button" class="btn btn-purple" onclick="validateAndProceed()" <?= empty($pets) ? 'disabled' : '' ?>>
            Tiếp Theo
          </button>
        </div>
      </form>
    </div>
  </div>
  <!-- Snackbar container -->
  <div class="toastBox"></div>
</main>
<!-- End: Main -->

<?php start_section('scripts'); ?>
<script>
  let selectedPets = [];
  let isMultipleMode = false;

  // Toggle multiple pets selection mode
  document.getElementById('allowMultiplePets').addEventListener('change', function() {
    isMultipleMode = this.checked;
    selectedPets = [];
    updatePetSelection();
    updateSelectedPetsDisplay();
  });

  // Select pet function
  function selectPet(petId) {
    if (isMultipleMode) {
      // Multiple selection mode
      const index = selectedPets.indexOf(petId);
      if (index > -1) {
        selectedPets.splice(index, 1);
      } else {
        selectedPets.push(petId);
      }
    } else {
      // Single selection mode
      selectedPets = [petId];
    }

    updatePetSelection();
    updateSelectedPetsDisplay();
    hideError('petSelectionError');
  }

  // Update visual selection
  function updatePetSelection() {
    const petCards = document.querySelectorAll('.pet-card');
    petCards.forEach(card => {
      const petId = parseInt(card.getAttribute('data-pet-id'));
      const isSelected = selectedPets.includes(petId);
      const indicator = card.querySelector('.pet-selection-indicator i');
      const radioInput = card.querySelector('input[type="radio"]');
      const checkboxInput = card.querySelector('input[type="checkbox"]');

      if (isSelected) {
        card.classList.add('selected');
        indicator.style.display = 'block';
        if (isMultipleMode) {
          checkboxInput.checked = true;
          radioInput.checked = false;
        } else {
          radioInput.checked = true;
          checkboxInput.checked = false;
        }
      } else {
        card.classList.remove('selected');
        indicator.style.display = 'none';
        radioInput.checked = false;
        checkboxInput.checked = false;
      }
    });
  }

  // Update selected pets display
  function updateSelectedPetsDisplay() {
    const display = document.getElementById('selectedPetsDisplay');
    const tagsContainer = document.getElementById('selectedPetsTags');

    if (isMultipleMode && selectedPets.length > 0) {
      display.style.display = 'block';
      tagsContainer.innerHTML = '';

      selectedPets.forEach(petId => {
        const petCard = document.querySelector(`[data-pet-id="${petId}"]`);
        const petName = petCard.querySelector('.pet-name').textContent;

        const tag = document.createElement('span');
        tag.className = 'selected-pet-tag';
        tag.innerHTML = `${petName} <span class="remove-pet" onclick="removePet(${petId})">&times;</span>`;
        tagsContainer.appendChild(tag);
      });
    } else {
      display.style.display = 'none';
    }
  }

  // Remove pet from selection
  function removePet(petId) {
    const index = selectedPets.indexOf(petId);
    if (index > -1) {
      selectedPets.splice(index, 1);
      updatePetSelection();
      updateSelectedPetsDisplay();
    }
  }

  // Show error message
  function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (message) {
      errorElement.textContent = message;
    }
    errorElement.style.display = 'block';
  }

  // Hide error message
  function hideError(elementId) {
    const errorElement = document.getElementById(elementId);
    errorElement.style.display = 'none';
  }

  // Show toast notification
  function showToast(message, type = 'error') {
    const toastBox = document.querySelector('.toastBox');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
    ${message}
  `;

    toastBox.appendChild(toast);

    setTimeout(() => {
      toast.remove();
    }, 3000);
  }

  // Validation function
  function validateAndProceed() {
    let isValid = true;

    // Validate pet selection
    if (selectedPets.length === 0) {
      showError('petSelectionError');
      showToast('Vui lòng chọn ít nhất một thú cưng');
      isValid = false;
    }

    // Validate payment method
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
    if (!paymentMethod) {
      showError('paymentSelectionError');
      showToast('Vui lòng chọn hình thức thanh toán');
      isValid = false;
    }

    // Validate date
    const selectedDate = document.getElementById('date').value;
    if (!selectedDate) {
      showError('dateSelectionError');
      showToast('Vui lòng chọn ngày đặt lịch');
      isValid = false;
    } else {
      const today = new Date();
      const selected = new Date(selectedDate);

      // Check if selected date is today or in the future
      today.setHours(0, 0, 0, 0);
      selected.setHours(0, 0, 0, 0);
      if (selected < today) {
        showError('dateSelectionError', 'Ngày đặt lịch không thể là ngày trong quá khứ');
        showToast('Ngày đặt lịch không thể là ngày trong quá khứ');
        isValid = false;
      }
    }

    if (isValid) {
      // Store selected data
      const bookingData = {
        pets: selectedPets,
        paymentMethod: paymentMethod.value,
        date: selectedDate,
        userInfo: {
          name: document.getElementById('name').value,
          phone: document.getElementById('phone').value,
          address: document.getElementById('address').value
        }
      };

      // Disable button to prevent double submit
      const submitButton = document.querySelector('.btn-purple');
      submitButton.disabled = true;
      submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

      loadAjaxStatus('start')

      // Send AJAX request to save customer info
      $.ajax({
        url: '/cart/save-customer-info',
        method: 'POST',
        data: JSON.stringify(bookingData),
        dataType: 'json',
        contentType: 'application/json',
        success: function(data) {
          swAlert('Thông báo', data.message, 'success');
          // Redirect after short delay
          setTimeout(() => {
            window.location.href = data.metadata.redirect_url;
          }, 1000);
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', error);
          console.error('Response:', xhr.responseText);
          swAlert('Thông báo', 'Có lỗi xảy ra, vui lòng thử lại!', 'error');
          // Re-enable button
          submitButton.disabled = false;
          submitButton.innerHTML = 'Tiếp Theo';
        },
        complete: function() {
          loadAjaxStatus('stop')
        }
      });
    }
  }

  // Initialize date picker with minimum date as today
  document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate());

    dateInput.min = tomorrow.toISOString().split('T')[0];
  });
</script>
<?php end_section(); ?>