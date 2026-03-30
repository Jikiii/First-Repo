<?php
require_once '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$sql = "SELECT b.BranchNo, b.City,
               p.PropertyNo, p.StreetName, p.District,
               p.PropertyType, p.Rooms, p.RentAmount, p.Status
        FROM Branch b
        LEFT JOIN Property p ON b.BranchNo = p.BranchNo
        ORDER BY b.City, p.PropertyNo";

$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Group by branch */
$branches = [];

foreach ($results as $row) {
    $branchNo = $row['BranchNo'];

    $branches[$branchNo]['City'] = $row['City'];

    if ($row['PropertyNo']) {
        $branches[$branchNo]['Properties'][] = $row;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Properties by Branch</h2>
    </div>

    <div class="card-body">
        <?php foreach ($branches as $branch): ?>
            <h4><?php echo $branch['City']; ?></h4>

            <?php if (!empty($branch['Properties'])): ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Property No</th>
                            <th>Street</th>
                            <th>District</th>
                            <th>Type</th>
                            <th>Rooms</th>
                            <th>Monthly Rent</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($branch['Properties'] as $prop): ?>
                            <tr>
                                <td><?php echo $prop['PropertyNo']; ?></td>
                                <td><?php echo $prop['StreetName']; ?></td>
                                <td><?php echo $prop['District']; ?></td>
                                <td><?php echo $prop['PropertyType']; ?></td>
                                <td><?php echo $prop['Rooms']; ?></td>
                                <td><?php echo formatMoney($prop['RentAmount']); ?></td>
                                <td><?php echo $prop['Status']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No properties in this branch.</p>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<a href="index.php" class="btn btn-secondary mt-3">
    Back to Properties
</a>

<?php include '../../includes/footer.php'; ?>