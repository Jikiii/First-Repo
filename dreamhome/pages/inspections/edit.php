<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$inspection_id = $_GET['id'];

$sql = "SELECT * FROM Inspection WHERE InspectionID = :inspection_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':inspection_id', $inspection_id);
$stmt->execute();
$inspection = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inspection) {
    echo "<div class='alert alert-danger'>Inspection record not found!</div>";
    include '../../includes/footer.php';
    exit;
}

$staff_sql = "SELECT StaffNo, CONCAT(FName, ' ', LName) as name FROM Staff ORDER BY FName";
$staff_stmt = $db->prepare($staff_sql);
$staff_stmt->execute();
$staff = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inspect_date = $_POST['inspect_date'];
    $notes = $_POST['notes'];
    $staff_no = $_POST['staff_no'];
    
    $sql = "UPDATE Inspection SET InspectDate=:inspect_date, Notes=:notes, StaffNo=:staff_no 
            WHERE InspectionID=:inspection_id";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':inspect_date', $inspect_date);
    $stmt->bindParam(':notes', $notes);
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
                    <input type="text" class="form-control" value="<?php echo $inspection['PropertyNo']; ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Inspector (Staff) *</label>
                    <select class="form-select" name="staff_no" required>
                        <?php foreach ($staff as $member): ?>
                        <option value="<?php echo $member['StaffNo']; ?>" <?php echo $inspection['StaffNo'] == $member['StaffNo'] ? 'selected' : ''; ?>>
                            <?php echo $member['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Inspection Date *</label>
                    <input type="date" class="form-control" name="inspect_date" value="<?php echo $inspection['InspectDate']; ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes *</label>
                    <textarea class="form-control" name="notes" rows="4" required><?php echo $inspection['Notes']; ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Inspection</button>
            <a href="view.php?id=<?php echo $inspection['InspectionID']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>