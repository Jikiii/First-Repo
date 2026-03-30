<?php
require_once '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

/* Get all staff */
$staff_sql = "SELECT StaffNo, FName, LName
              FROM Staff
              ORDER BY LName";

$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff_list_dropdown = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

$properties = [];

if (isset($_GET['staff_no']) && !empty($_GET['staff_no'])) {
    $staff_no = $_GET['staff_no'];

    $sql = "SELECT p.*, b.City
            FROM Property p
            LEFT JOIN Branch b ON p.BranchNo = b.BranchNo
            WHERE p.StaffNo = :staff_no
            ORDER BY p.PropertyNo";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->execute();

    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>Properties Managed by Staff</h2>

<form method="GET" class="mb-3">
    <label for="staff_no">Select Staff</label>
    <select name="staff_no" id="staff_no" class="form-select mb-2">
        <option value="">-- Choose Staff --</option>

        <?php foreach ($staff_list_dropdown as $staff):
            $selected = (isset($_GET['staff_no']) &&
                         $_GET['staff_no'] == $staff['StaffNo'])
                         ? 'selected' : '';
        ?>
            <option value="<?php echo $staff['StaffNo']; ?>" <?php echo $selected; ?>>
                <?php echo $staff['FName'] . ' ' . $staff['LName']; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary">
        Show Properties
    </button>
</form>

<?php if (!empty($properties)): ?>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Property No</th>
            <th>Street</th>
            <th>District</th>
            <th>City</th>
            <th>Type</th>
            <th>Rooms</th>
            <th>Monthly Rent</th>
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
                <td><?php echo formatMoney($prop['RentAmount']); ?></td>
                <td><?php echo $prop['Status']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php elseif (isset($_GET['staff_no'])): ?>
    <p>No properties found for this staff member.</p>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary mt-3">
    Back to Properties
</a>