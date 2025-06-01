$(document).ready(function () {
  // Toggle notification dropdown
  $("#notificationToggle").on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const dropdown = $("#notificationDropdown");
    const profileDropdown = $("#profileDropdown");

    // Close profile dropdown if open
    profileDropdown.removeClass("show").hide();
    $("#profileToggle").removeClass("active");

    // Toggle notification dropdown
    if (dropdown.hasClass("show")) {
      dropdown.removeClass("show").hide();
    } else {
      dropdown.addClass("show").show();
    }
  });

  // Toggle profile dropdown
  $("#profileToggle").on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const dropdown = $("#profileDropdown");
    const notificationDropdown = $("#notificationDropdown");
    const profileToggle = $(this);

    // Close notification dropdown if open
    notificationDropdown.removeClass("show").hide();

    // Toggle profile dropdown
    if (dropdown.hasClass("show")) {
      dropdown.removeClass("show").hide();
      profileToggle.removeClass("active");
    } else {
      dropdown.addClass("show").show();
      profileToggle.addClass("active");
    }
  });

  // Close dropdowns when clicking outside
  $(document).on("click", function (e) {
    if (!$(e.target).closest(".admin-header__notification-wrapper").length) {
      $("#notificationDropdown").removeClass("show").hide();
    }

    if (!$(e.target).closest(".admin-header__profile-wrapper").length) {
      $("#profileDropdown").removeClass("show").hide();
      $("#profileToggle").removeClass("active");
    }
  });

  // Mark notification as read when clicked
  $(".notification-item").on("click", function () {
    $(this).removeClass("unread");
    // Update notification count
    updateNotificationCount();
  });

  // Update notification count
  function updateNotificationCount() {
    const unreadCount = $(".notification-item.unread").length;
    $(".notification-count").text(unreadCount);

    if (unreadCount === 0) {
      $(".admin-header__notification-indicator").hide();
    } else {
      $(".admin-header__notification-indicator").show();
    }
  }

  // Initialize notification count
  updateNotificationCount();

  // Handle logout click
  $(".logout-admin").on("click", function (e) {
    e.preventDefault();

    Swal.fire({
      title: `Bạn có chắc chắn muốn đăng xuất?`,
      text: "Hành động này không thể hoàn tác!",
      icon: "warning",
      dangerMode: true,
      showCloseButton: true,
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Xác nhận",
      cancelButtonText: "Hủy",
    }).then((willDelete) => {
      if (willDelete.isConfirmed) {
        $.ajax({
          url: "/api/logout",
          method: "POST",
          dataType: "json",
          contentType: "application/json",
          success: function (response) {
            swAlert("Thông báo", response.message, "success");

            setTimeout(function () {
              window.location.href = "/login";
            }, 1000);
          },
          error: function (xhr) {
            swAlert("Thông báo", xhr.responseJSON?.message || "Có lỗi xảy ra", "error");
          },
        });
      }
    });
  });
});
