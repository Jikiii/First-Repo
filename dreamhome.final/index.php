<?php
include 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Check connection
if (!$db) {
    echo "<div class='alert alert-danger'>Database connection failed. Please check your configuration.</div>";
    include 'includes/footer.php';
    exit;
}

// Get counts for dashboard
$counts = [];

$queries = [
    'branches' => "SELECT COUNT(*) as count FROM Branch",
    'staff' => "SELECT COUNT(*) as count FROM Staff",
    'properties' => "SELECT COUNT(*) as count FROM Property WHERE Status = 'Available'",
    'renters' => "SELECT COUNT(*) as count FROM Renter",
    'leases' => "SELECT COUNT(*) as count FROM Lease WHERE Status = 'Active'",
    'inspections' => "SELECT COUNT(*) as count FROM Inspection",
    'viewings' => "SELECT COUNT(*) as count FROM Viewing",
    'advertisements' => "SELECT COUNT(*) as count FROM Advertisement"
];

foreach ($queries as $key => $sql) {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $counts[$key] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

// Get recent viewings
$recent_viewings_sql = "SELECT v.ViewingID, v.ViewDate, v.Remarks,
                        p.PropertyNo, p.StreetName, p.City,
                        CONCAT(r.FName, ' ', r.LName) as renter_name
                        FROM Viewing v
                        JOIN Property p ON v.PropertyNo = p.PropertyNo
                        JOIN Renter r ON v.RenterNo = r.RenterNo
                        ORDER BY v.ViewDate DESC LIMIT 5";
$recent_viewings_stmt = $db->prepare($recent_viewings_sql);
$recent_viewings_stmt->execute();
$recent_viewings = $recent_viewings_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent inspections
$recent_inspections_sql = "SELECT i.InspectionID, i.InspectDate, i.Notes,
                           p.PropertyNo, p.StreetName, p.City,
                           CONCAT(s.FName, ' ', s.LName) as inspector
                           FROM Inspection i
                           JOIN Property p ON i.PropertyNo = p.PropertyNo
                           JOIN Staff s ON i.StaffNo = s.StaffNo
                           ORDER BY i.InspectDate DESC LIMIT 5";
$recent_inspections_stmt = $db->prepare($recent_inspections_sql);
$recent_inspections_stmt->execute();
$recent_inspections = $recent_inspections_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Dashboard</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/branches/index.php'">
                            <h3><?php echo $counts['branches']; ?></h3>
                            <p>Branch Offices</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/staff/index.php'">
                            <h3><?php echo $counts['staff']; ?></h3>
                            <p>Staff Members</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/properties/index.php'">
                            <h3><?php echo $counts['properties']; ?></h3>
                            <p>Available Properties</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/renters/index.php'">
                            <h3><?php echo $counts['renters']; ?></h3>
                            <p>Active Renters</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/leases/index.php'">
                            <h3><?php echo $counts['leases']; ?></h3>
                            <p>Active Leases</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/inspections/index.php'">
                            <h3><?php echo $counts['inspections']; ?></h3>
                            <p>Total Inspections</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/viewings/index.php'">
                            <h3><?php echo $counts['viewings']; ?></h3>
                            <p>Property Viewings</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/advertisements/index.php'">
                            <h3><?php echo $counts['advertisements']; ?></h3>
                            <p>advertisement</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dashboard-card" onclick="location.href='pages/properties/search.php'">
                            <h3>Search</h3>
                            <p>Search Properties</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Recent Viewings</h4>
            </div>
            <div class="card-body">
                <?php if (count($recent_viewings) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Property</th>
                                <th>Renter</th>
                                <th>Remarks</th>
                            </thead>
                        <tbody>
                            <?php foreach ($recent_viewings as $viewing): ?>
                            <tr onclick="location.href='pages/viewings/view.php?id=<?php echo $viewing['ViewingID']; ?>'" style="cursor: pointer;">
                                <td><?php echo formatDate($viewing['ViewDate']); ?></td>
                                <td><?php echo $viewing['PropertyNo'] . ' - ' . $viewing['StreetName']; ?></td>
                                <td><?php echo $viewing['renter_name']; ?></td>
                                <td><?php echo substr($viewing['Remarks'], 0, 30) . (strlen($viewing['Remarks']) > 30 ? '...' : ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No viewings recorded yet.</p>
                <?php endif; ?>
                <a href="pages/viewings/index.php" class="btn btn-sm btn-primary">View All Viewings</a>
                <a href="pages/viewings/add.php" class="btn btn-sm btn-success">Record New Viewing</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Recent Inspections</h4>
            </div>
            <div class="card-body">
                <?php if (count($recent_inspections) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Property</th>
                                <th>Inspector</th>
                                <th>Notes</th>
                            </thead>
                        <tbody>
                            <?php foreach ($recent_inspections as $inspection): ?>
                            <tr onclick="location.href='pages/inspections/view.php?id=<?php echo $inspection['InspectionID']; ?>'" style="cursor: pointer;">
                                <td><?php echo formatDate($inspection['InspectDate']); ?></td>
                                <td><?php echo $inspection['PropertyNo'] . ' - ' . $inspection['StreetName']; ?></td>
                                <td><?php echo $inspection['inspector']; ?></td>
                                <td><?php echo substr($inspection['Notes'], 0, 30) . (strlen($inspection['Notes']) > 30 ? '...' : ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No inspections recorded yet.</p>
                <?php endif; ?>
                <a href="pages/inspections/index.php" class="btn btn-sm btn-primary">View All Inspections</a>
                <a href="pages/inspections/add.php" class="btn btn-sm btn-success">Record New Inspection</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Quick Actions</h4>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2 mb-2">
                        <a href="pages/properties/add.php" class="btn btn-outline-primary w-100">Add Property</a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="pages/staff/add.php" class="btn btn-outline-primary w-100">Add Staff</a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="pages/renters/add.php" class="btn btn-outline-primary w-100">Add Renter</a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="pages/leases/add.php" class="btn btn-outline-success w-100">New Lease</a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="pages/inspections/add.php" class="btn btn-outline-info w-100">Record Inspection</a>
                    </div>
                    <div class="col-md-2 mb-2">
                        <a href="pages/viewings/add.php" class="btn btn-outline-warning w-100">Record Viewing</a>
                    </div>
                    <div class="col mb-2">
                        <a href="pages/advertisements/add.php" class="btn btn-outline-dark w-100">Add Advertisement</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>