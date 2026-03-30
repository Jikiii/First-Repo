<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['delete'])) {
    $renter_no = $_GET['delete'];
    $sql = "DELETE FROM Renter WHERE RenterNo = :renter_no";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':renter_no', $renter_no);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Renter deleted successfully!</div>";
    }
}

$sql = "SELECT r.*, b.BranchName as branch_name
        FROM Renter r
        LEFT JOIN Branch b ON r.BranchNo = b.BranchNo
        ORDER BY r.RenterNo";
$stmt = $db->prepare($sql);
$stmt->execute();
$renters = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Renter Management</h2>
        <div>
            <button class="btn btn-light" onclick="location.href='add.php'">+ Add New Renter</button>
            <button class="btn btn-light" onclick="location.href='r.by_branch.php'"> renter by branch</button>
            <button class="btn btn-light" onclick="location.href='r.search_property.php'"> search property</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Renter No</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Preferred Type</th>
                        <th>Max Budget</th>
                        <th>Branch</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($renters as $renter): ?>
                    <tr>
                        <td><?php echo $renter['RenterNo']; ?></td>
                        <td><?php echo $renter['FName'] . ' ' . $renter['LName']; ?></td>
                        <td><?php echo substr($renter['Address'], 0, 30) . '...'; ?></td>
                        <td><?php echo $renter['Phone']; ?></td>
                        <td><?php echo $renter['PreferredType'] ?: 'Any'; ?></td>
                        <td><?php echo formatMoney($renter['MaxBudget']); ?></td>
                        <td><?php echo $renter['branch_name']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="location.href='view.php?renter_no=<?php echo $renter['RenterNo']; ?>'">View</button>
                            <button class="btn btn-sm btn-warning" onclick="location.href='edit.php?renter_no=<?php echo $renter['RenterNo']; ?>'">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Delete this renter?')) location.href='index.php?delete=<?php echo $renter['RenterNo']; ?>'">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>