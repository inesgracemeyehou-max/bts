<?php
// login_process.php
session_start();
require_once 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Requête pour vérifier l'utilisateur
    $query = "SELECT u.*, e.nom, e.prenom, e.matricule 
              FROM utilisateur u 
              INNER JOIN employee e ON u.matricule = e.matricule 
              WHERE u.login = :email AND u.actif = 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Vérification du mot de passe (si vous utilisez password_hash)
        if (password_verify($password, $user['motdepasse'])) {
            // Stocker les informations en session
            $_SESSION['user_id'] = $user['idutilisateur'];
            $_SESSION['matricule'] = $user['matricule'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['email'] = $user['login'];
            $_SESSION['role'] = $user['role'];
            
            // Mettre à jour la dernière connexion
            $updateQuery = "UPDATE utilisateur SET derniereconnexion = NOW() WHERE idutilisateur = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':id', $user['idutilisateur']);
            $updateStmt->execute();
            
            // Redirection selon le rôle
            switch($user['role']) {
                case 'Admin':
                    header("Location: Pages/DashboardAdmin.php");
                    break;
                case 'RH':
                    header("Location: Pages/DashboardRH.php");
                    break;
                case 'Manager':
                    header("Location: Pages/DashboardManager.php");
                    break;
                case 'Employé':
                    header("Location: Pages/DashboardEmploye.php");
                    break;
                default:
                    header("Location: index.php?error=role_invalide");
            }
            exit();
        } else {
            header("Location: index.php?error=password_incorrect");
        }
    } else {
        header("Location: index.php?error=user_not_found");
    }
} else {
    header("Location: index.php");
}
?>