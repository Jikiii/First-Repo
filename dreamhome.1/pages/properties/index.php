<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();


// Handle Withdraw
if (isset($_GET['withdraw'])) {
    $property_no = $_GET['withdraw'];

    $sql = "UPDATE Property 
            SET status = 'withdrawn',
                date_withdrawn = CURDATE()
            WHERE property_no = :property_no";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':property_no', $property_no);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Property withdrawn successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error withdrawing property!</div>";
    }
}


// Get all properties
$sql = "SELECT p.*, CONCAT(s.first_name, ' ', s.last_name) as manager, b.city 
        FROM Property p 
        LEFT JOIN Staff s ON p.staff_no = s.staff_no 
        LEFT JOIN Branch b ON p.branch_no = b.branch_no 
        ORDER BY CAST(SUBSTRING(p.property_no, 3) AS UNSIGNED), p.property_no";
$stmt = $db->prepare($sql);
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Property Management</h2>
        <div>
        <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Property</button>
        <button class="btn btn-light" onclick="location.href='p.by_branch.php'"> Property by branch</button>
        <button class="btn btn-light" onclick="location.href='p.by_staff.php'"> Property by staff</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="propertiesTable">
                <thead>
                    <tr>
                        <th>Property No</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Type</th>
                        <th>Rooms</th>
                        <th>Monthly Rent</th>
                        <th>Manager</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property): ?>
                    <tr>
                        <td><?php echo $property['property_no']; ?></td>
                        <td><?php echo $property['street']; ?></td>
                        <td><?php echo $property['city']; ?></td>
                        <td><?php echo $property['type']; ?></td>
                        <td><?php echo $property['rooms']; ?></td>
                        <td>£<?php echo number_format($property['monthly_rent'], 2); ?></td>
                        <td><?php echo $property['manager']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $property['status'] == 'available' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($property['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewProperty('<?php echo $property['property_no']; ?>')">View</button>
                            <button class="btn btn-sm btn-warning" onclick="editProperty('<?php echo $property['property_no']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger"onclick="withdrawProperty('<?php echo $property['property_no']; ?>')">Withdraw</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function viewProperty(property_no) {
    window.location.href = `view.php?property_no=${property_no}`;
}

function editProperty(property_no) {
    window.location.href = `edit.php?property_no=${property_no}`;
}

function withdrawProperty(property_no) {
    if (confirm('Are you sure you want to withdraw this property?')) {
        window.location.href = `index.php?withdraw=${property_no}`;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>