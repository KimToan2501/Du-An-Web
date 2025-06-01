<?php

/**
 * Enhanced Stepper Functions for Route-based Navigation
 */

/**
 * Get current route path
 */
function get_current_route()
{
  return $_SERVER['REQUEST_URI'] ?? '/';
}

/**
 * Check if current route matches the given route
 */
function is_active_route($route)
{
  $current = get_current_route();
  return $current === $route || strpos($current, $route) === 0;
}

/**
 * Enhanced active_link function with step progression logic
 */
function active_link_step($route, $class = 'active')
{
  return is_active_route($route) ? $class : '';
}

/**
 * Get step status based on current route
 */
function get_step_status($step_route)
{
  $current_route = get_current_route();
  $routes = [
    '/cart' => 1,
    '/cart/info' => 2,
    '/cart/staff' => 3,
    '/cart/finished' => 4
  ];

  $current_step = $routes[$current_route] ?? 1;
  $target_step = $routes[$step_route] ?? 1;

  if ($target_step < $current_step) {
    return 'completed';
  } elseif ($target_step == $current_step) {
    return 'active';
  } else {
    return 'inactive';
  }
}

/**
 * Check if step is completed
 */
function is_step_completed($step_route)
{
  return get_step_status($step_route) === 'completed';
}

/**
 * Get step classes based on route
 */
function get_step_classes($step_route)
{
  $status = get_step_status($step_route);
  $classes = ['d-flex', 'align-items-center', 'gap-2', 'step'];

  switch ($status) {
    case 'completed':
      $classes[] = 'completed';
      break;
    case 'active':
      $classes[] = 'active';
      break;
    case 'inactive':
      $classes[] = 'inactive';
      break;
  }

  return implode(' ', $classes);
}


$steps = [
  ['route' => '/cart', 'number' => 1, 'label' => 'Giỏ Hàng'],
  ['route' => '/cart/info', 'number' => 2, 'label' => 'Thông Tin'],
  ['route' => '/cart/staff', 'number' => 3, 'label' => 'Chọn Nhân Viên'],
  ['route' => '/cart/finished', 'number' => 4, 'label' => 'Hoàn Tất']
];

?>

<ul class="d-flex justify-content-center gap-5 text-xs font-semibold text-[#2e0a5a] py-3">
  <?php foreach ($steps as $step): ?>
    <li>
      <a href="<?= is_step_completed($step['route']) ? $step['route'] : '#' ?>" class="<?= get_step_classes($step['route']) ?>">
        <div class="step-circle"><?= $step['number'] ?></div>
        <span><?= $step['label'] ?></span>
      </a>
    </li>
  <?php endforeach; ?>
</ul>