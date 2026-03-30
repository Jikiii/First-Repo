<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$branch_no = $_GET['branch_no'];

// Get branch details
$sql = "SELECT * FROM Branch WHERE branch_no = :branch_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':branch_no', $branch_no);
$stmt->execute();
$branch = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$branch) {
    echo "<div class='alert alert-danger'>Branch not found!</div>";
    include '../../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $street = $_POST['street'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $telephone = $_POST['telephone'];
    $fax = $_POST['fax'];
    
    $sql = "UPDATE Branch SET street=:street, area=:area, city=:city, 
            postcode=:postcode, telephone=:telephone, fax=:fax 
            WHERE branch_no=:branch_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':fax', $fax);
    $stmt->bindParam(':branch_no', $branch_no);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Branch updated successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'view.php?branch_no=$branch_no'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error updating branch!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Edit Branch: <?php echo $branch['branch_no']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Street *</label>
                    <input type="text" class="form-control" name="street" value="<?php echo $branch['street']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Area</label>
                    <input type="text" class="form-control" name="area" value="<?php echo $branch['area']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-control" name="city" value="<?php echo $branch['city']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Postcode *</label>
                    <input type="text" class="form-control" name="postcode" value="<?php echo $branch['postcode']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Telephone *</label>
                    <input type="text" class="form-control" name="telephone" value="<?php echo $branch['telephone']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fax</label>
                    <input type="text" class="form-control" name="fax" value="<?php echo $branch['fax']; ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Branch</button>
            <a href="view.php?branch_no=<?php echo $branch['branch_no']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>