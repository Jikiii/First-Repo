<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Fetch dropdown data
$property_sql = "SELECT PropertyNo, StreetName, City, PropertyType, RentAmount 
                 FROM Property WHERE Status = 'Available'";
$property_stmt = $db->prepare($property_sql);
$property_stmt->execute();
$properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);

$renter_sql = "SELECT RenterNo, CONCAT(FName, ' ', LName) as name FROM Renter ORDER BY FName";
$renter_stmt = $db->prepare($renter_sql);
$renter_stmt->execute();
$renters = $renter_stmt->fetchAll(PDO::FETCH_ASSOC);

$staff_sql = "SELECT StaffNo, CONCAT(FName, ' ', LName) as name FROM Staff ORDER BY FName";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_no = $_POST['property_no'];
    $renter_no = $_POST['renter_no'];
    $staff_no = $_POST['staff_no'];
    $rent = $_POST['rent'];
    $payment_method = $_POST['payment_method'];
    $deposit_amount = $_POST['deposit_amount'];
    $deposit_paid = isset($_POST['deposit_paid']) ? 1 : 0;
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validate dates
    if ($end_date <= $start_date) {
        echo "<div class='alert alert-danger'>End date must be after start date!</div>";
    } else {
        // Check property availability
        $check = $db->prepare("SELECT Status FROM Property WHERE PropertyNo = :property_no");
        $check->bindParam(':property_no', $property_no);
        $check->execute();
        $status = $check->fetchColumn();

        if ($status !== 'Available') {
            echo "<div class='alert alert-danger'>Property is not available!</div>";
        } else {
            // Calculate duration in months
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $interval = $start->diff($end);
            $duration = ($interval->y * 12) + $interval->m;

            // Generate LeaseNo automatically
            $lease_no = $db->query("SELECT CONCAT('L', LPAD(IFNULL(MAX(CAST(SUBSTRING(LeaseNo,2) AS UNSIGNED)),0)+1,3,'0')) FROM Lease")->fetchColumn();

            // Insert lease
            $sql = "INSERT INTO Lease 
                    (LeaseNo, PropertyNo, RenterNo, StaffNo, Rent, PaymentMethod, DepositAmount, IsDepositPaid, StartDate, EndDate, LeaseDuration, Status) 
                    VALUES 
                    (:lease_no, :property_no, :renter_no, :staff_no, :rent, :payment_method, :deposit_amount, :deposit_paid, :start_date, :end_date, :duration, 'Active')";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':lease_no', $lease_no);
            $stmt->bindParam(':property_no', $property_no);
            $stmt->bindParam(':renter_no', $renter_no);
            $stmt->bindParam(':staff_no', $staff_no);
            $stmt->bindParam(':rent', $rent);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':deposit_amount', $deposit_amount);
            $stmt->bindParam(':deposit_paid', $deposit_paid);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':duration', $duration);

            try {
                $stmt->execute();

                // Update property status
                $update_property = "UPDATE Property SET Status = 'Rented' WHERE PropertyNo = :property_no";
                $prop_stmt = $db->prepare($update_property);
                $prop_stmt->bindParam(':property_no', $property_no);
                $prop_stmt->execute();

                echo "<div class='alert alert-success'>Lease agreement created successfully! (LeaseNo: $lease_no)</div>";
                echo "<script>
                        document.querySelector('form').reset();
                        document.getElementById('duration').value = '';
                      </script>";

            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Error creating lease: " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Create New Lease Agreement</h2>
    </div>

    <div class="card-body">
        <form method="POST">

            <div class="row">

                <!-- Property -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Property *</label>
                    <select class="form-select" name="property_no" id="propertySelect" required>
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $property): ?>
                        <option value="<?= $property['PropertyNo']; ?>" data-rent="<?= $property['RentAmount']; ?>">
                            <?= $property['PropertyNo'] . ' - ' . $property['PropertyType'] . ' - ' . $property['StreetName'] . ', ' . $property['City']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Renter -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Renter *</label>
                    <select class="form-select" name="renter_no" required>
                        <option value="">Select Renter</option>
                        <?php foreach ($renters as $renter): ?>
                        <option value="<?= $renter['RenterNo']; ?>"><?= $renter['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Staff -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Staff *</label>
                    <select class="form-select" name="staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?= $member['StaffNo']; ?>"><?= $member['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Rent -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Monthly Rent (£) *</label>
                    <input type="number" class="form-control" name="rent" id="monthlyRent" step="0.01" required>
                </div>

                <!-- Payment -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Method *</label>
                    <select class="form-select" name="payment_method" required>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cash">Cash</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Direct Debit">Direct Debit</option>
                    </select>
                </div>

                <!-- Deposit -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Deposit (£)</label>
                    <input type="number" class="form-control" name="deposit_amount" step="0.01">
                </div>

                <!-- Deposit Paid -->
                <div class="col-md-4 mb-3">
                    <div class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" name="deposit_paid">
                        <label class="form-check-label">Deposit Paid</label>
                    </div>
                </div>

                <!-- Dates -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Start Date *</label>
                    <input type="date" class="form-control" name="start_date" id="startDate" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">End Date *</label>
                    <input type="date" class="form-control" name="end_date" id="endDate" required>
                </div>

                <!-- Duration -->
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
// Auto-fill rent based on selected property
document.getElementById('propertySelect').addEventListener('change', function() {
    const rentField = document.getElementById('monthlyRent');
    const rent = this.selectedOptions[0].getAttribute('data-rent');
    rentField.value = rent;
});

// Calculate duration in months
function calcDuration() {
    const s = new Date(document.getElementById('startDate').value);
    const e = new Date(document.getElementById('endDate').value);
    if (s && e && e > s) {
        const months = (e.getFullYear() - s.getFullYear()) * 12 + (e.getMonth() - s.getMonth());
        document.getElementById('duration').value = months + " months";
    } else {
        document.getElementById('duration').value = '';
    }
}

document.getElementById('startDate').addEventListener('change', calcDuration);
document.getElementById('endDate').addEventListener('change', calcDuration);
</script>

<?php include '../../includes/footer.php'; ?>