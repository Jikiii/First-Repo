<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$property_no = $_GET['property_no'];

$sql = "SELECT p.*, 
        CONCAT(s.FName, ' ', s.LName) as manager_name, s.Phone as manager_phone,
        b.BranchName as branch_name, b.City as branch_city, b.ContactNo as branch_phone
        FROM Property p 
        LEFT JOIN Staff s ON p.StaffNo = s.StaffNo
        LEFT JOIN Branch b ON p.BranchNo = b.BranchNo
        WHERE p.PropertyNo = :property_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':property_no', $property_no);
$stmt->execute();
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo "<div class='alert alert-danger'>Property not found!</div>";
    include '../../includes/footer.php';
    exit;
}

$lease_sql = "SELECT l.*, CONCAT(r.FName, ' ', r.LName) as renter_name
              FROM Lease l
              LEFT JOIN Renter r ON l.RenterNo = r.RenterNo
              WHERE l.PropertyNo = :property_no AND l.Status = 'Active'
              ORDER BY l.StartDate DESC LIMIT 1";
$lease_stmt = $db->prepare($lease_sql);
$lease_stmt->bindParam(':property_no', $property_no);
$lease_stmt->execute();
$current_lease = $lease_stmt->fetch(PDO::FETCH_ASSOC);

$inspection_sql = "SELECT i.*, CONCAT(s.FName, ' ', s.LName) as inspector_name
                   FROM Inspection i
                   JOIN Staff s ON i.StaffNo = s.StaffNo
                   WHERE i.PropertyNo = :property_no
                   ORDER BY i.InspectDate DESC";
$inspection_stmt = $db->prepare($inspection_sql);
$inspection_stmt->bindParam(':property_no', $property_no);
$inspection_stmt->execute();
$inspections = $inspection_stmt->fetchAll(PDO::FETCH_ASSOC);

$viewing_sql = "SELECT v.*, CONCAT(r.FName, ' ', r.LName) as renter_name
                FROM Viewing v
                JOIN Renter r ON v.RenterNo = r.RenterNo
                WHERE v.PropertyNo = :property_no
                ORDER BY v.ViewDate DESC";
$viewing_stmt = $db->prepare($viewing_sql);
$viewing_stmt->bindParam(':property_no', $property_no);
$viewing_stmt->execute();
$viewings = $viewing_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Property Details: <?php echo $property['PropertyNo']; ?></h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Property Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Property Number</th><td><?php echo $property['PropertyNo']; ?></td></tr>
                    <tr><th>Property Type</th><td><?php echo $property['PropertyType']; ?></td></tr>
                    <tr><th>Address</th><td>
                        <?php echo $property['StreetName']; ?><br>
                        <?php if ($property['District']): echo $property['District'] . '<br>'; endif; ?>
                        <?php echo $property['City']; ?><br>
                        <?php echo $property['PostCode']; ?>
                    </td></tr>
                    <tr><th>Number of Rooms</th><td><?php echo $property['Rooms']; ?></td></tr>
                    <tr><th>Monthly Rent</th><td><?php echo formatMoney($property['RentAmount']); ?></td></tr>
                    <tr><th>Status</th><td>
                        <?php if ($property['Status'] == 'Available'): ?>
                            <span class="badge bg-success">Available</span>
                        <?php elseif ($property['Status'] == 'Rented'): ?>
                            <span class="badge bg-warning">Rented</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Withdrawn</span>
                        <?php endif; ?>
                    </td></tr>
                    <?php if ($property['DateAvailable']): ?>
                    <tr><th>Date Available</th><td><?php echo formatDate($property['DateAvailable']); ?></td></tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <div class="col-md-6">
                <h4>Management Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Managing Staff</th><td><?php echo $property['manager_name'] ?: 'Not Assigned'; ?></td></tr>
                    <tr><th>Staff Contact</th><td><?php echo $property['manager_phone'] ?: 'N/A'; ?></td></tr>
                    <tr><th>Branch Office</th><td><?php echo $property['branch_name']; ?></td></tr>
                    <tr><th>Branch City</th><td><?php echo $property['branch_city']; ?></td></tr>
                    <tr><th>Branch Phone</th><td><?php echo $property['branch_phone']; ?></td></tr>
                </table>
            </div>
        </div>
        
        <?php if ($current_lease): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Current Tenant Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Renter Name</th><td><?php echo $current_lease['renter_name']; ?></td></tr>
                    <tr><th>Lease Number</th><td><?php echo $current_lease['LeaseNo']; ?></td></tr>
                    <tr><th>Lease Period</th><td><?php echo formatDate($current_lease['StartDate']); ?> to <?php echo formatDate($current_lease['EndDate']); ?></td></tr>
                    <tr><th>Monthly Rent</th><td><?php echo formatMoney($current_lease['Rent']); ?></td></tr>
                    <tr><th>Deposit Amount</th><td><?php echo formatMoney($current_lease['DepositAmount']); ?></td></tr>
                    <tr><th>Deposit Paid</th><td><?php echo $current_lease['IsDepositPaid'] ? 'Yes' : 'No'; ?></td></tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (count($inspections) > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Inspection History</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Inspection Date</th><th>Inspector</th><th>Notes</th> </thead>
                    <tbody>
                        <?php foreach ($inspections as $inspection): ?>
                        <tr>
                            <td><?php echo formatDate($inspection['InspectDate']); ?></td>
                            <td><?php echo $inspection['inspector_name']; ?></td>
                            <td><?php echo nl2br($inspection['Notes']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (count($viewings) > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Viewing History</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Viewing Date</th><th>Renter</th><th>Remarks</th> </thead>
                    <tbody>
                        <?php foreach ($viewings as $viewing): ?>
                        <tr>
                            <td><?php echo formatDate($viewing['ViewDate']); ?></td>
                            <td><?php echo $viewing['renter_name']; ?></td>
                            <td><?php echo nl2br($viewing['Remarks']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?property_no=<?php echo $property['PropertyNo']; ?>" class="btn btn-warning">Edit Property</a>
            <?php if ($property['Status'] == 'Available'): ?>
            <a href="../leases/add.php?property_no=<?php echo $property['PropertyNo']; ?>" class="btn btn-success">Create Lease</a>
            <?php endif; ?>
            <a href="../inspections/add.php?property_no=<?php echo $property['PropertyNo']; ?>" class="btn btn-info">Record Inspection</a>
            <a href="../viewings/add.php?property_no=<?php echo $property['PropertyNo']; ?>" class="btn btn-info">Record Viewing</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>