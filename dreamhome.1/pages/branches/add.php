<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branch_no = $_POST['branch_no'];
    $street = $_POST['street'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $telephone = $_POST['telephone'];
    $fax = $_POST['fax'];
    
    $sql = "INSERT INTO Branch (branch_no, street, area, city, postcode, telephone, fax) 
            VALUES (:branch_no, :street, :area, :city, :postcode, :telephone, :fax)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':branch_no', $branch_no);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':fax', $fax);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Branch added successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error adding branch!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Add New Branch</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch Number *</label>
                    <input type="text" class="form-control" name="branch_no" required maxlength="5">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Street *</label>
                    <input type="text" class="form-control" name="street" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Area</label>
                    <input type="text" class="form-control" name="area">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-control" name="city" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Postcode *</label>
                    <input type="text" class="form-control" name="postcode" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Telephone *</label>
                    <input type="text" class="form-control" name="telephone" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fax</label>
                    <input type="text" class="form-control" name="fax">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Branch</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>