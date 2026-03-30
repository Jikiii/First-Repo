<?php
class Database {
    private $host = "localhost";
    private $db_name = "midterm_pit";
    private $username = "root";
    private $password = "twindaveace";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                   $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

function formatDate($dateString) {
    if (!$dateString || $dateString == '0000-00-00') {
        return 'N/A';
    }
    $timestamp = strtotime($dateString);
    return date('F j, Y', $timestamp);
}

function formatMoney($amount) {
    if ($amount === null || $amount === '') {
        return '£0.00';
    }
    return '£' . number_format((float)$amount, 2);
}
?>