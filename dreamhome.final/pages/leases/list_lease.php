<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Fetch all properties for the dropdown
$property_sql = "SELECT PropertyNo, StreetName, City, PropertyType FROM Property ORDER BY PropertyNo";
$property_stmt = $db->prepare($property_sql);
$property_stmt->execute();
$all_properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected property_no if submitted
$property_no = isset($_GET['property_no']) ? $_GET['property_no'] : null;

$leases = [];
$property = null;

if ($property_no) {
    // Fetch property info
    $property_info_sql = "SELECT PropertyNo, StreetName, City, PropertyType, Rooms, RentAmount 
                          FROM Property 
                          WHERE PropertyNo = :property_no";
    $property_stmt = $db->prepare($property_info_sql);
    $property_stmt->bindParam(':property_no', $property_no);
    $property_stmt->execute();
    $property = $property_stmt->fetch(PDO::FETCH_ASSOC);

    if ($property) {
        // Fetch lease agreements for this property arranged by Manager or Supervisor
        $lease_sql = "SELECT l.LeaseNo, l.Rent, l.DepositAmount, l.IsDepositPaid, l.StartDate, l.EndDate, l.PaymentMethod,
                             CONCAT(r.FName, ' ', r.LName) AS RenterName,
                             CONCAT(s.FName, ' ', s.LName) AS StaffName, s.JobTitle
                      FROM Lease l
                      JOIN Renter r ON l.RenterNo = r.RenterNo
                      LEFT JOIN Staff s ON l.StaffNo = s.StaffNo
                      WHERE l.PropertyNo = :property_no
                        AND s.JobTitle IN ('Manager','Supervisor')";
        $lease_stmt = $db->prepare($lease_sql);
        $lease_stmt->bindParam(':property_no', $property_no);
        $lease_stmt->execute();
        $leases = $lease_stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $property_no = null; // invalid property
    }
}
?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="">
            <label for="property_no" class="form-label">Select Property</label>
            <select class="form-select" name="property_no" id="property_no" onchange="this.form.submit()">
                <option value="">-- Choose Property --</option>
                <?php foreach ($all_properties as $prop): ?>
                    <option value="<?php echo $prop['PropertyNo']; ?>" <?php echo ($prop['PropertyNo'] == $property_no) ? 'selected' : ''; ?>>
                        <?php echo $prop['PropertyNo'] . ' - ' . $prop['PropertyType'] . ' - ' . $prop['StreetName'] . ', ' . $prop['City']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<?php if ($property_no && $property): ?>
<div class="card">
    <div class="card-header">
        <h2>Lease Agreements for Property <?php echo $property['PropertyNo']; ?></h2>
        <p><?php echo $property['PropertyType'] . ' - ' . $property['StreetName'] . ', ' . $property['City']; ?></p>
    </div>
    <div class="card-body">
        <?php if (!empty($leases)): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Lease No</th>
                    <th>Renter</th>
                    <th>Monthly Rent (£)</th>
                    <th>Deposit (£)</th>
                    <th>Deposit Paid</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Payment Method</th>
                    <th>Arranged By</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leases as $lease): ?>
                <tr>
                    <td><?php echo $lease['LeaseNo']; ?></td>
                    <td><?php echo $lease['RenterName']; ?></td>
                    <td><?php echo number_format($lease['Rent'], 2); ?></td>
                    <td><?php echo number_format($lease['DepositAmount'], 2); ?></td>
                    <td><?php echo $lease['IsDepositPaid'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($lease['StartDate'])); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($lease['EndDate'])); ?></td>
                    <td><?php echo $lease['PaymentMethod']; ?></td>
                    <td><?php echo $lease['StaffName'] . ' (' . $lease['JobTitle'] . ')'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert alert-info">No lease agreements found for this property under a Manager or Supervisor.</div>
        <?php endif; ?>
        <a href="index.php" class="btn btn-secondary mt-3">Back to Properties</a>
    </div>
</div>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>