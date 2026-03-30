<?php
include '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get branches for dropdown
$branch_sql = "SELECT BranchNo, BranchName FROM Branch ORDER BY BranchName";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get supervisors for dropdown
$supervisor_sql = "SELECT StaffNo, CONCAT(FName, ' ', LName) AS name FROM Staff WHERE JobTitle IN ('Manager', 'Supervisor')";
$supervisor_stmt = $db->prepare($supervisor_sql);
$supervisor_stmt->execute();
$supervisors = $supervisor_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Staff fields from form
    $staff_no = $_POST['staff_no'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'] ?? null; // optional
    $sex = $_POST['sex'];
    $date_of_birth = $_POST['date_of_birth'];
    $national_insurance_no = $_POST['national_insurance_no'];
    $job_title = $_POST['job_title'];
    $salary = $_POST['salary'];
    $date_joined = $_POST['date_joined'];
    $branch_no = $_POST['branch_no'];

    // Optional job-specific fields (Secretary/Manager)
    $supervisor_no = $_POST['supervisor_no'] ?: null;
    $typing_speed = $_POST['typing_speed'] ?: null;
    $car_allowance = $_POST['car_allowance'] ?: null;
    $monthly_bonus = $_POST['monthly_bonus'] ?: null;
    $manager_start_date = $_POST['manager_start_date'] ?: null;

    // Insert staff
    $sql = "INSERT INTO Staff 
    (StaffNo, FName, LName, Address, Phone, Email, Gender, BirthDate, NationalID, JobTitle, Salary, HireDate, BranchNo)
    VALUES 
    (:staff_no, :first_name, :last_name, :address, :phone, :email, :gender, :birth_date, :national_id, :job_title, :salary, :hire_date, :branch_no)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':phone', $telephone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':gender', $sex);
    $stmt->bindParam(':birth_date', $date_of_birth);
    $stmt->bindParam(':national_id', $national_insurance_no);
    $stmt->bindParam(':job_title', $job_title);
    $stmt->bindParam(':salary', $salary);
    $stmt->bindParam(':hire_date', $date_joined);
    $stmt->bindParam(':branch_no', $branch_no);

    if ($stmt->execute()) {
        // Insert Next-of-Kin if provided
        $kin_full_name = $_POST['kin_full_name'] ?? null;
        $kin_relationship = $_POST['kin_relationship'] ?? null;
        $kin_address = $_POST['kin_address'] ?? null;
        $kin_telephone = $_POST['kin_telephone'] ?? null;

        if (!empty($kin_full_name)) {
            $kin_sql = "INSERT INTO NextOfKin (StaffNo, KinName, Relation, Address, Phone)
                        VALUES (:staff_no, :kin_name, :relation, :address, :phone)";
            $kin_stmt = $db->prepare($kin_sql);
            $kin_stmt->bindParam(':staff_no', $staff_no);
            $kin_stmt->bindParam(':kin_name', $kin_full_name);
            $kin_stmt->bindParam(':relation', $kin_relationship);
            $kin_stmt->bindParam(':address', $kin_address);
            $kin_stmt->bindParam(':phone', $kin_telephone);
            $kin_stmt->execute();
        }

        echo "<div class='alert alert-success'>Staff member added successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-danger'>Error adding staff member!</div>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Add New Staff Member</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <!-- Staff Info -->
                <div class="col-md-4 mb-3">
                    <label class="form-label">Staff Number *</label>
                    <input type="text" class="form-control" name="staff_no" required maxlength="5">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" class="form-control" name="last_name" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address *</label>
                    <textarea class="form-control" name="address" rows="2" required></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Telephone *</label>
                    <input type="text" class="form-control" name="telephone" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sex *</label>
                    <select class="form-select" name="sex" required>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" class="form-control" name="date_of_birth" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">National ID *</label>
                    <input type="text" class="form-control" name="national_insurance_no" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Job Title *</label>
                    <select class="form-select" name="job_title" required id="jobTitle">
                        <option value="Manager">Manager</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Staff">Staff</option>
                        <option value="Secretary">Secretary</option>
                        <option value="Administrator">Administrator</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Salary (£) *</label>
                    <input type="number" class="form-control" name="salary" required step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Hire Date *</label>
                    <input type="date" class="form-control" name="date_joined" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch *</label>
                    <select class="form-select" name="branch_no" required>
                        <option value="">Select Branch</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo $branch['BranchNo']; ?>"><?php echo $branch['BranchName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Supervisor</label>
                    <select class="form-select" name="supervisor_no">
                        <option value="">None</option>
                        <?php foreach ($supervisors as $supervisor): ?>
                            <option value="<?php echo $supervisor['StaffNo']; ?>"><?php echo $supervisor['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Job-specific fields -->
                <div class="col-md-4 mb-3" id="typingSpeedDiv" style="display:none;">
                    <label class="form-label">Typing Speed (wpm)</label>
                    <input type="number" class="form-control" name="typing_speed">
                </div>
                <div class="col-md-4 mb-3" id="carAllowanceDiv" style="display:none;">
                    <label class="form-label">Car Allowance (£)</label>
                    <input type="number" class="form-control" name="car_allowance" step="0.01">
                </div>
                <div class="col-md-4 mb-3" id="bonusDiv" style="display:none;">
                    <label class="form-label">Monthly Bonus (£)</label>
                    <input type="number" class="form-control" name="monthly_bonus" step="0.01">
                </div>
                <div class="col-md-4 mb-3" id="startDateDiv" style="display:none;">
                    <label class="form-label">Manager Start Date</label>
                    <input type="date" class="form-control" name="manager_start_date">
                </div>
            </div>

            <!-- Next-of-Kin Info -->
            <h4>Next of Kin Information</h4>
            <div class="row">
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
            </div>

            <button type="submit" class="btn btn-primary">Add Staff Member</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
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