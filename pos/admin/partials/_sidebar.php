<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$companyResult = $mysqli->query("SELECT command_center_image FROM company_info LIMIT 1");
$companyInfo = $companyResult ? $companyResult->fetch_assoc() : null;
$commandCenterImage = $companyInfo['command_center_image'] ?? '';
$adminName = 'Administrator';

if (!empty($_SESSION['admin_id'])) {
  $adminStmt = $mysqli->prepare("SELECT admin_name FROM rpos_admin WHERE admin_id = ? LIMIT 1");
  $adminStmt->bind_param('s', $_SESSION['admin_id']);
  $adminStmt->execute();
  $adminResult = $adminStmt->get_result();
  $adminRecord = $adminResult ? $adminResult->fetch_assoc() : null;
  $adminName = $adminRecord['admin_name'] ?? $adminName;
}

$navGroups = [
  [
    'label' => 'Overview',
    'icon' => 'overview',
    'links' => [
      ['label' => 'Dashboard', 'href' => 'dashboard.php'],
    ],
  ],
  [
    'label' => 'Inventory',
    'icon' => 'presentation',
    'links' => [
      ['label' => 'Products', 'href' => 'products.php'],
      ['label' => 'Categories', 'href' => 'categories.php'],
      ['label' => 'Employees', 'href' => 'hrm.php'],
    ],
  ],
  [
    'label' => 'Sales',
    'icon' => 'commerce',
    'links' => [
      ['label' => 'Orders', 'href' => 'invo.php'],
      ['label' => 'Payments', 'href' => 'payments.php'],
      ['label' => 'Receipts', 'href' => 'receipts.php'],
    ],
  ],
  [
    'label' => 'Reporting',
    'icon' => 'reports',
    'links' => [
      ['label' => 'Order Reports', 'href' => 'orders_reports.php'],
      ['label' => 'Payment Reports', 'href' => 'payments_reports.php'],
      ['label' => 'Sales Summary', 'href' => 'sales.php'],
    ],
  ],
  [
    'label' => 'System',
    'icon' => 'account',
    'links' => [
      ['label' => 'Profile', 'href' => 'change_profile.php'],
      ['label' => 'Support', 'href' => '#', 'modal' => true],
      ['label' => 'Logout', 'href' => 'logout.php'],
    ],
  ],
];

$groupIsActive = function (array $group) use ($currentPage): bool {
  foreach ($group['links'] as $link) {
    if (($link['href'] ?? '') === $currentPage) {
      return true;
    }
  }

  return false;
};

$renderIcon = function (string $icon): void {
  switch ($icon) {
    case 'commerce':
      echo '<svg viewBox="0 0 24 24" fill="none"><path d="M6 6h15l-1.5 8.5a2 2 0 0 1-2 1.5H9a2 2 0 0 1-2-1.5L5.2 4H3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 20h.01M18 20h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
      break;
    case 'reports':
      echo '<svg viewBox="0 0 24 24" fill="none"><path d="M5 19V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M14 3v5h5M8.5 16h7M8.5 12h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
      break;
    case 'presentation':
      echo '<svg viewBox="0 0 24 24" fill="none"><path d="M4 7.75A2.75 2.75 0 0 1 6.75 5h10.5A2.75 2.75 0 0 1 20 7.75v5.5A2.75 2.75 0 0 1 17.25 16H6.75A2.75 2.75 0 0 1 4 13.25v-5.5Z" stroke="currentColor" stroke-width="1.8"/><path d="M12 16v3M8.5 19h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
      break;
    case 'account':
      echo '<svg viewBox="0 0 24 24" fill="none"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5 19.5a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
      break;
    default:
      echo '<svg viewBox="0 0 24 24" fill="none"><path d="M5 12h5V5H5v7ZM14 19h5v-7h-5v7ZM14 10h5V5h-5v5ZM5 19h5v-5H5v5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>';
  }
};
?>

<aside class="admin-sidebar" id="sidenav-main">
  <div class="admin-brand-card">
    <a href="dashboard.php" class="admin-brand-logo-shell">
      <?php if (!empty($commandCenterImage)) { ?>
        <img src="<?php echo htmlspecialchars($commandCenterImage); ?>" alt="POS System" class="admin-brand-logo">
      <?php } else { ?>
        <span class="admin-brand-logo admin-brand-logo-text">POS</span>
      <?php } ?>
      <span class="admin-brand-copy">
        <span class="admin-brand-name">POS System</span>
        <span class="admin-brand-tagline">Command center</span>
      </span>
    </a>
    <div class="admin-sidebar-user">
      <p class="admin-sidebar-role">Administrator</p>
      <p class="admin-sidebar-name"><?php echo htmlspecialchars($adminName); ?></p>
    </div>
  </div>

  <nav class="admin-sidebar-nav space-y-5 text-sm">
    <?php foreach ($navGroups as $group) {
      $isActiveGroup = $groupIsActive($group);
    ?>
      <details class="admin-nav-group" <?php echo $isActiveGroup ? 'open' : ''; ?>>
        <summary class="admin-nav-group-summary">
          <span class="admin-nav-group-meta">
            <span class="admin-nav-group-symbol" aria-hidden="true">
              <?php $renderIcon($group['icon']); ?>
            </span>
            <span class="admin-nav-group-title"><?php echo htmlspecialchars($group['label']); ?></span>
            <span class="admin-nav-group-count"><?php echo count($group['links']); ?></span>
          </span>
          <svg class="admin-nav-group-icon" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </summary>

        <div class="admin-nav-group-links">
          <?php foreach ($group['links'] as $link) {
            $href = $link['href'];
            $activeClass = $href === $currentPage ? ' admin-nav-link-active' : '';
            $modalAttrs = !empty($link['modal']) ? ' data-toggle="modal" data-target="#myModal" role="button"' : '';
          ?>
            <a class="admin-nav-link<?php echo $activeClass; ?>" href="<?php echo htmlspecialchars($href); ?>"<?php echo $modalAttrs; ?>>
              <?php echo htmlspecialchars($link['label']); ?>
            </a>
          <?php } ?>
        </div>
      </details>
    <?php } ?>
  </nav>
</aside>
