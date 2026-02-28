<?php
// Pages/ajouter_contrat.php
session_start();
require_once '../config/auth.php';
require_once '../config/contract_functions.php';

checkAuth('Admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contratManager = new ContratManager();
    
    $matricule = intval($_POST['matricule']);
    $typecontrat = $_POST['typecontrat'];
    $datedebut = $_POST['datedebut'];
    $datefin = !empty($_POST['datefin']) ? $_POST['datefin'] : null;
    $salairebase = floatval($_POST['salairebase']);
    
    // Validation
    $errors = [];
    
    if($matricule <= 0) {
        $errors[] = "Employé invalide";
    }
    
    if(empty($typecontrat)) {
        $errors[] = "Le type de contrat est requis";
    }
    
    if(empty($datedebut)) {
        $errors[] = "La date de début est requise";
    }
    
    if($salairebase <= 0) {
        $errors[] = "Le salaire doit être supérieur à 0";
    }
    
    // Validation des dates
    if($datefin && strtotime($datefin) < strtotime($datedebut)) {
        $errors[] = "La date de fin doit être postérieure à la date de début";
    }
    
    if(empty($errors)) {
        $result = $contratManager->addContrat($matricule, $typecontrat, $datedebut, $datefin, $salairebase);
        
        if($result['success']) {
            header("Location: contract.php?success=" . urlencode($result['message']));
            exit();
        } else {
            header("Location: contract.php?error=" . urlencode($result['message']));
            exit();
        }
    } else {
        $errorMessage = implode(", ", $errors);
        header("Location: contract.php?error=" . urlencode($errorMessage));
        exit();
    }
} else {
    header("Location: contract.php");
    exit();
}
?>