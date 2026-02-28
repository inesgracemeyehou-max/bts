<?php
// Pages/supprimer_poste.php
session_start();
require_once '../config/auth.php';
require_once '../config/post_functions.php';

checkAuth('Admin');

if(isset($_GET['id'])) {
    $posteManager = new PosteManager();
    $result = $posteManager->deletePoste(intval($_GET['id']));
    
    if($result['success']) {
        header("Location: poste.php?success=" . urlencode($result['message']));
    } else {
        header("Location: poste.php?error=" . urlencode($result['message']));
    }
} else {
    header("Location: poste.php?error=ID de poste manquant");
}
exit();
?>