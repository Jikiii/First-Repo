<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$branch_sql = "SELECT BranchNo, BranchName FROM Branch ORDER BY BranchName";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $renter_no = $_POST['renter_no'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $preferred_type = $_POST['preferred_type'];
    $max_budget = $_POST['max_budget'];
    $notes = $_POST['notes'];
    $branch_no = $_POST['branch_no'];
    
    $sql = "INSERT INTO Renter (RenterNo, FName, LName, Address, Phone, PreferredType, MaxBudget, Notes, BranchNo) 
            VALUES (:renter_no, :fname, :lname, :address, :phone, :preferred_type, :max_budget, :notes, :branch_no)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':renter_no', $renter_no);
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':preferred_type', $preferred_type);
    $stmt->bindParam(':max_budget', $max_budget);
    $stmt->bindParam(':notes', $notes);
    $stmt->bindParam(':branch_no', $branch_no);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Renter added successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error adding renter!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Add New Renter</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Renter Number *</label>
                    <input type="text" class="form-control" name="renter_no" required maxlength="10">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" class="form-control" name="fname" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" class="form-control" name="lname" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address *</label>
                    <textarea class="form-control" name="address" rows="2" required></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone *</label>
                    <input type="text" class="form-control" name="phone" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Preferred Property Type</label>
                    <select class="form-select" name="preferred_type">
                        <option value="">Any</option>
                        <option value="Flat">Flat</option>
                        <option value="House">House</option>
                        <option value="Apartment">Apartment</option>
                        <option value="Studio">Studio</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Maximum Budget (£)</label>
                    <input type="number" class="form-control" name="max_budget" step="50">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="3"></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['BranchNo']; ?>"><?php echo $branch['BranchName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Renter</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>