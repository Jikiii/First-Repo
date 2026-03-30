<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$lease_no = $_GET['lease_no'];

$sql = "SELECT * FROM Lease WHERE LeaseNo = :lease_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':lease_no', $lease_no);
$stmt->execute();
$lease = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lease) {
    echo "<div class='alert alert-danger'>Lease agreement not found!</div>";
    include '../../includes/footer.php';
    exit;
}

$staff_sql = "SELECT StaffNo, CONCAT(FName, ' ', LName) as name FROM Staff ORDER BY FName";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rent = $_POST['rent'];
    $payment_method = $_POST['payment_method'];
    $deposit_amount = $_POST['deposit_amount'];
    $deposit_paid = isset($_POST['deposit_paid']) ? 1 : 0;
    $end_date = $_POST['end_date'];
    $staff_no = $_POST['staff_no'];
    
    $sql = "UPDATE Lease SET Rent=:rent, PaymentMethod=:payment_method, 
            DepositAmount=:deposit_amount, IsDepositPaid=:deposit_paid, EndDate=:end_date, 
            StaffNo=:staff_no WHERE LeaseNo=:lease_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':rent', $rent);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':deposit_amount', $deposit_amount);
    $stmt->bindParam(':deposit_paid', $deposit_paid);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':lease_no', $lease_no);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Lease updated successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'view.php?lease_no=$lease_no'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error updating lease!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Edit Lease Agreement: <?php echo $lease['LeaseNo']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lease Number</label>
                    <input type="text" class="form-control" value="<?php echo $lease['LeaseNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property</label>
                    <input type="text" class="form-control" value="<?php echo $lease['PropertyNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Renter</label>
                    <input type="text" class="form-control" value="<?php echo $lease['RenterNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Monthly Rent (£) *</label>
                    <input type="number" class="form-control" name="rent" value="<?php echo $lease['Rent']; ?>" required step="0.01">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Method *</label>
                    <select class="form-select" name="payment_method" required>
                        <option value="Bank Transfer" <?php echo $lease['PaymentMethod'] == 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="Cheque" <?php echo $lease['PaymentMethod'] == 'Cheque' ? 'selected' : ''; ?>>Cheque</option>
                        <option value="Cash" <?php echo $lease['PaymentMethod'] == 'Cash' ? 'selected' : ''; ?>>Cash</option>
                        <option value="Direct Debit" <?php echo $lease['PaymentMethod'] == 'Direct Debit' ? 'selected' : ''; ?>>Direct Debit</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Deposit Amount (£)</label>
                    <input type="number" class="form-control" name="deposit_amount" value="<?php echo $lease['DepositAmount']; ?>" step="0.01">
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="deposit_paid" id="depositPaid" <?php echo $lease['IsDepositPaid'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="depositPaid">
                            Deposit Paid
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="text" class="form-control" value="<?php echo formatDate($lease['StartDate']); ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date *</label>
                    <input type="date" class="form-control" name="end_date" value="<?php echo $lease['EndDate']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Arranged By (Staff) *</label>
                    <select class="form-select" name="staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?php echo $member['StaffNo']; ?>" <?php echo $lease['StaffNo'] == $member['StaffNo'] ? 'selected' : ''; ?>>
                            <?php echo $member['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Lease</button>
            <a href="view.php?lease_no=<?php echo $lease['LeaseNo']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>