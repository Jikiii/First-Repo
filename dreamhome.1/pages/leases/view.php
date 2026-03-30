<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$lease_no = $_GET['lease_no'];
$sql = "SELECT l.*, p.street, p.area, p.city, p.postcode, p.type as property_type, p.rooms,
        CONCAT(r.first_name, ' ', r.last_name) as renter_name, r.address as renter_address, r.telephone as renter_telephone,
        CONCAT(s.first_name, ' ', s.last_name) as arranged_by
        FROM LeaseAgreement l 
        JOIN Property p ON l.property_no = p.property_no
        JOIN Renter r ON l.renter_no = r.renter_no
        LEFT JOIN Staff s ON l.arranged_by_staff_no = s.staff_no
        WHERE l.lease_no = :lease_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':lease_no', $lease_no);
$stmt->execute();
$lease = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lease) {
    echo "<div class='alert alert-danger'>Lease agreement not found!</div>";
    include '../../includes/footer.php';
    exit;
}

// Get inspections for this property
$inspection_sql = "SELECT i.*, CONCAT(s.first_name, ' ', s.last_name) as inspector
                   FROM PropertyInspection i
                   JOIN Staff s ON i.staff_no = s.staff_no
                   WHERE i.property_no = :property_no
                   ORDER BY i.inspection_date DESC";
$inspection_stmt = $db->prepare($inspection_sql);
$inspection_stmt->bindParam(':property_no', $lease['property_no']);
$inspection_stmt->execute();
$inspections = $inspection_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Lease Agreement Details</h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Lease Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Lease Number</th><td><?php echo $lease['lease_no']; ?></td></tr>
                    <tr><th>Property</th><td><?php echo $lease['property_type'] . ' - ' . $lease['street'] . ', ' . $lease['city']; ?></td></tr>
                    <tr><th>Property Address</th><td><?php echo $lease['street'] . '<br>' . $lease['area'] . '<br>' . $lease['city'] . '<br>' . $lease['postcode']; ?></td></tr>
                    <tr><th>Number of Rooms</th><td><?php echo $lease['rooms']; ?></td></tr>
                    <tr><th>Monthly Rent</th><td>£<?php echo number_format($lease['monthly_rent'], 2); ?></td></tr>
                    <tr><th>Payment Method</th><td><?php echo $lease['payment_method']; ?></td></tr>
                    <tr><th>Deposit Amount</th><td>£<?php echo number_format($lease['deposit_amount'], 2); ?></td></tr>
                    <tr><th>Deposit Paid</th><td><?php echo $lease['deposit_paid'] ? 'Yes' : 'No'; ?></td></tr>
                    <tr><th>Start Date</th><td><?php echo $lease['start_date']; ?></td></tr>
                    <tr><th>End Date</th><td><?php echo $lease['end_date']; ?></td></tr>
                    <tr><th>Duration</th><td><?php echo $lease['duration']; ?> months</td></tr>
                    <tr><th>Arranged By</th><td><?php echo $lease['arranged_by']; ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h4>Renter Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Renter Name</th><td><?php echo $lease['renter_name']; ?></td></tr>
                    <tr><th>Address</th><td><?php echo nl2br($lease['renter_address']); ?></td></tr>
                    <tr><th>Telephone</th><td><?php echo $lease['renter_telephone']; ?></td></tr>
                </table>
            </div>
        </div>
        
        <?php if (count($inspections) > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Property Inspection History</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Date</th><th>Inspector</th><th>Comments</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inspections as $inspection): ?>
                        <tr>
                            <td><?php echo $inspection['inspection_date']; ?></td>
                            <td><?php echo $inspection['inspector']; ?></td>
                            <td><?php echo $inspection['comments']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?lease_no=<?php echo $lease['lease_no']; ?>" class="btn btn-warning">Edit</a>
            <a href="../inspections/add.php?property_no=<?php echo $lease['property_no']; ?>" class="btn btn-primary">Add Inspection</a>
            <button onclick="window.print()" class="btn btn-info">Print Agreement</button>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>