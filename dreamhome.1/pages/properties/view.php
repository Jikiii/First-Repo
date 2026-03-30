<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$property_no = $_GET['property_no'];

// Get property details with staff and branch info
$sql = "SELECT p.*, 
        CONCAT(s.first_name, ' ', s.last_name) as manager_name,
        s.telephone as manager_phone,
        b.city as branch_city, 
        b.street as branch_street,
        b.telephone as branch_phone
        FROM Property p 
        LEFT JOIN Staff s ON p.staff_no = s.staff_no
        LEFT JOIN Branch b ON p.branch_no = b.branch_no
        WHERE p.property_no = :property_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':property_no', $property_no);
$stmt->execute();
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo "<div class='alert alert-danger'>Property not found!</div>";
    include '../../includes/footer.php';
    exit;
}

// Get current lease if property is rented
$lease_sql = "SELECT l.*, 
              CONCAT(r.first_name, ' ', r.last_name) as renter_name,
              r.telephone as renter_phone,
              r.address as renter_address
              FROM LeaseAgreement l 
              JOIN Renter r ON l.renter_no = r.renter_no
              WHERE l.property_no = :property_no 
              AND l.end_date >= CURDATE()
              ORDER BY l.start_date DESC LIMIT 1";
$lease_stmt = $db->prepare($lease_sql);
$lease_stmt->bindParam(':property_no', $property_no);
$lease_stmt->execute();
$current_lease = $lease_stmt->fetch(PDO::FETCH_ASSOC);

// Get inspection history
$inspection_sql = "SELECT i.*, 
                   CONCAT(s.first_name, ' ', s.last_name) as inspector_name
                   FROM PropertyInspection i
                   JOIN Staff s ON i.staff_no = s.staff_no
                   WHERE i.property_no = :property_no
                   ORDER BY i.inspection_date DESC";
$inspection_stmt = $db->prepare($inspection_sql);
$inspection_stmt->bindParam(':property_no', $property_no);
$inspection_stmt->execute();
$inspections = $inspection_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get lease history
$history_sql = "SELECT l.*, 
                CONCAT(r.first_name, ' ', r.last_name) as renter_name
                FROM LeaseAgreement l
                JOIN Renter r ON l.renter_no = r.renter_no
                WHERE l.property_no = :property_no
                ORDER BY l.start_date DESC";
$history_stmt = $db->prepare($history_sql);
$history_stmt->bindParam(':property_no', $property_no);
$history_stmt->execute();
$lease_history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Property Details: <?php echo $property['property_no']; ?></h2>
    </div>
    <div class="card-body">
        
        <!-- Property Information -->
        <div class="row">
            <div class="col-md-6">
                <h4>Property Information</h4>
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Property Number</th>
                        <td><?php echo $property['property_no']; ?></td>
                    </tr>
                    <tr>
                        <th>Property Type</th>
                        <td><?php echo $property['type']; ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>
                            <?php echo $property['street']; ?><br>
                            <?php if ($property['area']): echo $property['area'] . '<br>'; endif; ?>
                            <?php echo $property['city']; ?><br>
                            <?php echo $property['postcode']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Number of Rooms</th>
                        <td><?php echo $property['rooms']; ?></td>
                    </tr>
                    <tr>
                        <th>Monthly Rent</th>
                        <td class="fw-bold text-success">£<?php echo number_format($property['monthly_rent'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php if ($property['status'] == 'available'): ?>
                                <span class="badge bg-success">Available</span>
                            <?php elseif ($property['status'] == 'rented'): ?>
                                <span class="badge bg-warning">Rented</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Withdrawn</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ($property['date_withdrawn']): ?>
                    <tr>
                        <th>Date Withdrawn</th>
                        <td><?php echo $property['date_withdrawn']; ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <div class="col-md-6">
                <h4>Management Information</h4>
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Managing Staff</th>
                        <td><?php echo $property['manager_name'] ?: 'Not Assigned'; ?></td>
                    </tr>
                    <tr>
                        <th>Staff Contact</th>
                        <td><?php echo $property['manager_phone'] ?: 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Branch Office</th>
                        <td><?php echo $property['branch_city']; ?></td>
                    </tr>
                    <tr>
                        <th>Branch Address</th>
                        <td>
                            <?php echo $property['branch_street']; ?><br>
                            <?php echo $property['branch_city']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Branch Phone</th>
                        <td><?php echo $property['branch_phone']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Current Lease Information -->
        <?php if ($current_lease): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Current Tenant Information</h4>
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Renter Name</th>
                        <td><?php echo $current_lease['renter_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Contact Number</th>
                        <td><?php echo $current_lease['renter_phone']; ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?php echo nl2br($current_lease['renter_address']); ?></td>
                    </tr>
                    <tr>
                        <th>Lease Number</th>
                        <td><?php echo $current_lease['lease_no']; ?></td>
                    </tr>
                    <tr>
                        <th>Lease Period</th>
                        <td><?php echo $current_lease['start_date']; ?> to <?php echo $current_lease['end_date']; ?></td>
                    </tr>
                    <tr>
                        <th>Monthly Rent</th>
                        <td>£<?php echo number_format($current_lease['monthly_rent'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Deposit Amount</th>
                        <td>£<?php echo number_format($current_lease['deposit_amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Deposit Paid</th>
                        <td><?php echo $current_lease['deposit_paid'] ? 'Yes' : 'No'; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Inspection History -->
        <?php if (count($inspections) > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Inspection History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Inspection Date</th>
                                <th>Inspector</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inspections as $inspection): ?>
                            <tr>
                                <td><?php echo $inspection['inspection_date']; ?></td>
                                <td><?php echo $inspection['inspector_name']; ?></td>
                                <td><?php echo nl2br($inspection['comments']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Lease History -->
        <?php if (count($lease_history) > 0): ?>
        <div class="row mt-4">
            <div class="col-12">
                <h4>Lease History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Lease No</th>
                                <th>Renter</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Monthly Rent</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lease_history as $history): 
                                $status = strtotime($history['end_date']) < time() ? 'Expired' : 'Active';
                            ?>
                            <tr>
                                <td><?php echo $history['lease_no']; ?></td>
                                <td><?php echo $history['renter_name']; ?></td>
                                <td><?php echo $history['start_date']; ?></td>
                                <td><?php echo $history['end_date']; ?></td>
                                <td>£<?php echo number_format($history['monthly_rent'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $status == 'Active' ? 'success' : 'secondary'; ?>">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">Back to List</a>
            <a href="edit.php?property_no=<?php echo $property['property_no']; ?>" class="btn btn-warning">Edit Property</a>
            <?php if ($property['status'] == 'available'): ?>
            <a href="../leases/add.php?property_no=<?php echo $property['property_no']; ?>" class="btn btn-success">Create Lease</a>
            <?php endif; ?>
            <a href="../inspections/add.php?property_no=<?php echo $property['property_no']; ?>" class="btn btn-info">Record Inspection</a>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>