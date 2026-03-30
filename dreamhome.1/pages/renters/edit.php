<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$renter_no = $_GET['renter_no'];

// Get renter details
$sql = "SELECT * FROM Renter WHERE renter_no = :renter_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':renter_no', $renter_no);
$stmt->execute();
$renter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$renter) {
    echo "<div class='alert alert-danger'>Renter not found!</div>";
    include '../../includes/footer.php';
    exit;
}

// Get staff for dropdown
$staff_sql = "SELECT staff_no, CONCAT(first_name, ' ', last_name) as name FROM Staff ORDER BY first_name";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get branches for dropdown
$branch_sql = "SELECT branch_no, city FROM Branch ORDER BY city";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $telephone = $_POST['telephone'];
    $preferred_property_type = $_POST['preferred_property_type'];
    $max_monthly_rent = $_POST['max_monthly_rent'];
    $comments = $_POST['comments'];
    $date_registered = $_POST['date_registered'];
    $seen_by_staff_no = $_POST['seen_by_staff_no'];
    $branch_no = $_POST['branch_no'];
    
    $sql = "UPDATE Renter SET first_name=:first_name, last_name=:last_name, address=:address, 
            telephone=:telephone, preferred_property_type=:preferred_property_type, 
            max_monthly_rent=:max_monthly_rent, comments=:comments, date_registered=:date_registered, 
            seen_by_staff_no=:seen_by_staff_no, branch_no=:branch_no 
            WHERE renter_no=:renter_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':preferred_property_type', $preferred_property_type);
    $stmt->bindParam(':max_monthly_rent', $max_monthly_rent);
    $stmt->bindParam(':comments', $comments);
    $stmt->bindParam(':date_registered', $date_registered);
    $stmt->bindParam(':seen_by_staff_no', $seen_by_staff_no);
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
        <h2 class="mb-0">Edit Renter: <?php echo $renter['first_name'] . ' ' . $renter['last_name']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" class="form-control" name="first_name" value="<?php echo $renter['first_name']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" class="form-control" name="last_name" value="<?php echo $renter['last_name']; ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address *</label>
                    <textarea class="form-control" name="address" rows="2" required><?php echo $renter['address']; ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Telephone *</label>
                    <input type="text" class="form-control" name="telephone" value="<?php echo $renter['telephone']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Preferred Property Type</label>
                    <select class="form-select" name="preferred_property_type">
                        <option value="">Any</option>
                        <option value="Flat" <?php echo $renter['preferred_property_type'] == 'Flat' ? 'selected' : ''; ?>>Flat</option>
                        <option value="House" <?php echo $renter['preferred_property_type'] == 'House' ? 'selected' : ''; ?>>House</option>
                        <option value="Apartment" <?php echo $renter['preferred_property_type'] == 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                        <option value="Studio" <?php echo $renter['preferred_property_type'] == 'Studio' ? 'selected' : ''; ?>>Studio</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Maximum Monthly Rent (£)</label>
                    <input type="number" class="form-control" name="max_monthly_rent" value="<?php echo $renter['max_monthly_rent']; ?>" step="50">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Comments</label>
                    <textarea class="form-control" name="comments" rows="3"><?php echo $renter['comments']; ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date Registered *</label>
                    <input type="date" class="form-control" name="date_registered" value="<?php echo $renter['date_registered']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Seen By (Staff) *</label>
                    <select class="form-select" name="seen_by_staff_no" required>
                        <option value="">Select Staff</option>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?php echo $member['staff_no']; ?>" <?php echo $renter['seen_by_staff_no'] == $member['staff_no'] ? 'selected' : ''; ?>>
                            <?php echo $member['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['branch_no']; ?>" <?php echo $renter['branch_no'] == $branch['branch_no'] ? 'selected' : ''; ?>>
                            <?php echo $branch['city']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Renter</button>
            <a href="view.php?renter_no=<?php echo $renter['renter_no']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>