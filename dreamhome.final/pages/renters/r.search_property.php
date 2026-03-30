<?php
require_once '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

/* Get all renters */
$renter_sql = "SELECT RenterNo, FName, LName, BranchNo FROM Renter ORDER BY LName";
$renter_stmt = $db->prepare($renter_sql);
$renter_stmt->execute();
$renters = $renter_stmt->fetchAll(PDO::FETCH_ASSOC);

$properties = [];
$selected_renter_no = null;
$renter_requirements = null;

if (isset($_GET['renter_no']) && !empty($_GET['renter_no'])) {
    $selected_renter_no = $_GET['renter_no'];

    /* Get renter requirements */
    $req_sql = "SELECT PreferredType, MaxBudget, BranchNo FROM Renter WHERE RenterNo = :renter_no";
    $req_stmt = $db->prepare($req_sql);
    $req_stmt->bindParam(':renter_no', $selected_renter_no);
    $req_stmt->execute();
    $renter_requirements = $req_stmt->fetch(PDO::FETCH_ASSOC);

    if ($renter_requirements) {
        /* Fetch properties matching requirements */
        $prop_sql = "SELECT p.PropertyNo, p.StreetName, p.District, p.City, p.PropertyType, p.Rooms, 
                            p.RentAmount, p.Status, b.BranchName
                     FROM Property p
                     INNER JOIN Branch b ON p.BranchNo = b.BranchNo
                     WHERE p.Status = 'Available'
                       AND p.RentAmount <= :max_budget
                       AND p.PropertyType LIKE :pref_type
                       AND p.BranchNo = :branch_no
                     ORDER BY p.RentAmount ASC";

        $prop_stmt = $db->prepare($prop_sql);
        $prop_stmt->bindValue(':max_budget', $renter_requirements['MaxBudget']);
        $prop_stmt->bindValue(':pref_type', '%' . $renter_requirements['PreferredType'] . '%');
        $prop_stmt->bindValue(':branch_no', $renter_requirements['BranchNo']);
        $prop_stmt->execute();

        $properties = $prop_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<form method="GET" class="mb-3">
    <label>Select Renter</label>
    <select name="renter_no" class="form-select" onchange="this.form.submit()">
        <option value="">-- Choose Renter --</option>
        <?php foreach ($renters as $r): ?>
            <option value="<?php echo $r['RenterNo']; ?>" 
                <?php echo ($selected_renter_no == $r['RenterNo']) ? 'selected' : ''; ?>>
                <?php echo $r['FName'] . ' ' . $r['LName']; ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($renter_requirements): ?>
<div class="card mb-3">
    <div class="card-header">
        <h4>Renter Requirements</h4>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Preferred Property Type</th>
                <td><?php echo $renter_requirements['PreferredType'] ?: 'Any'; ?></td>
            </tr>
            <tr>
                <th>Maximum Budget</th>
                <td>£<?php echo number_format($renter_requirements['MaxBudget'], 2); ?></td>
            </tr>
            <tr>
                <th>Preferred Branch</th>
                <?php 
                // Get branch name
                $branch_sql = "SELECT BranchName FROM Branch WHERE BranchNo = :branch_no";
                $branch_stmt = $db->prepare($branch_sql);
                $branch_stmt->bindParam(':branch_no', $renter_requirements['BranchNo']);
                $branch_stmt->execute();
                $branch = $branch_stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <td><?php echo $branch ? $branch['BranchName'] : 'Any'; ?></td>
            </tr>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($properties)): ?>
<table class="table table-striped mt-3">
    <thead class="table-dark">
        <tr>
            <th>Property No</th>
            <th>Street</th>
            <th>District</th>
            <th>City</th>
            <th>Type</th>
            <th>Rooms</th>
            <th>Rent Amount</th>
            <th>Branch</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($properties as $prop): ?>
        <tr>
            <td><?php echo $prop['PropertyNo']; ?></td>
            <td><?php echo $prop['StreetName']; ?></td>
            <td><?php echo $prop['District']; ?></td>
            <td><?php echo $prop['City']; ?></td>
            <td><?php echo $prop['PropertyType']; ?></td>
            <td><?php echo $prop['Rooms']; ?></td>
            <td>£<?php echo number_format($prop['RentAmount'], 2); ?></td>
            <td><?php echo $prop['BranchName']; ?></td>
            <td>
                <span class="badge <?php echo $prop['Status']=='Available' ? 'bg-success' : 'bg-secondary'; ?>">
                    <?php echo $prop['Status']; ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php elseif($selected_renter_no): ?>
<p class="mt-3">No available properties match this renter's requirements.</p>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary mt-3">Back to Home</a>

<?php include '../../includes/footer.php'; ?>