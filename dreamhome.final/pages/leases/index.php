<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Delete lease if requested
if (isset($_GET['delete'])) {
    $lease_no = $_GET['delete'];
    $sql = "DELETE FROM Lease WHERE LeaseNo = :lease_no";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':lease_no', $lease_no);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Lease deleted successfully!</div>";
    }
}

// Fetch all leases with property, renter, and staff info
$sql = "SELECT l.*, 
               p.StreetName, p.City, p.PropertyType,
               CONCAT(r.FName, ' ', r.LName) AS renter_name,
               CONCAT(s.FName, ' ', s.LName) AS staff_name
        FROM Lease l
        JOIN Property p ON l.PropertyNo = p.PropertyNo
        JOIN Renter r ON l.RenterNo = r.RenterNo
        LEFT JOIN Staff s ON l.StaffNo = s.StaffNo
        ORDER BY l.StartDate DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$leases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Lease Agreements</h2>
        <div>
            <a href="add.php" class="btn btn-light">+ Add New Lease</a>
            <a href="list_lease.php" class="btn btn-light">List of Leases</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Lease No</th>
                        <th>Property</th>
                        <th>Renter</th>
                        <th>Rent</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>    
                </thead>
                <tbody>
                    <?php foreach ($leases as $lease): 
                        // Calculate status dynamically
                        $status = (strtotime($lease['EndDate']) < time()) ? 'Expired' : 'Active';
                        $status_class = ($status === 'Active') ? 'success' : 'secondary';
                    ?>
                    <tr>
                        <td><?php echo $lease['LeaseNo']; ?></td>
                        <td><?php echo $lease['PropertyType'] . ' - ' . $lease['StreetName'] . ', ' . $lease['City']; ?></td>
                        <td><?php echo $lease['renter_name']; ?></td>
                        <td><?php echo formatMoney($lease['Rent']); ?></td>
                        <td><?php echo formatDate($lease['StartDate']); ?></td>
                        <td><?php echo formatDate($lease['EndDate']); ?></td>
                        <td><span class="badge bg-<?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                        <td>
                            <a href="view.php?lease_no=<?php echo $lease['LeaseNo']; ?>" class="btn btn-sm btn-info">View</a>
                            <a href="edit.php?lease_no=<?php echo $lease['LeaseNo']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="index.php?delete=<?php echo $lease['LeaseNo']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this lease?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($leases) === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center">No leases found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
             </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>