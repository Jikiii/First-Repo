<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $branch_no = $_POST['branch_no'];
    $branch_name = $_POST['branch_name'];
    $street = $_POST['street'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];
    
    $sql = "INSERT INTO Branch (BranchNo, BranchName, Street, Area, City, PostCode, ContactNo, Email) 
            VALUES (:branch_no, :branch_name, :street, :area, :city, :postcode, :contact_no, :email)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':branch_no', $branch_no);
    $stmt->bindParam(':branch_name', $branch_name);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':contact_no', $contact_no);
    $stmt->bindParam(':email', $email);
    
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
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch Number *</label>
                    <input type="text" class="form-control" name="branch_no" required maxlength="10">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch Name *</label>
                    <input type="text" class="form-control" name="branch_name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Street *</label>
                    <input type="text" class="form-control" name="street" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Area</label>
                    <input type="text" class="form-control" name="area">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-control" name="city" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Postcode *</label>
                    <input type="text" class="form-control" name="postcode" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Contact No *</label>
                    <input type="text" class="form-control" name="contact_no" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Branch</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>