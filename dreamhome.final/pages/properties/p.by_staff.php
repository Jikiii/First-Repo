<?php
require_once '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Fetch all staff
$staff_sql = "SELECT StaffNo, FName, LName FROM Staff ORDER BY LName";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff_list = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

$properties = [];
$selected_staff = null;

if (isset($_GET['staff_no']) && !empty($_GET['staff_no'])) {
    $selected_staff = $_GET['staff_no'];

    // Fetch properties for selected staff
    $sql = "SELECT p.*, b.City
            FROM Property p
            LEFT JOIN Branch b ON p.BranchNo = b.BranchNo
            WHERE p.StaffNo = :staff_no
            ORDER BY p.PropertyNo";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':staff_no', $selected_staff);
    $stmt->execute();
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="card mb-3">
    <div class="card-body">
        <label class="form-label">Select Staff</label>
       <div class="dropdown">
    <button class="btn btn-light dropdown-toggle w-100 border" type="button" id="staffDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <?php
        if ($selected_staff) {
            foreach ($staff_list as $s) {
                if ($s['StaffNo'] == $selected_staff) {
                    echo $s['FName'] . ' ' . $s['LName'];
                    break;
                }
            }
        } else {
            echo "-- Choose Staff --";
        }
        ?>
    </button>
    <ul class="dropdown-menu w-100" aria-labelledby="staffDropdown" style="max-height: 300px; overflow-y: auto;">
        <?php foreach ($staff_list as $staff): ?>
            <li>
                <a class="dropdown-item" href="?staff_no=<?php echo $staff['StaffNo']; ?>">
                    <?php echo $staff['FName'] . ' ' . $staff['LName']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
    </div>
</div>

<?php if ($selected_staff && !empty($properties)): ?>
<div class="card">
    <div class="card-header">
        <h2>Properties Managed by <?php
            foreach ($staff_list as $s) {
                if ($s['StaffNo'] == $selected_staff) {
                    echo $s['FName'] . ' ' . $s['LName'];
                    break;
                }
            }
        ?></h2>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Property No</th>
                    <th>Street</th>
                    <th>District</th>
                    <th>City</th>
                    <th>Type</th>
                    <th>Rooms</th>
                    <th>Monthly Rent (£)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $prop): ?>
                <tr>
                    <td><?php echo $prop['PropertyNo']; ?></td>
                    <td><?php echo $prop['StreetName']; ?></td>
                    <td><?php echo $prop['District']; ?></td>
                    <td><?php echo $prop['City']; ?></td>
                    <td><?php echo $prop['PropertyType']; ?></td>
                    <td><?php echo $prop['Rooms']; ?></td>
                    <td><?php echo number_format($prop['RentAmount'], 2); ?></td>
                    <td>
                        <span class="badge <?php echo $prop['Status']=='Available' ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo $prop['Status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif ($selected_staff): ?>
    <div class="alert alert-info">No properties found for this staff member.</div>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary mt-3">Back to Properties</a>

<?php include '../../includes/footer.php'; ?>