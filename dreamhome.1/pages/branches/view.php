<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$branch_no = $_GET['branch_no'];
$sql = "SELECT * FROM Branch WHERE branch_no = :branch_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':branch_no', $branch_no);
$stmt->execute();
$branch = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$branch) {
    echo "<div class='alert alert-danger'>Branch not found!</div>";
    include '../../includes/footer.php';
    exit;
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Branch Details: <?php echo $branch['branch_no']; ?></h2>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">Branch Number</th>
                <td><?php echo $branch['branch_no']; ?></td>
            </tr>
            <tr>
                <th>Street</th>
                <td><?php echo $branch['street']; ?></td>
            </tr>
            <tr>
                <th>Area</th>
                <td><?php echo $branch['area']; ?></td>
            </tr>
            <tr>
                <th>City</th>
                <td><?php echo $branch['city']; ?></td>
            </tr>
            <tr>
                <th>Postcode</th>
                <td><?php echo $branch['postcode']; ?></td>
            </tr>
            <tr>
                <th>Telephone</th>
                <td><?php echo $branch['telephone']; ?></td>
            </tr>
            <tr>
                <th>Fax</th>
                <td><?php echo $branch['fax']; ?></td>
            </tr>
        </table>
        <a href="index.php" class="btn btn-secondary">Back to List</a>
        <a href="edit.php?branch_no=<?php echo $branch['branch_no']; ?>" class="btn btn-warning">Edit</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>