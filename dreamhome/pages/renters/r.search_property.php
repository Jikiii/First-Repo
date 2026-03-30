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

if (isset($_GET['renter_no']) && !empty($_GET['renter_no'])) {
    $renter_no = $_GET['renter_no'];

    /* Get renter requirements */
    $req_sql = "SELECT PreferredType, MaxBudget, BranchNo FROM Renter WHERE RenterNo = :renter_no";
    $req_stmt = $db->prepare($req_sql);
    $req_stmt->bindParam(':renter_no', $renter_no);
    $req_stmt->execute();
    $renter = $req_stmt->fetch(PDO::FETCH_ASSOC);

    if ($renter) {
        /* Search properties that match requirements */
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
        $prop_stmt->bindValue(':max_budget', $renter['MaxBudget']);
        $prop_stmt->bindValue(':pref_type', '%' . $renter['PreferredType'] . '%');
        $prop_stmt->bindValue(':branch_no', $renter['BranchNo']);
        $prop_stmt->execute();

        $properties = $prop_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<form method="GET" class="mb-3">
    <label>Select Renter</label>
    <select name="renter_no" class="form-select">
        <option value="">Choose Renter</option>
        <?php foreach ($renters as $r): ?>
            <option value="<?php echo $r['RenterNo']; ?>" 
                <?php echo (isset($_GET['renter_no']) && $_GET['renter_no'] == $r['RenterNo']) ? 'selected' : ''; ?>>
                <?php echo $r['FName'] . ' ' . $r['LName']; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary mt-2">
        Search Properties
    </button>
</form>

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
            <td><?php echo $prop['Status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php elseif(isset($_GET['renter_no'])): ?>
<p class="mt-3">No available properties match this renter's requirements.</p>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary mt-3">Back to Home</a>