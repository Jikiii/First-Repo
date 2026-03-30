<?php
include '../../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$branch_sql = "SELECT BranchNo, BranchName FROM Branch ORDER BY BranchName";
$branch_stmt = $db->prepare($branch_sql);
$branch_stmt->execute();
$branches = $branch_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_no = $_POST['staff_no'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $national_id = $_POST['national_id'];
    $job_title = $_POST['job_title'];
    $salary = $_POST['salary'];
    $hire_date = $_POST['hire_date'];
    $branch_no = $_POST['branch_no'];
    
    $sql = "INSERT INTO Staff (StaffNo, FName, LName, Address, Phone, Email, Gender, BirthDate, NationalID, JobTitle, Salary, HireDate, BranchNo) 
            VALUES (:staff_no, :fname, :lname, :address, :phone, :email, :gender, :birthdate, :national_id, :job_title, :salary, :hire_date, :branch_no)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':staff_no', $staff_no);
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':birthdate', $birthdate);
    $stmt->bindParam(':national_id', $national_id);
    $stmt->bindParam(':job_title', $job_title);
    $stmt->bindParam(':salary', $salary);
    $stmt->bindParam(':hire_date', $hire_date);
    $stmt->bindParam(':branch_no', $branch_no);
    
    if ($stmt->execute()) {
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
                <div class="col-md-4 mb-3">
                    <label class="form-label">Staff Number *</label>
                    <input type="text" class="form-control" name="staff_no" required maxlength="10">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" class="form-control" name="fname" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" class="form-control" name="lname" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address *</label>
                    <textarea class="form-control" name="address" rows="2" required></textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone *</label>
                    <input type="text" class="form-control" name="phone" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Gender *</label>
                    <select class="form-select" name="gender" required>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" class="form-control" name="birthdate" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">National ID *</label>
                    <input type="text" class="form-control" name="national_id" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Job Title *</label>
                    <select class="form-select" name="job_title" required>
                        <option value="Manager">Manager</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Administrator">Administrator</option>
                        <option value="Secretary">Secretary</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Salary (£) *</label>
                    <input type="number" class="form-control" name="salary" required step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Hire Date *</label>
                    <input type="date" class="form-control" name="hire_date" required>
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
            </div>
            <button type="submit" class="btn btn-primary">Add Staff Member</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>