<?php
// Pages/resilier_contrat.php
session_start();
require_once '../config/auth.php';
require_once '../config/contract_functions.php';

checkAuth('Admin');

if(isset($_GET['id'])) {
    $contratManager = new ContratManager();
    $result = $contratManager->resiliateContrat(intval($_GET['id']));
    
    if($result['success']) {
        header("Location: contract.php?success=" . urlencode($result['message']));
    } else {
        header("Location: contract.php?error=" . urlencode($result['message']));
    }
} else {
    header("Location: contract.php?error=ID de contrat manquant");
}
exit();
?>