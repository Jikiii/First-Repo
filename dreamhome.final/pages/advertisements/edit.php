<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$ad_id = $_GET['id'];

// Get Advertisement along with Property info
$sql = "SELECT a.*, p.PropertyNo, p.StreetName, p.District, p.City, p.PostCode, 
               p.PropertyType, p.Rooms, p.RentAmount
        FROM Advertisement a
        JOIN Property p ON a.PropertyNo = p.PropertyNo
        WHERE a.AdID = :ad_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':ad_id', $ad_id);
$stmt->execute();
$ad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ad) {
    echo "<div class='alert alert-danger'>Advertisement not found!</div>";
    include '../../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $media_source = $_POST['media_source'];
    $publish_date = $_POST['publish_date'];
    
    $sql = "UPDATE Advertisement SET MediaSource=:media_source, PublishDate=:publish_date
            WHERE AdID=:ad_id";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':media_source', $media_source);
    $stmt->bindParam(':publish_date', $publish_date);
    $stmt->bindParam(':ad_id', $ad_id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Advertisement updated successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'view.php?id=$ad_id'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error updating advertisement!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Edit Advertisement</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Property</label>
                    <input type="text" class="form-control" 
                           value="<?php echo $ad['PropertyNo'] . ' - ' . $ad['StreetName'] . ', ' . $ad['City']; ?>" 
                           readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Monthly Rent (£)</label>
                    <input type="text" class="form-control" 
                           value="<?php echo number_format($ad['RentAmount'], 2); ?>" readonly disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Media Source (Newspaper/Website) *</label>
                    <input type="text" class="form-control" name="media_source" 
                           value="<?php echo $ad['MediaSource']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Publish Date *</label>
                    <input type="date" class="form-control" name="publish_date" 
                           value="<?php echo $ad['PublishDate']; ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Advertisement</button>
            <a href="view.php?id=<?php echo $ad['AdID']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>