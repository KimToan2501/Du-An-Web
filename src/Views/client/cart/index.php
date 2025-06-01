<?php
// header.php - sử dụng Auth Singleton
use App\Core\Auth;

$auth = Auth::getInstance();

$isLoggedIn = $auth->isLoggedIn();

$total_price = 0;
?>


<?php start_section('title'); ?>
<?= $title ?>
<?php end_section(); ?>

<?php start_section('links'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cart.css') ?>">
<?php end_section(); ?>

<!-- Start: Main -->
<main class="container mt-4">
  <!-- Progress Steps -->
  <div class="progress-container">
    <div class="container-xl">
      <?php include_partial('client/stepper-cart') ?>
    </div>
  </div>

  <div class="row step-content step-1-content active">
    <div class="col-lg-8">
      <div class="table-responsive">
        <table class="table bg-white rounded">
          <thead class="table-light">
            <tr>
              <th><input type="checkbox" id="mainCheckbox"></th>
              <th>Dịch Vụ</th>
              <th>Giá Tiền</th>
              <th>Số Lượng</th>
              <th>Thành Tiền</th>
              <th></th>
            </tr>
          </thead>
          
          <tbody id="service-list">
            <?php if (!empty($cart)): ?>
              <?php foreach ($cart as $id => $item) : ?>
                <?php $total_price += $item['quantity'] * $item['price_new'] ?>
                <tr data-service-id="<?= $item['service_id'] ?>">
                  <td>
                    <input type="checkbox" class="service-check"
                      data-name="<?= htmlspecialchars($item['name']) ?>"
                      data-price="<?= $item['price_new'] ?>"
                      data-quantity="<?= $item['quantity'] ?>"
                      data-id="<?= $id ?>">
                  </td>

                  <td>
                    <div class="d-flex align-items-center">
                      <img src="<?= base_url($item['image'] ?? '/assets/images/Product/CATTIALONGTAOKIEU.webp') ?>"
                        class="me-3 img-fluid" width="60" height="60"
                        alt="<?= htmlspecialchars($item['name']) ?>">
                      <div>
                        <div class="fw-medium"><?= htmlspecialchars($item['name']) ?></div>
                        <?php if (!empty($item['description'])): ?>
                          <small class="text-muted"><?= htmlspecialchars($item['description']) ?></small>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>

                  <td class="text-purple">
                    <?php if ($item['discount_percent']): ?>
                      <span class="fw-medium"><?= format_price($item['price_new']) ?></span>
                      <span class="price-old"><?= format_price($item['price']) ?></span>
                    <?php else: ?>
                      <span class="fw-medium"><?= format_price($item['price_new']) ?></span>
                    <?php endif; ?>
                  </td>

                  <td>
                    <div class="input-group quantity-group" style="width: 120px;">
                      <button class="btn btn-outline-secondary btn-minus" type="button">
                        <i class="fa fa-minus"></i>
                      </button>
                      <input type="number" class="form-control text-center quantity-input no-controls"
                        value="<?= $item['quantity'] ?>"
                        data-id="<?= $item['service_id'] ?>"
                        min="1" max="99">
                      <button class="btn btn-outline-secondary btn-plus" type="button">
                        <i class="fa fa-plus"></i>
                      </button>
                    </div>
                  </td>

                  <td class="text-purple total-price fw-medium">
                    <?= format_price($item['price_new'] * $item['quantity']) ?>
                  </td>

                  <td>
                    <a href="<?= base_url('/cart/remove/' . $item['service_id']) ?>"
                      class="btn btn-sm btn-outline-danger">
                      <i class="fa fa-trash"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center empty-cart">
                  <i class="fa fa-shopping-cart"></i>
                  <div class="mt-2">Không có dịch vụ nào trong giỏ hàng</div>
                  <a href="<?= base_url('/service') ?>" class="btn btn-purple mt-3">
                    <i class="fa fa-plus me-2"></i>Thêm dịch vụ
                  </a>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="order-box">
        <h5>Hóa Đơn <span class="text-muted fs-6">(<span id="selected-count">0</span> dịch vụ được chọn)</span></h5>

        <div id="invoice-items" class="border-bottom my-3 pb-3">
          <!-- Danh sách items được chọn sẽ được cập nhật bằng JavaScript -->
        </div>

        <div class="mb-3">
          <label for="coupon" class="form-label fw-medium">Mã Giảm Giá</label>
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Nhập mã giảm giá" id="coupon">
            <button class="btn btn-purple" id="apply-coupon" type="button">
              <i class="fa fa-check me-1"></i>Áp dụng
            </button>
          </div>
        </div>

        <div class="d-flex justify-content-between mb-2">
          <span>Tạm tính:</span>
          <span id="subtotal" class="fw-medium"><?= format_price($total_price) ?></span>
        </div>

        <div class="d-flex justify-content-between mb-2">
          <span>Giảm giá:</span>
          <span id="discount-amount" class="text-success">0%</span>
        </div>

        <hr>

        <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
          <span>Tổng Cộng:</span>
          <span id="total-price" class="text-purple"><?= format_price($total_price) ?></span>
        </div>

        <button class="btn btn-purple w-100" id="btnXacNhan" type="button">
          <i class="fa fa-arrow-right me-2"></i>Tiếp tục thanh toán
        </button>
      </div>
    </div>
  </div>
</main>
<!-- End: Main -->

<!-- Start: Script -->
<?php start_section('scripts'); ?>
<script>
  const isLoggedIn = <?= json_encode($isLoggedIn) ?>;

  $(document).ready(function() {
    // Xử lý nút tăng số lượng
    $(document).on('click', '.btn-plus', function(e) {
      e.preventDefault();
      const quantityInput = $(this).siblings('.quantity-input');
      const currentQuantity = parseInt(quantityInput.val());
      const newQuantity = currentQuantity + 1;

      if (newQuantity <= 99) {
        quantityInput.val(newQuantity);
        updateQuantity(quantityInput, newQuantity);
      }
    });

    // Xử lý nút giảm số lượng
    $(document).on('click', '.btn-minus', function(e) {
      e.preventDefault();
      const quantityInput = $(this).siblings('.quantity-input');
      const currentQuantity = parseInt(quantityInput.val());

      if (currentQuantity > 1) {
        const newQuantity = currentQuantity - 1;
        quantityInput.val(newQuantity);
        updateQuantity(quantityInput, newQuantity);
      }
    });

    // Xử lý nhập trực tiếp số lượng
    $(document).on('change', '.quantity-input', function() {
      const newQuantity = parseInt($(this).val());

      if (newQuantity < 1 || isNaN(newQuantity)) {
        $(this).val(1);
        updateQuantity($(this), 1);
      } else if (newQuantity > 99) {
        $(this).val(99);
        updateQuantity($(this), 99);
      } else {
        updateQuantity($(this), newQuantity);
      }
    });

    // Hàm cập nhật số lượng qua Ajax - ĐƯỢC CẬP NHẬT
    function updateQuantity(inputElement, quantity) {
      const serviceId = inputElement.data('id');
      const row = inputElement.closest('tr');
      const checkbox = row.find('.service-check');
      const isSelected = checkbox.is(':checked');

      // Lưu giá trị cũ để rollback nếu lỗi
      const oldQuantity = inputElement.val();
      const oldCheckboxQuantity = checkbox.data('quantity');
      inputElement.data('old-value', oldQuantity);

      // Hiển thị loading
      loadAjaxStatus('start');
      showLoadingState(row);

      $.ajax({
        url: '<?= base_url("/cart/update/quantity") ?>',
        method: 'POST',
        data: {
          id: serviceId,
          quantity: quantity
        },
        success: function(response) {
          // 1. Cập nhật thành tiền cho dòng hiện tại
          updateRowTotal(row, quantity);

          // 2. Cập nhật data-quantity trong checkbox (QUAN TRỌNG)
          checkbox.attr('data-quantity', quantity);
          checkbox.data('quantity', quantity);

          // 3. Nếu item đã được chọn, cập nhật toàn bộ hóa đơn
          if (isSelected) {
            updateSelectedItems();
          }

          // Hiển thị thông báo thành công
          swAlert({
            icon: 'success',
            title: 'Thành công!',
            text: `Cập nhật số lượng ${isSelected ? 'và hóa đơn ' : ''}thành công!`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
          });

          hideLoadingState(row);
          loadAjaxStatus('end');
        },
        error: function(xhr, status, error) {
          // Hiển thị thông báo lỗi
          swAlert({
            icon: 'error',
            title: 'Lỗi!',
            text: 'Có lỗi xảy ra khi cập nhật số lượng!',
            confirmButtonColor: '#2e0a5a'
          });

          // Rollback về giá trị cũ
          inputElement.val(oldQuantity);
          checkbox.attr('data-quantity', oldCheckboxQuantity);
          checkbox.data('quantity', oldCheckboxQuantity);

          hideLoadingState(row);
          loadAjaxStatus('end');
        }
      });
    }

    // Cập nhật thành tiền cho từng dòng - ĐƯỢC CẬP NHẬT
    function updateRowTotal(row, quantity) {
      const priceElement = row.find('td:nth-child(3) .fw-medium');
      const priceText = priceElement.text().replace(/[^\d]/g, '');
      const price = parseInt(priceText);

      if (!isNaN(price)) {
        const total = price * quantity;
        const formattedTotal = formatPrice(total);
        row.find('.total-price').text(formattedTotal);
      }
    }

    // Cập nhật danh sách items đã chọn - ĐƯỢC CẬP NHẬT
    function updateSelectedItems() {
      const selectedItems = [];
      let selectedTotal = 0;

      $('.service-check:checked').each(function() {
        const row = $(this).closest('tr');
        const serviceName = $(this).data('name');
        const price = parseInt($(this).data('price'));

        // LẤY QUANTITY MỚI NHẤT từ input, không phải từ data attribute
        const quantityInput = row.find('.quantity-input');
        const quantity = parseInt(quantityInput.val());

        // Đảm bảo data attribute được đồng bộ
        $(this).attr('data-quantity', quantity);
        $(this).data('quantity', quantity);

        const totalPrice = price * quantity;

        selectedItems.push({
          name: serviceName,
          price: totalPrice,
          quantity: quantity,
          unitPrice: price
        });

        selectedTotal += totalPrice;
      });

      // Cập nhật số lượng đã chọn
      $('#selected-count').text(selectedItems.length);

      // Cập nhật danh sách items trong hóa đơn
      const invoiceItems = $('#invoice-items');
      invoiceItems.empty();

      if (selectedItems.length > 0) {
        selectedItems.forEach(item => {
          invoiceItems.append(`
        <div class="d-flex justify-content-between small mb-1">
          <span class="text-truncate me-2">${item.name} (x${item.quantity})</span>
          <span class="text-nowrap">${formatPrice(item.price)}</span>
        </div>
      `);
        });

        // Cập nhật tạm tính
        $('#subtotal').text(formatPrice(selectedTotal));

        // Tính lại tổng với giảm giá hiện tại
        recalculateTotal(selectedTotal);

      } else {
        invoiceItems.append('<small class="text-muted">Chưa chọn dịch vụ nào</small>');
        $('#subtotal').text(formatPrice(0));
        $('#total-price').text(formatPrice(0));
      }
    }

    // Hàm tính lại tổng cộng với giảm giá - MỚI
    function recalculateTotal(subtotal) {
      const discountPercent = parseDiscountPercent();
      const discountAmount = subtotal * discountPercent / 100;
      const finalTotal = subtotal - discountAmount;

      $('#total-price').text(formatPrice(finalTotal));
    }

    // Hàm lấy % giảm giá hiện tại - MỚI
    function parseDiscountPercent() {
      const discountText = $('#discount-amount').text();
      const match = discountText.match(/(\d+)%/);
      return match ? parseInt(match[1]) : 0;
    }

    // Xử lý checkbox chọn tất cả
    $('#mainCheckbox').change(function() {
      const isChecked = $(this).is(':checked');
      $('.service-check').prop('checked', isChecked);
      updateSelectedItems();
    });

    // Xử lý checkbox từng item - ĐƯỢC CẬP NHẬT
    $(document).on('change', '.service-check', function() {
      const row = $(this).closest('tr');
      const quantityInput = row.find('.quantity-input');
      const currentQuantity = parseInt(quantityInput.val());

      // Đồng bộ data-quantity với input value khi check/uncheck
      $(this).attr('data-quantity', currentQuantity);
      $(this).data('quantity', currentQuantity);

      updateSelectedItems();

      // Cập nhật trạng thái checkbox chính
      const totalCheckboxes = $('.service-check').length;
      const checkedCheckboxes = $('.service-check:checked').length;

      $('#mainCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes);
      $('#mainCheckbox').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
    });

    // Xử lý mã giảm giá - ĐƯỢC CẬP NHẬT
    $('#apply-coupon').click(function() {
      const couponCode = $('#coupon').val().trim();

      if (!couponCode) {
        swAlert({
          icon: 'warning',
          title: 'Cảnh báo!',
          text: 'Vui lòng nhập mã giảm giá!',
          confirmButtonColor: '#2e0a5a'
        });
        return;
      }

      // Disable button và hiển thị loading
      $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>Đang xử lý...');

      $.ajax({
        url: '<?= base_url("/discount/check-code") ?>',
        method: 'POST',
        data: {
          code: couponCode
        },
        success: function(response) {
          const data = JSON.parse(response);

          console.log(data); // Log the response for details

          if (data.status) {
            const discountPercent = data.discount.percent;

            // Lấy tạm tính hiện tại (từ items đã chọn)
            const subtotalText = $('#subtotal').text().replace(/[^\d]/g, '');
            const subtotal = parseInt(subtotalText) || 0;

            $('#discount-amount').text(`${discountPercent}%`);

            // Tính lại tổng với giảm giá mới
            recalculateTotal(subtotal);

            swAlert({
              icon: 'success',
              title: 'Thành công!',
              text: `Áp dụng mã giảm giá ${discountPercent}% thành công!`,
              timer: 2000,
              showConfirmButton: false,
              toast: true,
              position: 'top-end'
            });
          } else {
            swAlert({
              icon: 'error',
              title: 'Lỗi!',
              text: data.message || 'Mã giảm giá không hợp lệ!',
              confirmButtonColor: '#2e0a5a'
            });
          }

          // Reset button
          $('#apply-coupon').prop('disabled', false).html('<i class="fa fa-check me-1"></i>Áp dụng');
        },
        error: function() {
          swAlert({
            icon: 'error',
            title: 'Lỗi!',
            text: 'Có lỗi xảy ra khi kiểm tra mã giảm giá!',
            confirmButtonColor: '#2e0a5a'
          });

          // Reset button
          $('#apply-coupon').prop('disabled', false).html('<i class="fa fa-check me-1"></i>Áp dụng');
        }
      });
    });

    // Format giá tiền theo định dạng Việt Nam
    function formatPrice(price) {
      return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
      }).format(price);
    }

    // Hiển thị trạng thái loading cho row
    function showLoadingState(row) {
      row.find('.quantity-input').prop('disabled', true);
      row.find('.btn-plus, .btn-minus').prop('disabled', true);
      row.find('.total-price').html('<i class="fa fa-spinner fa-spin"></i>');
      row.addClass('loading');
    }

    // Ẩn trạng thái loading cho row
    function hideLoadingState(row) {
      row.find('.quantity-input').prop('disabled', false);
      row.find('.btn-plus, .btn-minus').prop('disabled', false);
      row.removeClass('loading');
    }

    // Xử lý nút xác nhận
    $('#btnXacNhan').click(function() {
      if (!isLoggedIn) {
        swAlert({
          icon: 'warning',
          title: 'Cảnh báo!',
          text: 'Vui lòng đăng nhập để tiếp tục!',
          confirmButtonColor: '#2e0a5a'
        });

        return;
      }

      const selectedItems = $('.service-check:checked').length;

      if (selectedItems === 0) {
        swAlert({
          icon: 'warning',
          title: 'Cảnh báo!',
          text: 'Vui lòng chọn ít nhất một dịch vụ để tiếp tục!',
          confirmButtonColor: '#2e0a5a'
        });
        return;
      }

      // Lưu thông tin items đã chọn vào session
      const serviceIds = [];
      $('.service-check:checked').each(function() {
        const serviceId = $(this).data('id');

        serviceIds.push(serviceId);
      })

      const discountCode = $('#coupon').val().trim();

      const data = {
        selected_services: serviceIds,
        discount_code: discountCode,
        discount_percent: parseDiscountPercent(),
        subtotal: parseInt($('#subtotal').text().replace(/[^\d]/g, '')),
        total_price: parseInt($('#total-price').text().replace(/[^\d]/g, ''))
      }

      loadAjaxStatus('start');

      $.ajax({
        url: '<?= base_url("/cart/save-booking-info") ?>',
        method: 'POST',
        data: data,
        success: function(response) {
          const data = JSON.parse(response);

          if (data.status) {
            window.location.href = '<?= base_url("/cart/info") ?>';
          } else {
            swAlert({
              icon: 'error',
              title: 'Lỗi!',
              text: data.message || 'Có lỗi xảy ra khi lưu thông tin!',
              confirmButtonColor: '#2e0a5a'
            });
          }
        },
        complete: function() {
          loadAjaxStatus('end');
        }
      })
    });

    // Xử lý xóa sản phẩm với confirm
    $(document).on('click', 'a[href*="/cart/remove/"]', function(e) {
      e.preventDefault();
      const url = $(this).attr('href');

      swAlert({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc chắn muốn xóa dịch vụ này khỏi giỏ hàng?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2e0a5a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = url;
        }
      });
    });

    // Auto-save quantity khi người dùng ngừng nhập (debounce)
    let quantityTimeout;
    $(document).on('input', '.quantity-input', function() {
      const inputElement = $(this);

      clearTimeout(quantityTimeout);
      quantityTimeout = setTimeout(() => {
        const newQuantity = parseInt(inputElement.val());
        if (newQuantity >= 1 && newQuantity <= 99 && !isNaN(newQuantity)) {
          updateQuantity(inputElement, newQuantity);
        }
      }, 1000); // Đợi 1 giây sau khi ngừng nhập
    });

    // KHỞI TẠO: Đồng bộ tất cả data-quantity với input values
    function initializeQuantitySync() {
      $('.service-check').each(function() {
        const row = $(this).closest('tr');
        const quantityInput = row.find('.quantity-input');
        const currentQuantity = parseInt(quantityInput.val());

        $(this).attr('data-quantity', currentQuantity);
        $(this).data('quantity', currentQuantity);
      });

      // Cập nhật hóa đơn ban đầu
      updateSelectedItems();
    }

    // Khởi tạo khi DOM ready
    initializeQuantitySync();
  });
</script>
<?php end_section(); ?>