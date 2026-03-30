<?php
include '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// DELETE ADVERTISEMENT
if (isset($_GET['delete'])) {
    $ad_id = $_GET['delete'];
    $sql = "DELETE FROM Advertisement WHERE AdID = :ad_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':ad_id', $ad_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Advertisement deleted successfully!</div>";
    }
}

// GET ALL ADVERTISEMENTS WITH PROPERTY RENT
$sql = "SELECT a.*, p.PropertyNo, p.StreetName, p.City, p.PropertyType, p.RentAmount
        FROM Advertisement a
        JOIN Property p ON a.PropertyNo = p.PropertyNo  
        ORDER BY a.PublishDate DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$advertisements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Advertisements</h2>
        <div>
            <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Advertisement</button>
            <button class="btn btn-light" onclick="location.href='property_ads.php'"> property list</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Ad ID</th>
                        <th>Property</th>
                        <th>Newspaper / Media</th>
                        <th>Publish Date</th>
                        <th>Rent Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($advertisements as $ad): ?>
                    <tr>
                        <td><?php echo $ad['AdID']; ?></td>
                        <td>
                            <strong><?php echo $ad['PropertyNo']; ?></strong><br>
                            <small class="text-muted"><?php echo $ad['PropertyType'] . ' - ' . $ad['StreetName']; ?></small>
                        </td>
                        <td><?php echo $ad['MediaSource']; ?></td>
                        <td><?php echo formatDate($ad['PublishDate']); ?></td>
                        <td><?php echo formatMoney($ad['RentAmount']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="location.href='view.php?id=<?php echo $ad['AdID']; ?>'">View</button>
                            <button class="btn btn-sm btn-warning" onclick="location.href='edit.php?id=<?php echo $ad['AdID']; ?>'">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete this advertisement?')) location.href='index.php?delete=<?php echo $ad['AdID']; ?>'">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($advertisements)) : ?>
                    <tr>
                        <td colspan="6" class="text-center">No advertisements found.</td>
                    </tr>
                    <?php endif; ?>
                    
                </tbody>
            </table>
            
        </div>
        
    </div>
</div>

<?php include '../../includes/footer.php'; ?>