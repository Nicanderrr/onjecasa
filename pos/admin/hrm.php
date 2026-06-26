<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

function staff_h($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function staff_mask_pincode($value)
{
  $value = trim((string) $value);
  return $value === '' ? 'N/A' : '****';
}

if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $adn = "DELETE FROM rpos_staff WHERE staff_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  if ($stmt->affected_rows > 0) {
    $success = "Employee Deleted";
    header("refresh:1; url=hrm.php");
  } else {
    $err = "Employee could not be deleted";
  }
  $stmt->close();
}

$employees = [];
$employeeResult = $mysqli->query("SELECT * FROM rpos_staff ORDER BY created_at DESC");
while ($employeeResult && $row = $employeeResult->fetch_object()) {
  $employees[] = $row;
}

$query = trim($_GET['q'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$totalEmployees = count($employees);
$withEmail = 0;
$withoutEmail = 0;
$recentEmployee = $employees[0]->staff_name ?? 'None';

foreach ($employees as $employee) {
  if (trim((string) $employee->staff_email) !== '') {
    $withEmail++;
  } else {
    $withoutEmail++;
  }
}

$filteredEmployees = array_values(array_filter($employees, function ($employee) use ($query, $statusFilter) {
  if ($query !== '') {
    $haystack = strtolower($employee->staff_name . ' ' . $employee->staff_number . ' ' . $employee->staff_email);
    if (strpos($haystack, strtolower($query)) === false) {
      return false;
    }
  }

  $hasEmail = trim((string) $employee->staff_email) !== '';
  if ($statusFilter === 'email' && !$hasEmail) {
    return false;
  }
  if ($statusFilter === 'blank' && $hasEmail) {
    return false;
  }

  return true;
}));

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="container-fluid mt-4 category-workspace">
      <div class="row category-metric-row">
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-primary mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Employees</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($totalEmployees); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                    <i class="fas fa-users"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Active staff records</p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-success mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">With Email</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($withEmail); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-green text-white rounded-circle shadow">
                    <i class="fas fa-envelope"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Contactable employees</p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-warning mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Without Email</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($withoutEmail); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                    <i class="fas fa-user-slash"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Needs profile update</p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-danger mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Latest Employee</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo staff_h($recentEmployee); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                    <i class="fas fa-id-badge"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Most recent record</p>
            </div>
          </div>
        </div>
      </div>

      <div class="product-panel card shadow">
        <div class="card-header border-0 product-panel-head">
          <div>
            <h3>Employee Directory</h3>
            <p>Search staff, filter blanks, and manage each employee from one place.</p>
          </div>
          <a href="add_staff.php" class="btn btn-outline-success">
            <i class="fas fa-user-plus"></i>
            New Employee
          </a>
        </div>

        <form class="product-filter-bar" method="GET">
          <div class="product-search-field">
            <i class="fas fa-search"></i>
            <input type="search" name="q" value="<?php echo staff_h($query); ?>" placeholder="Search name, staff number, email">
          </div>
          <select name="status" class="form-control">
            <option value="">All status</option>
            <option value="email" <?php echo $statusFilter === 'email' ? 'selected' : ''; ?>>With email</option>
            <option value="blank" <?php echo $statusFilter === 'blank' ? 'selected' : ''; ?>>Without email</option>
          </select>
          <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i></button>
          <a class="btn btn-light" href="hrm.php"><i class="fas fa-times"></i></a>
        </form>

        <div class="table-responsive product-table-wrap">
          <table class="table align-items-center table-flush product-inventory-table" id="employeeInventoryTable">
            <thead class="thead-light">
              <tr>
                <th scope="col">Staff No.</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Pin Code</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($filteredEmployees) === 0) { ?>
                <tr>
                  <td colspan="6">
                    <div class="product-empty-state">
                      <strong>No employees found</strong>
                      <span>Try a different search or clear the filters.</span>
                    </div>
                  </td>
                </tr>
              <?php } ?>
              <?php foreach ($filteredEmployees as $staff) {
                $hasEmail = trim((string) $staff->staff_email) !== '';
                $statusClass = $hasEmail ? 'badge-success' : 'badge-warning';
                $statusLabel = $hasEmail ? 'Contact ready' : 'Email missing';
              ?>
                <tr>
                  <td><?php echo staff_h($staff->staff_number); ?></td>
                  <td><?php echo staff_h($staff->staff_name); ?></td>
                  <td><?php echo staff_h($staff->staff_email ?: 'N/A'); ?></td>
                  <td><?php echo staff_h(staff_mask_pincode($staff->staff_pincode)); ?></td>
                  <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                  <td class="text-right">
                    <div class="product-action-group">
                      <a href="update_staff.php?update=<?php echo (int) $staff->staff_id; ?>" class="btn btn-sm btn-primary" title="Edit employee">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="hrm.php?delete=<?php echo (int) $staff->staff_id; ?>" class="btn btn-sm btn-danger" title="Delete employee" onclick="return confirm('Delete <?php echo staff_h($staff->staff_name); ?>? This cannot be undone.');">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
  <script>
    if (window.jQuery && $.fn.DataTable) {
      $('#employeeInventoryTable').DataTable({
        searching: false,
        lengthChange: false,
        pageLength: 10,
        order: [],
        language: {
          paginate: {
            previous: '<i class="fas fa-chevron-left"></i>',
            next: '<i class="fas fa-chevron-right"></i>'
          }
        }
      });
    }
  </script>
</body>

</html>
