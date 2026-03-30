<?php
include '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get all properties for the dropdown
$property_sql = "SELECT PropertyNo, StreetName, City, PropertyType FROM Property ORDER BY StreetName";
$property_stmt = $db->prepare($property_sql);
$property_stmt->execute();
$properties = $property_stmt->fetchAll(PDO::FETCH_ASSOC);

$comments = [];
$selected_property = '';

if (isset($_GET['property_no']) && !empty($_GET['property_no'])) {
    $selected_property = $_GET['property_no'];

    $sql = "SELECT v.ViewingID, v.ViewDate, v.Remarks, 
                   CONCAT(r.FName, ' ', r.LName) as renter_name
            FROM Viewing v
            LEFT JOIN Renter r ON v.RenterNo = r.RenterNo
            WHERE v.PropertyNo = :property_no
            ORDER BY v.ViewDate DESC";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':property_no', $selected_property);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Renter Comments Report</h2>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <label>Select Property:</label>
            <select name="property_no" class="form-select" onchange="this.form.submit()">
                <option value="">-- Choose Property --</option>
                <?php foreach ($properties as $property): ?>
                    <option value="<?php echo $property['PropertyNo']; ?>" <?php echo ($selected_property == $property['PropertyNo']) ? 'selected' : ''; ?>>
                        <?php echo $property['StreetName'] . ', ' . $property['City'] . ' (' . $property['PropertyType'] . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (!empty($comments)): ?>
        <div class="table-responsive">
            <table id= "commentsTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Viewing ID</th>
                        <th>Renter</th>
                        <th>View Date</th>
                        <th>Comments / Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?php echo $comment['ViewingID']; ?></td>
                        <td><?php echo $comment['renter_name']; ?></td>
                        <td><?php echo formatDate($comment['ViewDate']); ?></td>
                        <td><?php echo $comment['Remarks']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php elseif($selected_property): ?>
            <div class="alert alert-info">No comments found for this property.</div>
        <?php endif; ?>
    </div>
</div>

<a href="index.php" class="btn btn-secondary mt-3">Back to Viewings</a>

<?php include '../../includes/footer.php'; ?>