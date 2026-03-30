<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$viewing_id = $_GET['id'];

$sql = "SELECT * FROM Viewing WHERE ViewingID = :viewing_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':viewing_id', $viewing_id);
$stmt->execute();
$viewing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$viewing) {
    echo "<div class='alert alert-danger'>Viewing record not found!</div>";
    include '../../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $view_date = $_POST['view_date'];
    $remarks = $_POST['remarks'];
    
    $sql = "UPDATE Viewing SET ViewDate=:view_date, Remarks=:remarks 
            WHERE ViewingID=:viewing_id";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':view_date', $view_date);
    $stmt->bindParam(':remarks', $remarks);
    $stmt->bindParam(':viewing_id', $viewing_id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Viewing record updated successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'view.php?id=$viewing_id'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error updating viewing record!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Edit Viewing Record</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property</label>
                    <input type="text" class="form-control" value="<?php echo $viewing['PropertyNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Renter</label>
                    <input type="text" class="form-control" value="<?php echo $viewing['RenterNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Viewing Date *</label>
                    <input type="date" class="form-control" name="view_date" value="<?php echo $viewing['ViewDate']; ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Remarks / Feedback</label>
                    <textarea class="form-control" name="remarks" rows="4"><?php echo $viewing['Remarks']; ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Viewing Record</button>
            <a href="view.php?id=<?php echo $viewing['ViewingID']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>