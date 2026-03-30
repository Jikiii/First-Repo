<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get available properties for dropdown
$property_sql = "SELECT property_no, street, city, type, monthly_rent FROM Property WHERE status = 'available'";
$property_stmt = $db->prepare($property_sql);
$property_stmt->execute();
$properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get renters for dropdown
$renter_sql = "SELECT renter_no, CONCAT(first_name, ' ', last_name) as name FROM Renter ORDER BY first_name";
$renter_stmt = $db->prepare($renter_sql);
$renter_stmt->execute();
$renters = $renter_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get staff for dropdown
$staff_sql = "SELECT staff_no, CONCAT(first_name, ' ', last_name) as name FROM Staff ORDER BY first_name";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lease_no = $_POST['lease_no'];
    $property_no = $_POST['property_no'];
    $renter_no = $_POST['renter_no'];
    $arranged_by_staff_no = $_POST['arranged_by_staff_no'];
    $monthly_rent = $_POST['monthly_rent'];
    $payment_method = $_POST['payment_method'];
    $deposit_amount = $_POST['deposit_amount'];
    $deposit_paid = isset($_POST['deposit_paid']) ? 1 : 0;
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Calculate duration in months
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $duration = $start->diff($end)->m + ($start->diff($end)->y * 12);
    
    $sql = "INSERT INTO LeaseAgreement (lease_no, property_no, renter_no, arranged_by_staff_no, 
            monthly_rent, payment_method, deposit_amount, deposit_paid, start_date, end_date, duration) 
            VALUES (:lease_no, :property_no, :renter_no, :arranged_by_staff_no, 
            :monthly_rent, :payment_method, :deposit_amount, :deposit_paid, :start_date, :end_date, :duration)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':lease_no', $lease_no);
    $stmt->bindParam(':property_no', $property_no);
    $stmt->bindParam(':renter_no', $renter_no);
    $stmt->bindParam(':arranged_by_staff_no', $arranged_by_staff_no);
    $stmt->bindParam(':monthly_rent', $monthly_rent);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':deposit_amount', $deposit_amount);
    $stmt->bindParam(':deposit_paid', $deposit_paid);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':duration', $duration);
    
    if ($stmt->execute()) {
        // Update property status to rented
        $update_property = "UPDATE Property SET status = 'rented' WHERE property_no = :property_no";
        $prop_stmt = $db->prepare($update_property);
        $prop_stmt->bindParam(':property_no', $property_no);
        $prop_stmt->execute();
        
        echo "<div class='alert alert-success'>Lease agreement created successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error creating lease agreement!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Create New Lease Agreement</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="" id="leaseForm">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Lease Number *</label>
                    <input type="text" class="form-control" name="lease_no" required maxlength="5">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Property *</label>
                    <select class="form-select" name="property_no" required id="propertySelect">
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $property): ?>
                        <option value="<?php echo $property['property_no']; ?>" 
                                data-rent="<?php echo $property['monthly_rent']; ?>">
                            <?php echo $property['type'] . ' - ' . $property['street'] . ', ' . $property['city'] . ' (£' . $property['monthly_rent'] . ')'; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Renter *</label>
                    <select class="form-select" name="renter_no" required>
                        <option value="">Select Renter</option>
                        <?php foreach ($renters as $renter): ?>
                        <option value="<?php echo $renter['renter_no']; ?>"><?php echo $renter['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Arranged By (Staff) *</label>
                    <select class="form-select" name="arranged_by_staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?php echo $member['staff_no']; ?>"><?php echo $member['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Monthly Rent (£) *</label>
                    <input type="number" class="form-control" name="monthly_rent" required step="0.01" id="monthlyRent">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Method *</label>
                    <select class="form-select" name="payment_method" required>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Cash">Cash</option>
                        <option value="Credit Card">Credit Card</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Deposit Amount (£)</label>
                    <input type="number" class="form-control" name="deposit_amount" step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="deposit_paid" id="depositPaid">
                        <label class="form-check-label" for="depositPaid">
                            Deposit Paid
                        </label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Start Date *</label>
                    <input type="date" class="form-control" name="start_date" required id="startDate">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">End Date *</label>
                    <input type="date" class="form-control" name="end_date" required id="endDate">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Duration</label>
                    <input type="text" class="form-control" id="duration" readonly>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Create Lease</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
document.getElementById('propertySelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const rent = selected.getAttribute('data-rent');
    if (rent) {
        document.getElementById('monthlyRent').value = rent;
    }
});

function calculateDuration() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const diffMonths = Math.floor(diffDays / 30);
        
        document.getElementById('duration').value = diffMonths + ' months (' + diffDays + ' days)';
        
        if (diffMonths < 3) {
            alert('Warning: Minimum lease duration is 3 months!');
        } else if (diffMonths > 12) {
            alert('Warning: Maximum lease duration is 12 months!');
        }
    }
}

document.getElementById('startDate').addEventListener('change', calculateDuration);
document.getElementById('endDate').addEventListener('change', calculateDuration);
</script>

<?php include '../../includes/footer.php'; ?>