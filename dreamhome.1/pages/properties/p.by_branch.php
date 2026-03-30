<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$sql = "SELECT b.branch_no, b.city, p.property_no, p.street, p.area, p.type, 
               p.rooms, p.monthly_rent, p.status
        FROM Branch b
        LEFT JOIN Property p ON b.branch_no = p.branch_no
        ORDER BY b.city, p.property_no";

$stmt = $db->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by branch
$branches = [];
foreach ($results as $row) {
    $branches[$row['branch_no']]['city'] = $row['city'];
    if ($row['property_no']) {
        $branches[$row['branch_no']]['properties'][] = $row;
    }
}
?>
<div class="card">
    <div class="card-header">
        <h2>Properties by Branch</h2>
    </div>
    <div class="card-body">
        <?php foreach ($branches as $branch): ?>
            <h4><?php echo $branch['city']; ?></h4>
            <?php if (!empty($branch['properties'])): ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Property No</th>
                            <th>Street</th>
                            <th>Area</th>
                            <th>Type</th>
                            <th>Rooms</th>
                            <th>Monthly Rent</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($branch['properties'] as $prop): ?>
                            <tr>
                                <td><?php echo $prop['property_no']; ?></td>
                                <td><?php echo $prop['street']; ?></td>
                                <td><?php echo $prop['area']; ?></td>
                                <td><?php echo $prop['type']; ?></td>
                                <td><?php echo $prop['rooms']; ?></td>
                                <td>£<?php echo number_format($prop['monthly_rent'], 2); ?></td>
                                <td><?php echo ucfirst($prop['status']); ?></td>
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
<a href="index.php" class="btn btn-secondary mt-3">Back to Properties</a>

<?php include '../../includes/footer.php'; ?>