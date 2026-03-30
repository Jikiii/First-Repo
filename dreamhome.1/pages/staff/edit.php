<?php
include '../../includes/header.php';
include '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$staff_no = $_GET['staff_no'];

// Get staff details
$sql = "SELECT * FROM Staff WHERE staff_no = :staff_no";
$stmt = $db->prepare($sql);
$stmt->bindParam(':staff_no', $staff_no);
$stmt->execute();
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    echo "<div class='alert alert-danger'>Staff member not found!</div>";
    include '../../includes/footer.php';
    exit;
}

// Get branches for dropdown
$branch_sql = "SELECT branch_no, city FROM Branch ORDER BY city";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get supervisors for dropdown
$supervisor_sql = "SELECT staff_no, CONCAT(first_name, ' ', last_name) as name FROM Staff WHERE job_title IN ('Manager', 'Supervisor') AND staff_no != :staff_no";
$supervisor_stmt = $db->prepare($supervisor_sql);
$supervisor_stmt->bindParam(':staff_no', $staff_no);
$supervisor_stmt->execute();
$supervisors = $supervisor_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $telephone = $_POST['telephone'];
    $sex = $_POST['sex'];
    $date_of_birth = $_POST['date_of_birth'];
    $national_insurance_no = $_POST['national_insurance_no'];
    $job_title = $_POST['job_title'];
    $salary = $_POST['salary'];
    $date_joined = $_POST['date_joined'];
    $branch_no = $_POST['branch_no'];
    $supervisor_no = $_POST['supervisor_no'] ?: null;
    $typing_speed = $_POST['typing_speed'] ?: null;
    $car_allowance = $_POST['car_allowance'] ?: null;
    $monthly_bonus = $_POST['monthly_bonus'] ?: null;
    $manager_start_date = $_POST['manager_start_date'] ?: null;
    
    $sql = "UPDATE Staff SET first_name=:first_name, last_name=:last_name, address=:address, 
            telephone=:telephone, sex=:sex, date_of_birth=:date_of_birth, 
            national_insurance_no=:national_insurance_no, job_title=:job_title, 
            salary=:salary, date_joined=:date_joined, branch_no=:branch_no, 
            supervisor_no=:supervisor_no, typing_speed=:typing_speed, 
            car_allowance=:car_allowance, monthly_bonus=:monthly_bonus, 
            manager_start_date=:manager_start_date WHERE staff_no=:staff_no";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':sex', $sex);
    $stmt->bindParam(':date_of_birth', $date_of_birth);
    $stmt->bindParam(':national_insurance_no', $national_insurance_no);
    $stmt->bindParam(':job_title', $job_title);
    $stmt->bindParam(':salary', $salary);
    $stmt->bindParam(':date_joined', $date_joined);
    $stmt->bindParam(':branch_no', $branch_no);
    $stmt->bindParam(':supervisor_no', $supervisor_no);
    $stmt->bindParam(':typing_speed', $typing_speed);
    $stmt->bindParam(':car_allowance', $car_allowance);
    $stmt->bindParam(':monthly_bonus', $monthly_bonus);
    $stmt->bindParam(':manager_start_date', $manager_start_date);
    $stmt->bindParam(':staff_no', $staff_no);
    
   if ($stmt->execute()) {

    //  Next of Kin 
    $kin_full_name = $_POST['kin_full_name'] ?? null;
    $kin_relationship = $_POST['kin_relationship'] ?? null;
    $kin_address = $_POST['kin_address'] ?? null;
    $kin_telephone = $_POST['kin_telephone'] ?? null;

    // Check if kin exists
    $check_sql = "SELECT * FROM NextOfKin WHERE staff_no = :staff_no";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':staff_no', $staff_no);
    $check_stmt->execute();
    $existingKin = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($kin_full_name)) {

        if ($existingKin) {
            // UPDATE
            $update_sql = "UPDATE NextOfKin SET 
                            full_name = :full_name,
                            relationship = :relationship,
                            address = :address,
                            telephone = :telephone
                           WHERE staff_no = :staff_no";

            $update_stmt = $db->prepare($update_sql);
            $update_stmt->bindParam(':staff_no', $staff_no);
            $update_stmt->bindParam(':full_name', $kin_full_name);
            $update_stmt->bindParam(':relationship', $kin_relationship);
            $update_stmt->bindParam(':address', $kin_address);
            $update_stmt->bindParam(':telephone', $kin_telephone);
            $update_stmt->execute();

        } else {
            // INSERT
            $insert_sql = "INSERT INTO NextOfKin (staff_no, full_name, relationship, address, telephone)
                           VALUES (:staff_no, :full_name, :relationship, :address, :telephone)";

            $insert_stmt = $db->prepare($insert_sql);
            $insert_stmt->bindParam(':staff_no', $staff_no);
            $insert_stmt->bindParam(':full_name', $kin_full_name);
            $insert_stmt->bindParam(':relationship', $kin_relationship);
            $insert_stmt->bindParam(':address', $kin_address);
            $insert_stmt->bindParam(':telephone', $kin_telephone);
            $insert_stmt->execute();
        }
    }

    echo "<div class='alert alert-success'>Staff member updated successfully!</div>";
    echo "<script>setTimeout(function(){ window.location.href = 'view.php?staff_no=$staff_no'; }, 2000);</script>";

} else {
    echo "<div class='alert alert-danger'>Error updating staff member!</div>";
}
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Edit Staff Member: <?php echo $staff['first_name'] . ' ' . $staff['last_name']; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" class="form-control" name="first_name" value="<?php echo $staff['first_name']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" class="form-control" name="last_name" value="<?php echo $staff['last_name']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sex *</label>
                    <select class="form-select" name="sex" required>
                        <option value="M" <?php echo $staff['sex'] == 'M' ? 'selected' : ''; ?>>Male</option>
                        <option value="F" <?php echo $staff['sex'] == 'F' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address *</label>
                    <textarea class="form-control" name="address" rows="2" required><?php echo $staff['address']; ?></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Telephone *</label>
                    <input type="text" class="form-control" name="telephone" value="<?php echo $staff['telephone']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" class="form-control" name="date_of_birth" value="<?php echo $staff['date_of_birth']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">National Insurance No *</label>
                    <input type="text" class="form-control" name="national_insurance_no" value="<?php echo $staff['national_insurance_no']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Job Title *</label>
                    <select class="form-select" name="job_title" required id="jobTitle">
                        <option value="Manager" <?php echo $staff['job_title'] == 'Manager' ? 'selected' : ''; ?>>Manager</option>
                        <option value="Supervisor" <?php echo $staff['job_title'] == 'Supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                        <option value="Staff" <?php echo $staff['job_title'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                        <option value="Secretary" <?php echo $staff['job_title'] == 'Secretary' ? 'selected' : ''; ?>>Secretary</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Salary (£) *</label>
                    <input type="number" class="form-control" name="salary" value="<?php echo $staff['salary']; ?>" required step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date Joined *</label>
                    <input type="date" class="form-control" name="date_joined" value="<?php echo $staff['date_joined']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['branch_no']; ?>" <?php echo $staff['branch_no'] == $branch['branch_no'] ? 'selected' : ''; ?>>
                            <?php echo $branch['city']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Supervisor</label>
                    <select class="form-select" name="supervisor_no">
                        <option value="">None</option>
                        <?php foreach ($supervisors as $supervisor): ?>
                        <option value="<?php echo $supervisor['staff_no']; ?>" <?php echo $staff['supervisor_no'] == $supervisor['staff_no'] ? 'selected' : ''; ?>>
                            <?php echo $supervisor['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3" id="typingSpeedDiv" style="display:<?php echo $staff['job_title'] == 'Secretary' ? 'block' : 'none'; ?>">
                    <label class="form-label">Typing Speed (wpm)</label>
                    <input type="number" class="form-control" name="typing_speed" value="<?php echo $staff['typing_speed']; ?>">
                </div>
                <div class="col-md-4 mb-3" id="carAllowanceDiv" style="display:<?php echo $staff['job_title'] == 'Manager' ? 'block' : 'none'; ?>">
                    <label class="form-label">Car Allowance (£)</label>
                    <input type="number" class="form-control" name="car_allowance" value="<?php echo $staff['car_allowance']; ?>" step="0.01">
                </div>
                <div class="col-md-4 mb-3" id="bonusDiv" style="display:<?php echo $staff['job_title'] == 'Manager' ? 'block' : 'none'; ?>">
                    <label class="form-label">Monthly Bonus (£)</label>
                    <input type="number" class="form-control" name="monthly_bonus" value="<?php echo $staff['monthly_bonus']; ?>" step="0.01">
                </div>
                <div class="col-md-4 mb-3" id="startDateDiv" style="display:<?php echo $staff['job_title'] == 'Manager' ? 'block' : 'none'; ?>">
                    <label class="form-label">Manager Start Date</label>
                    <input type="date" class="form-control" name="manager_start_date" value="<?php echo $staff['manager_start_date']; ?>">
                </div>
            </div>
            <h4>Next of Kin Information</h4>

            <div class="col-md-6 mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="kin_full_name">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Relationship</label>
                <input type="text" class="form-control" name="kin_relationship">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="kin_address"></textarea>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Telephone</label>
                <input type="text" class="form-control" name="kin_telephone">
            </div>
            <button type="submit" class="btn btn-primary">Update Staff Member</button>
            <a href="view.php?staff_no=<?php echo $staff['staff_no']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>



<script>
document.getElementById('jobTitle').addEventListener('change', function() {
    const job = this.value;
    document.getElementById('typingSpeedDiv').style.display = job === 'Secretary' ? 'block' : 'none';
    document.getElementById('carAllowanceDiv').style.display = job === 'Manager' ? 'block' : 'none';
    document.getElementById('bonusDiv').style.display = job === 'Manager' ? 'block' : 'none';
    document.getElementById('startDateDiv').style.display = job === 'Manager' ? 'block' : 'none';
});
</script>

<?php include '../../includes/footer.php'; ?>