<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$inspection_id = $_GET['id'];

// Get inspection details
$sql = "SELECT * FROM PropertyInspection WHERE inspection_id = :inspection_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':inspection_id', $inspection_id);
$stmt->execute();
$inspection = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inspection) {
    echo "<div class='alert alert-danger'>Inspection record not found!</div>";
    include '../../includes/footer.php';
    exit;
}

// Get staff for dropdown
$staff_sql = "SELECT staff_no, CONCAT(first_name, ' ', last_name) as name FROM Staff ORDER BY first_name";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inspection_date = $_POST['inspection_date'];
    $comments = $_POST['comments'];
    $staff_no = $_POST['staff_no'];
    
    $sql = "UPDATE PropertyInspection SET inspection_date=:inspection_date, comments=:comments, staff_no=:staff_no 
            WHERE inspection_id=:inspection_id";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':inspection_date', $inspection_date);
    $stmt->bindParam(':comments', $comments);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':inspection_id', $inspection_id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Inspection updated successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'view.php?id=$inspection_id'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error updating inspection!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Edit Inspection Record</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property</label>
                    <input type="text" class="form-control" value="<?php echo $inspection['property_no']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Inspector (Staff) *</label>
                    <select class="form-select" name="staff_no" required>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?php echo $member['staff_no']; ?>" <?php echo $inspection['staff_no'] == $member['staff_no'] ? 'selected' : ''; ?>>
                            <?php echo $member['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Inspection Date *</label>
                    <input type="date" class="form-control" name="inspection_date" value="<?php echo $inspection['inspection_date']; ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Comments *</label>
                    <textarea class="form-control" name="comments" rows="4" required><?php echo $inspection['comments']; ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Inspection</button>
            <a href="view.php?id=<?php echo $inspection['inspection_id']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>