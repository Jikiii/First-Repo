<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$renter_no = $_GET['renter_no'];
$sql = "SELECT r.*, CONCAT(s.first_name, ' ', s.last_name) as seen_by, 
        b.city as branch_city, b.street as branch_street
        FROM Renter r 
        LEFT JOIN Staff s ON r.seen_by_staff_no = s.staff_no
        LEFT JOIN Branch b ON r.branch_no = b.branch_no
        WHERE r.renter_no = :renter_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':renter_no', $renter_no);
$stmt->execute();
$renter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$renter) {
    echo "<div class='alert alert-danger'>Renter not found!</div>";
    include '../../includes/footer.php';
    exit;
}

// Get active leases for this renter
$lease_sql = "SELECT l.*, p.street, p.city, p.type 
              FROM LeaseAgreement l 
              JOIN Property p ON l.property_no = p.property_no 
              WHERE l.renter_no = :renter_no AND l.end_date >= CURDATE()";
$lease_stmt = $db->prepare($lease_sql);
$lease_stmt->bindParam(':renter_no', $renter_no);
$lease_stmt->execute();
$leases = $lease_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Renter Details: <?php echo $renter['first_name'] . ' ' . $renter['last_name']; ?></h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Personal Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Renter Number</th><td><?php echo $renter['renter_no']; ?></td></tr>
                    <tr><th>Full Name</th><td><?php echo $renter['first_name'] . ' ' . $renter['last_name']; ?></td></tr>
                    <tr><th>Address</th><td><?php echo nl2br($renter['address']); ?></td></tr>
                    <tr><th>Telephone</th><td><?php echo $renter['telephone']; ?></td></tr>
                    <tr><th>Date Registered</th><td><?php echo $renter['date_registered']; ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Property Requirements</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Preferred Type</th><td><?php echo $renter['preferred_property_type'] ?: 'Any'; ?></td></tr>
                    <tr><th>Maximum Monthly Rent</th><td>£<?php echo number_format($renter['max_monthly_rent'], 2); ?></td></tr>
                    <tr><th>Comments</th><td><?php echo nl2br($renter['comments']); ?></td></tr>
                    <tr><th>Seen By</th><td><?php echo $renter['seen_by']; ?></td></tr>
                    <tr><th>Branch</th><td><?php echo $renter['branch_city']; ?><br><?php echo $renter['branch_street']; ?></td></tr>
                </table>
            </div>
        </div>
        
        <?php if (count($leases) > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Active Leases</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Lease No</th><th>Property</th><th>Address</th><th>Monthly Rent</th><th>Start Date</th><th>End Date</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leases as $lease): ?>
                        <tr>
                            <td><?php echo $lease['lease_no']; ?></td>
                            <td><?php echo $lease['type']; ?></td>
                            <td><?php echo $lease['street'] . ', ' . $lease['city']; ?></td>
                            <td>£<?php echo number_format($lease['monthly_rent'], 2); ?></td>
                            <td><?php echo $lease['start_date']; ?></td>
                            <td><?php echo $lease['end_date']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?renter_no=<?php echo $renter['renter_no']; ?>" class="btn btn-warning">Edit</a>
            <a href="../properties/search.php?renter_no=<?php echo $renter['renter_no']; ?>" class="btn btn-primary">Find Properties</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>