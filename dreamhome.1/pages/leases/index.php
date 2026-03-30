<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle Delete
if (isset($_GET['delete'])) {
    $lease_no = $_GET['delete'];
    $sql = "DELETE FROM LeaseAgreement WHERE lease_no = :lease_no";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':lease_no', $lease_no);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Lease deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting lease!</div>";
    }
}

$sql = "SELECT l.*, p.street, p.city, p.type as property_type,
        CONCAT(r.first_name, ' ', r.last_name) as renter_name,
        CONCAT(s.first_name, ' ', s.last_name) as arranged_by
        FROM LeaseAgreement l 
        JOIN Property p ON l.property_no = p.property_no
        JOIN Renter r ON l.renter_no = r.renter_no
        LEFT JOIN Staff s ON l.arranged_by_staff_no = s.staff_no
        ORDER BY l.start_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$leases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Lease Agreements</h2>
        <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Lease</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Lease No</th>
                        <th>Property</th>
                        <th>Renter</th>
                        <th>Monthly Rent</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leases as $lease): 
                        $status = strtotime($lease['end_date']) < time() ? 'Expired' : 'Active';
                        $status_class = $status == 'Active' ? 'success' : 'secondary';
                    ?>
                    <tr>
                        <td><?php echo $lease['lease_no']; ?></td>
                        <td><?php echo $lease['property_type'] . ' - ' . $lease['street'] . ', ' . $lease['city']; ?></td>
                        <td><?php echo $lease['renter_name']; ?></td>
                        <td>£<?php echo number_format($lease['monthly_rent'], 2); ?></td>
                        <td><?php echo $lease['start_date']; ?></td>
                        <td><?php echo $lease['end_date']; ?></td>
                        <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewLease('<?php echo $lease['lease_no']; ?>')">View</button>
                            <button class="btn btn-sm btn-warning" onclick="editLease('<?php echo $lease['lease_no']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteLease('<?php echo $lease['lease_no']; ?>')">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function viewLease(lease_no) { window.location.href = `view.php?lease_no=${lease_no}`; }
function editLease(lease_no) { window.location.href = `edit.php?lease_no=${lease_no}`; }
function deleteLease(lease_no) {
    if (confirm('Are you sure you want to delete this lease?')) {
        window.location.href = `index.php?delete=${lease_no}`;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>