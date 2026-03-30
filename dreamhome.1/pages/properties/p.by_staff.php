<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();
?>

<?php
// Get all staff who manage properties (supervisors or staff)
$staff_sql = "SELECT staff_no, first_name, last_name 
              FROM staff
              ORDER BY last_name";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff_list_dropdown = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
$properties = [];

if (isset($_GET['staff_no']) && !empty($_GET['staff_no'])) {
    $staff_no = $_GET['staff_no'];

    $sql = "SELECT p.*, b.city 
            FROM property p
            LEFT JOIN branch b ON p.branch_no = b.branch_no
            WHERE p.staff_no = :staff_no
            ORDER BY p.property_no";

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
            $selected = (isset($_GET['staff_no']) && $_GET['staff_no'] == $staff['staff_no']) ? 'selected' : '';
        ?>
            <option value="<?php echo $staff['staff_no']; ?>" <?php echo $selected; ?>>
                <?php echo $staff['first_name'] . ' ' . $staff['last_name']; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">Show Properties</button>
</form>

<?php if (!empty($properties)): ?>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Property No</th>
            <th>Street</th>
            <th>Area</th>
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
                <td><?php echo $prop['property_no']; ?></td>
                <td><?php echo $prop['street']; ?></td>
                <td><?php echo $prop['area']; ?></td>
                <td><?php echo $prop['city']; ?></td>
                <td><?php echo $prop['type']; ?></td>
                <td><?php echo $prop['rooms']; ?></td>
                <td>£<?php echo number_format($prop['monthly_rent'], 2); ?></td>
                <td><?php echo ucfirst($prop['status']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php elseif (isset($_GET['staff_no'])): ?>
    <p>No properties found for this staff member.</p>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary mt-3">Back to Properties</a>