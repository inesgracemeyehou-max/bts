<?php
// Pages/ajouter_poste.php
session_start();
require_once '../config/auth.php';
require_once '../config/post_functions.php';

checkAuth('Admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posteManager = new PosteManager();
    
    $libelle = trim($_POST['libelle']);
    $departement = $_POST['departement'];
    $salairebase = floatval($_POST['salairebase']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // Validation
    $errors = [];
    
    if(empty($libelle)) {
        $errors[] = "Le libellé est requis";
    }
    
    if(empty($departement)) {
        $errors[] = "Le département est requis";
    }
    
    if($salairebase <= 0) {
        $errors[] = "Le salaire de base doit être supérieur à 0";
    }
    
    if(empty($errors)) {
        $result = $posteManager->addPoste($libelle, $departement, $salairebase, $description);
        
        if($result['success']) {
            header("Location: poste.php?success=" . urlencode($result['message']));
            exit();
        } else {
            header("Location: poste.php?error=" . urlencode($result['message']));
            exit();
        }
    } else {
        $errorMessage = implode(", ", $errors);
        header("Location: poste.php?error=" . urlencode($errorMessage));
        exit();
    }
} else {
    header("Location: poste.php");
    exit();
}
?>