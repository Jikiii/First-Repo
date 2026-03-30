<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle Delete
if (isset($_GET['delete'])) {
    $renter_no = $_GET['delete'];
    $sql = "DELETE FROM Renter WHERE renter_no = :renter_no";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':renter_no', $renter_no);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Renter deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting renter!</div>";
    }
}

$sql = "SELECT r.*, CONCAT(s.first_name, ' ', s.last_name) as seen_by, b.city as branch_city
        FROM Renter r 
        LEFT JOIN Staff s ON r.seen_by_staff_no = s.staff_no
        LEFT JOIN Branch b ON r.branch_no = b.branch_no
        ORDER BY r.renter_no";
$stmt = $db->prepare($sql);
$stmt->execute();
$renters = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Renter Management</h2>
        <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Renter</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Renter No</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Telephone</th>
                        <th>Preferred Type</th>
                        <th>Max Rent</th>
                        <th>Branch</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($renters as $renter): ?>
                    <tr>
                        <td><?php echo $renter['renter_no']; ?></td>
                        <td><?php echo $renter['first_name'] . ' ' . $renter['last_name']; ?></td>
                        <td><?php echo substr($renter['address'], 0, 30) . '...'; ?></td>
                        <td><?php echo $renter['telephone']; ?></td>
                        <td><?php echo $renter['preferred_property_type']; ?></td>
                        <td>£<?php echo number_format($renter['max_monthly_rent'], 2); ?></td>
                        <td><?php echo $renter['branch_city']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewRenter('<?php echo $renter['renter_no']; ?>')">View</button>
                            <button class="btn btn-sm btn-warning" onclick="editRenter('<?php echo $renter['renter_no']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRenter('<?php echo $renter['renter_no']; ?>')">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function viewRenter(renter_no) { window.location.href = `view.php?renter_no=${renter_no}`; }
function editRenter(renter_no) { window.location.href = `edit.php?renter_no=${renter_no}`; }
function deleteRenter(renter_no) {
    if (confirm('Are you sure you want to delete this renter?')) {
        window.location.href = `index.php?delete=${renter_no}`;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>