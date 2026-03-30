<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$lease_no = $_GET['lease_no'];

$sql = "SELECT l.*, p.StreetName, p.District, p.City, p.PostCode, p.PropertyType, p.Rooms,
        CONCAT(r.FName, ' ', r.LName) as renter_name, r.Address as renter_address, r.Phone as renter_phone,
        CONCAT(s.FName, ' ', s.LName) as staff_name
        FROM Lease l 
        JOIN Property p ON l.PropertyNo = p.PropertyNo
        JOIN Renter r ON l.RenterNo = r.RenterNo
        LEFT JOIN Staff s ON l.StaffNo = s.StaffNo
        WHERE l.LeaseNo = :lease_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':lease_no', $lease_no);
$stmt->execute();
$lease = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lease) {
    echo "<div class='alert alert-danger'>Lease agreement not found!</div>";
    include '../../includes/footer.php';
    exit;
}

$inspection_sql = "SELECT i.*, CONCAT(s.FName, ' ', s.LName) as inspector
                   FROM Inspection i
                   JOIN Staff s ON i.StaffNo = s.StaffNo
                   WHERE i.PropertyNo = :property_no
                   ORDER BY i.InspectDate DESC";
$inspection_stmt = $db->prepare($inspection_sql);
$inspection_stmt->bindParam(':property_no', $lease['PropertyNo']);
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
                    <tr><th width="200">Lease Number</th><td><?php echo $lease['LeaseNo']; ?></tr>
                    <tr><th>Property</th><td><?php echo $lease['PropertyType'] . ' - ' . $lease['StreetName'] . ', ' . $lease['City']; ?></tr>
                    <tr><th>Property Address</th><td><?php echo $lease['StreetName'] . '<br>' . $lease['District'] . '<br>' . $lease['City'] . '<br>' . $lease['PostCode']; ?></tr>
                    <tr><th>Number of Rooms</th><td><?php echo $lease['Rooms']; ?></tr>
                    <tr><th>Monthly Rent</th><td><?php echo formatMoney($lease['Rent']); ?></tr>
                    <tr><th>Payment Method</th><td><?php echo $lease['PaymentMethod']; ?></tr>
                    <tr><th>Deposit Amount</th><td><?php echo formatMoney($lease['DepositAmount']); ?></tr>
                    <tr><th>Deposit Paid</th><td><?php echo $lease['IsDepositPaid'] ? 'Yes' : 'No'; ?></tr>
                    <tr><th>Start Date</th><td><?php echo formatDate($lease['StartDate']); ?></tr>
                    <tr><th>End Date</th><td><?php echo formatDate($lease['EndDate']); ?></tr>
                    <tr><th>Duration</th><td><?php echo $lease['LeaseDuration']; ?> months</tr>
                    <tr><th>Arranged By</th><td><?php echo $lease['staff_name']; ?></tr>
                 </table>
            </div>
            <div class="col-md-6">
                <h4>Renter Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Renter Name</th><td><?php echo $lease['renter_name']; ?></tr>
                    <tr><th>Address</th><td><?php echo nl2br($lease['renter_address']); ?></tr>
                    <tr><th>Phone</th><td><?php echo $lease['renter_phone']; ?></tr>
                 </table>
            </div>
        </div>
        
        <?php if (count($inspections) > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Property Inspection History</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Date</th><th>Inspector</th><th>Notes</th> </thead>
                    <tbody>
                        <?php foreach ($inspections as $inspection): ?>
                        <tr>
                            <td><?php echo formatDate($inspection['InspectDate']); ?></td>
                            <td><?php echo $inspection['inspector']; ?></td>
                            <td><?php echo $inspection['Notes']; ?></td>
                         </tr>
                        <?php endforeach; ?>
                    </tbody>
                 </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?lease_no=<?php echo $lease['LeaseNo']; ?>" class="btn btn-warning">Edit</a>
            <a href="../inspections/add.php?property_no=<?php echo $lease['PropertyNo']; ?>" class="btn btn-info">Add Inspection</a>
            <button onclick="window.print()" class="btn btn-primary">Print Agreement</button>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>