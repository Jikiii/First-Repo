<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamHome - Property Management System</title>
    <link rel="stylesheet" href="/dreamhome/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dreamhome/index.php">🏠 DreamHome</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/dreamhome/pages/branches/index.php">Branches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dreamhome/pages/staff/index.php">Staff</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dreamhome/pages/properties/index.php">Properties</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dreamhome/pages/renters/index.php">Renters</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dreamhome/pages/leases/index.php">Leases</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dreamhome/pages/inspections/index.php">Inspections</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">