<?php
include '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Fetch renters along with their branch
$sql = "SELECT r.RenterNo, r.FName, r.LName, r.Address, r.Phone, r.PreferredType, r.MaxBudget, b.BranchName
        FROM Renter r
        LEFT JOIN Branch b ON r.BranchNo = b.BranchNo
        ORDER BY b.BranchName, r.LName";

$stmt = $db->prepare($sql);
$stmt->execute();
$renters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group renters by branch
$branches = [];
foreach ($renters as $r) {
    $branchName = $r['BranchName'] ?? 'No Branch';
    $branches[$branchName][] = $r;
}
?>

<h2>Prospective Renters by Branch</h2>

<?php foreach ($branches as $branch => $branchRenters): ?>
    <h4><?php echo $branch; ?></h4>
    <?php if (!empty($branchRenters)): ?>
        <table class="table table-hover mb-4">
            <thead>
                <tr>
                    <th>Renter No</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Preferred Type</th>
                    <th>Max Budget (£)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($branchRenters as $renter): ?>
                    <tr>
                        <td><?php echo $renter['RenterNo']; ?></td>
                        <td><?php echo $renter['FName'] . ' ' . $renter['LName']; ?></td>
                        <td><?php echo $renter['Address']; ?></td>
                        <td><?php echo $renter['Phone']; ?></td>
                        <td><?php echo $renter['PreferredType']; ?></td>
                        <td><?php echo number_format($renter['MaxBudget'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No prospective renters registered at this branch.</p>
    <?php endif; ?>
<?php endforeach; ?>

<a href="index.php" class="btn btn-secondary mt-3">Back to Home</a>