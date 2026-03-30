<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$renter_no = $_GET['renter_no'];

$sql = "SELECT r.*, b.BranchName as branch_name, b.City as branch_city
        FROM Renter r
        LEFT JOIN Branch b ON r.BranchNo = b.BranchNo
        WHERE r.RenterNo = :renter_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':renter_no', $renter_no);
$stmt->execute();
$renter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$renter) {
    echo "<div class='alert alert-danger'>Renter not found!</div>";
    include '../../includes/footer.php';
    exit;
}

$lease_sql = "SELECT l.*, p.StreetName, p.City, p.PropertyType 
              FROM Lease l 
              JOIN Property p ON l.PropertyNo = p.PropertyNo 
              WHERE l.RenterNo = :renter_no 
              ORDER BY l.StartDate DESC";
$lease_stmt = $db->prepare($lease_sql);
$lease_stmt->bindParam(':renter_no', $renter_no);
$lease_stmt->execute();
$leases = $lease_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Renter Details: <?php echo $renter['FName'] . ' ' . $renter['LName']; ?></h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h4>Personal Information</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Renter Number</th><td><?php echo $renter['RenterNo']; ?></tr>
                    <tr><th>Full Name</th><td><?php echo $renter['FName'] . ' ' . $renter['LName']; ?></tr>
                    <tr><th>Address</th><td><?php echo nl2br($renter['Address']); ?></tr>
                    <tr><th>Phone</th><td><?php echo $renter['Phone']; ?></tr>
                 </table>
            </div>
            <div class="col-md-6">
                <h4>Property Requirements</h4>
                <table class="table table-bordered">
                    <tr><th width="200">Preferred Type</th><td><?php echo $renter['PreferredType'] ?: 'Any'; ?></tr>
                    <tr><th>Maximum Budget</th><td><?php echo formatMoney($renter['MaxBudget']); ?></tr>
                    <tr><th>Notes</th><td><?php echo nl2br($renter['Notes']); ?></tr>
                    <tr><th>Branch</th><td><?php echo $renter['branch_name'] . ' - ' . $renter['branch_city']; ?></tr>
                 </table>
            </div>
        </div>
        
        <?php if (count($leases) > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Lease History</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Lease No</th><th>Property</th><th>Address</th><th>Rent</th><th>Start Date</th><th>End Date</th><th>Status</th> </thead>
                    <tbody>
                        <?php foreach ($leases as $lease): 
                            $status = strtotime($lease['EndDate']) < time() ? 'Expired' : 'Active';
                        ?>
                        <tr>
                            <td><?php echo $lease['LeaseNo']; ?></td>
                            <td><?php echo $lease['PropertyType']; ?></td>
                            <td><?php echo $lease['StreetName'] . ', ' . $lease['City']; ?></td>
                            <td><?php echo formatMoney($lease['Rent']); ?></td>
                            <td><?php echo formatDate($lease['StartDate']); ?></td>
                            <td><?php echo formatDate($lease['EndDate']); ?></td>
                            <td><span class="badge bg-<?php echo $status == 'Active' ? 'success' : 'secondary'; ?>"><?php echo $status; ?></span></td>
                         </tr>
                        <?php endforeach; ?>
                    </tbody>
                 </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?renter_no=<?php echo $renter['RenterNo']; ?>" class="btn btn-warning">Edit</a>
            <a href="../properties/search.php" class="btn btn-primary">Find Properties</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>