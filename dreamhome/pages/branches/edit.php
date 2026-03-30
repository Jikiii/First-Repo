<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$branch_no = $_GET['branch_no'];

$sql = "SELECT * FROM Branch WHERE BranchNo = :branch_no";
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
    $branch_name = $_POST['branch_name'];
    $street = $_POST['street'];
    $area = $_POST['area'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];
    
    $sql = "UPDATE Branch SET BranchName=:branch_name, Street=:street, Area=:area, City=:city, 
            PostCode=:postcode, ContactNo=:contact_no, Email=:email 
            WHERE BranchNo=:branch_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':branch_name', $branch_name);
    $stmt->bindParam(':street', $street);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':contact_no', $contact_no);
    $stmt->bindParam(':email', $email);
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
        <h2 class="mb-0">Edit Branch: <?php echo $branch['BranchNo']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch Number</label>
                    <input type="text" class="form-control" value="<?php echo $branch['BranchNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch Name *</label>
                    <input type="text" class="form-control" name="branch_name" value="<?php echo $branch['BranchName']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Street *</label>
                    <input type="text" class="form-control" name="street" value="<?php echo $branch['Street']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Area</label>
                    <input type="text" class="form-control" name="area" value="<?php echo $branch['Area']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-control" name="city" value="<?php echo $branch['City']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Postcode *</label>
                    <input type="text" class="form-control" name="postcode" value="<?php echo $branch['PostCode']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Contact No *</label>
                    <input type="text" class="form-control" name="contact_no" value="<?php echo $branch['ContactNo']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $branch['Email']; ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Branch</button>
            <a href="view.php?branch_no=<?php echo $branch['BranchNo']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>