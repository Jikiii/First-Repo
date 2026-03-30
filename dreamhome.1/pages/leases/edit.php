<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$lease_no = $_GET['lease_no'];

// Get lease details
$sql = "SELECT * FROM LeaseAgreement WHERE lease_no = :lease_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':lease_no', $lease_no);
$stmt->execute();
$lease = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lease) {
    echo "<div class='alert alert-danger'>Lease agreement not found!</div>";
    include '../../includes/footer.php';
    exit;
}

// Get staff for dropdown
$staff_sql = "SELECT staff_no, CONCAT(first_name, ' ', last_name) as name FROM Staff ORDER BY first_name";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monthly_rent = $_POST['monthly_rent'];
    $payment_method = $_POST['payment_method'];
    $deposit_amount = $_POST['deposit_amount'];
    $deposit_paid = isset($_POST['deposit_paid']) ? 1 : 0;
    $end_date = $_POST['end_date'];
    $arranged_by_staff_no = $_POST['arranged_by_staff_no'];
    
    $sql = "UPDATE LeaseAgreement SET monthly_rent=:monthly_rent, payment_method=:payment_method, 
            deposit_amount=:deposit_amount, deposit_paid=:deposit_paid, end_date=:end_date, 
            arranged_by_staff_no=:arranged_by_staff_no WHERE lease_no=:lease_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':monthly_rent', $monthly_rent);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':deposit_amount', $deposit_amount);
    $stmt->bindParam(':deposit_paid', $deposit_paid);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':arranged_by_staff_no', $arranged_by_staff_no);
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
        <h2 class="mb-0">Edit Lease Agreement: <?php echo $lease['lease_no']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Monthly Rent (£) *</label>
                    <input type="number" class="form-control" name="monthly_rent" value="<?php echo $lease['monthly_rent']; ?>" required step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Method *</label>
                    <select class="form-select" name="payment_method" required>
                        <option value="Bank Transfer" <?php echo $lease['payment_method'] == 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="Cheque" <?php echo $lease['payment_method'] == 'Cheque' ? 'selected' : ''; ?>>Cheque</option>
                        <option value="Cash" <?php echo $lease['payment_method'] == 'Cash' ? 'selected' : ''; ?>>Cash</option>
                        <option value="Credit Card" <?php echo $lease['payment_method'] == 'Credit Card' ? 'selected' : ''; ?>>Credit Card</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Deposit Amount (£)</label>
                    <input type="number" class="form-control" name="deposit_amount" value="<?php echo $lease['deposit_amount']; ?>" step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="deposit_paid" id="depositPaid" <?php echo $lease['deposit_paid'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="depositPaid">
                            Deposit Paid
                        </label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">End Date *</label>
                    <input type="date" class="form-control" name="end_date" value="<?php echo $lease['end_date']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Arranged By (Staff) *</label>
                    <select class="form-select" name="arranged_by_staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?php echo $member['staff_no']; ?>" <?php echo $lease['arranged_by_staff_no'] == $member['staff_no'] ? 'selected' : ''; ?>>
                            <?php echo $member['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Lease</button>
            <a href="view.php?lease_no=<?php echo $lease['lease_no']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>