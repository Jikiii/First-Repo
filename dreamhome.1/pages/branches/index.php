<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get all branches
$sql = "SELECT * FROM Branch ORDER BY branch_no";
$stmt = $db->prepare($sql);
$stmt->execute();
$branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Branch Offices</h2>
        <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Branch</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Branch No</th>
                        <th>Street</th>
                        <th>Area</th>
                        <th>City</th>
                        <th>Postcode</th>
                        <th>Telephone</th>
                        <th>Fax</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($branches as $branch): ?>
                    <tr>
                        <td><?php echo $branch['branch_no']; ?></td>
                        <td><?php echo $branch['street']; ?></td>
                        <td><?php echo $branch['area']; ?></td>
                        <td><?php echo $branch['city']; ?></td>
                        <td><?php echo $branch['postcode']; ?></td>
                        <td><?php echo $branch['telephone']; ?></td>
                        <td><?php echo $branch['fax']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewBranch('<?php echo $branch['branch_no']; ?>')">View</button>
                            <button class="btn btn-sm btn-warning" onclick="editBranch('<?php echo $branch['branch_no']; ?>')">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteBranch('<?php echo $branch['branch_no']; ?>')">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function viewBranch(branch_no) {
    window.location.href = `view.php?branch_no=${branch_no}`;
}
function editBranch(branch_no) {
    window.location.href = `edit.php?branch_no=${branch_no}`;
}
function deleteBranch(branch_no) {
    if (confirm('Are you sure you want to delete this branch?')) {
        window.location.href = `index.php?delete=${branch_no}`;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>