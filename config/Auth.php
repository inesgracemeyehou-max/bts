<?php
// config/auth.php
function checkAuth($requiredRole = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }
    
    if ($requiredRole && $_SESSION['role'] !== $requiredRole) {
        // Rediriger vers le dashboard approprié si le rôle ne correspond pas
        switch($_SESSION['role']) {
            case 'Admin': header("Location: DashboardAdmin.php"); break;
            case 'RH': header("Location: DashboardRH.php"); break;
            case 'Manager': header("Location: DashboardManager.php"); break;
            case 'Employé': header("Location: DashboardEmploye.php"); break;
            default: header("Location: ../index.php");
        }
        exit();
    }
}
?>