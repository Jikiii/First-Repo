<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['delete'])) {
    $branch_no = $_GET['delete'];
    $sql = "DELETE FROM Branch WHERE BranchNo = :branch_no";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':branch_no', $branch_no);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Branch deleted successfully!</div>";
    }
}

$sql = "SELECT * FROM Branch ORDER BY BranchNo";
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
                        <th>Branch Name</th>
                        <th>Street</th>
                        <th>Area</th>
                        <th>City</th>
                        <th>Postcode</th>
                        <th>Contact No</th>
                        <th>Actions</th>
                    </thead>
                <tbody>
                    <?php foreach ($branches as $branch): ?>
                    <tr>
                        <td><?php echo $branch['BranchNo']; ?></td>
                        <td><?php echo $branch['BranchName']; ?></td>
                        <td><?php echo $branch['Street']; ?></td>
                        <td><?php echo $branch['Area']; ?></td>
                        <td><?php echo $branch['City']; ?></td>
                        <td><?php echo $branch['PostCode']; ?></td>
                        <td><?php echo $branch['ContactNo']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="location.href='view.php?branch_no=<?php echo $branch['BranchNo']; ?>'">View</button>
                            <button class="btn btn-sm btn-warning" onclick="location.href='edit.php?branch_no=<?php echo $branch['BranchNo']; ?>'">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete this branch?')) location.href='index.php?delete=<?php echo $branch['BranchNo']; ?>'">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>