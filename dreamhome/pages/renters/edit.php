<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$renter_no = $_GET['renter_no'];

$sql = "SELECT * FROM Renter WHERE RenterNo = :renter_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':renter_no', $renter_no);
$stmt->execute();
$renter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$renter) {
    echo "<div class='alert alert-danger'>Renter not found!</div>";
    include '../../includes/footer.php';
    exit;
}

$branch_sql = "SELECT BranchNo, BranchName FROM Branch ORDER BY BranchName";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $preferred_type = $_POST['preferred_type'];
    $max_budget = $_POST['max_budget'];
    $notes = $_POST['notes'];
    $branch_no = $_POST['branch_no'];
    
    $sql = "UPDATE Renter SET FName=:fname, LName=:lname, Address=:address, Phone=:phone, 
            PreferredType=:preferred_type, MaxBudget=:max_budget, Notes=:notes, BranchNo=:branch_no 
            WHERE RenterNo=:renter_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':preferred_type', $preferred_type);
    $stmt->bindParam(':max_budget', $max_budget);
    $stmt->bindParam(':notes', $notes);
    $stmt->bindParam(':branch_no', $branch_no);
    $stmt->bindParam(':renter_no', $renter_no);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Renter updated successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'view.php?renter_no=$renter_no'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error updating renter!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Edit Renter: <?php echo $renter['FName'] . ' ' . $renter['LName']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Renter Number</label>
                    <input type="text" class="form-control" value="<?php echo $renter['RenterNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" class="form-control" name="fname" value="<?php echo $renter['FName']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" class="form-control" name="lname" value="<?php echo $renter['LName']; ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address *</label>
                    <textarea class="form-control" name="address" rows="2" required><?php echo $renter['Address']; ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone *</label>
                    <input type="text" class="form-control" name="phone" value="<?php echo $renter['Phone']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Preferred Property Type</label>
                    <select class="form-select" name="preferred_type">
                        <option value="">Any</option>
                        <option value="Flat" <?php echo $renter['PreferredType'] == 'Flat' ? 'selected' : ''; ?>>Flat</option>
                        <option value="House" <?php echo $renter['PreferredType'] == 'House' ? 'selected' : ''; ?>>House</option>
                        <option value="Apartment" <?php echo $renter['PreferredType'] == 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                        <option value="Studio" <?php echo $renter['PreferredType'] == 'Studio' ? 'selected' : ''; ?>>Studio</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Maximum Budget (£)</label>
                    <input type="number" class="form-control" name="max_budget" value="<?php echo $renter['MaxBudget']; ?>" step="50">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="3"><?php echo $renter['Notes']; ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['BranchNo']; ?>" <?php echo $renter['BranchNo'] == $branch['BranchNo'] ? 'selected' : ''; ?>>
                            <?php echo $branch['BranchName']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Renter</button>
            <a href="view.php?renter_no=<?php echo $renter['RenterNo']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>