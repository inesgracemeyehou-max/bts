<?php
// Pages/delete_employee.php
session_start();
require_once '../config/auth.php';
require_once '../config/employee_functions.php';

checkAuth('Admin');

if(isset($_GET['id'])) {
    $employeeManager = new EmployeeManager();
    $result = $employeeManager->deleteEmployee($_GET['id']);
    
    if($result['success']) {
        header("Location: GestionE.php?message=deleted");
    } else {
        header("Location: GestionE.php?error=delete_failed");
    }
} else {
    header("Location: GestionE.php");
}
exit();
?>