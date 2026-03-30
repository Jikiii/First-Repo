<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle Delete
if (isset($_GET['delete'])) {
    $inspection_id = $_GET['delete'];
    $sql = "DELETE FROM PropertyInspection WHERE inspection_id = :inspection_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':inspection_id', $inspection_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Inspection deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting inspection!</div>";
    }
}

$sql = "SELECT i.*, p.street, p.city, p.type as property_type,
        CONCAT(s.first_name, ' ', s.last_name) as inspector_name
        FROM PropertyInspection i 
        JOIN Property p ON i.property_no = p.property_no
        JOIN Staff s ON i.staff_no = s.staff_no
        ORDER BY i.inspection_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$inspections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Property Inspections</h2>
        <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Inspection</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Property</th>
                        <th>Address</th>
                        <th>Inspection Date</th>
                        <th>Inspector</th>
                        <th>Comments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inspections as $inspection): ?>
                    <tr>
                        <td><?php echo $inspection['inspection_id']; ?></td>
                        <td><?php echo $inspection['property_type']; ?></td>
                        <td><?php echo $inspection['street'] . ', ' . $inspection['city']; ?></td>
                        <td><?php echo $inspection['inspection_date']; ?></td>
                        <td><?php echo $inspection['inspector_name']; ?></td>
                        <td><?php echo substr($inspection['comments'], 0, 50) . (strlen($inspection['comments']) > 50 ? '...' : ''); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewInspection(<?php echo $inspection['inspection_id']; ?>)">View</button>
                            <button class="btn btn-sm btn-warning" onclick="editInspection(<?php echo $inspection['inspection_id']; ?>)">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteInspection(<?php echo $inspection['inspection_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function viewInspection(id) { window.location.href = `view.php?id=${id}`; }
function editInspection(id) { window.location.href = `edit.php?id=${id}`; }
function deleteInspection(id) {
    if (confirm('Are you sure you want to delete this inspection record?')) {
        window.location.href = `index.php?delete=${id}`;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>