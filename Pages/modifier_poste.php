<?php
// Pages/modifier_poste.php
session_start();
require_once '../config/auth.php';
require_once '../config/post_functions.php';

checkAuth('Admin');

$posteManager = new PosteManager();

// Récupérer l'ID du poste
$idposte = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($idposte <= 0) {
    header("Location: poste.php?error=ID de poste invalide");
    exit();
}

// Récupérer les données du poste
$poste = $posteManager->getPosteById($idposte);

if(!$poste) {
    header("Location: poste.php?error=Poste non trouvé");
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $result = $posteManager->updatePoste($idposte, $libelle, $departement, $salairebase, $description);
        
        if($result['success']) {
            header("Location: poste.php?success=" . urlencode($result['message']));
            exit();
        } else {
            $error = $result['message'];
        }
    } else {
        $error = implode(", ", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style1.css">
    <title>Modifier un poste</title>
    <style>
        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-retour {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
        .btn-retour:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <header>
        <h1>Modifier un poste</h1>
        <div class="user-info">
            <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?>
            <a href="../logout.php" class="btn-deconnexion">Déconnexion</a>
        </div>
    </header>

    <nav>
        <a href="DashboardAdmin.php">Dashboard</a>
        <a href="GestionE.php">Employés</a>
        <a href="poste.php">Postes</a>
        <a href="contract.php">Contrats</a>
        <a href="conge.php">Congés</a>
        <a href="paie.php">Paie</a>
    </nav>

    <div class="container">
        <div class="card">
            <h2>Modifier le poste : <?php echo htmlspecialchars($poste['libelle']); ?></h2>
            
            <?php if(isset($error)): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label>Libellé du poste</label>
                <input type="text" name="libelle" value="<?php echo htmlspecialchars($poste['libelle']); ?>" required>

                <label>Département</label>
                <select name="departement" required>
                    <option value="">--Sélectionner--</option>
                    <option value="DSI" <?php echo $poste['departement'] == 'DSI' ? 'selected' : ''; ?>>DSI</option>
                    <option value="RH" <?php echo $poste['departement'] == 'RH' ? 'selected' : ''; ?>>RH</option>
                    <option value="MARKETING" <?php echo $poste['departement'] == 'MARKETING' ? 'selected' : ''; ?>>Marketing</option>
                    <option value="COMMERCIAL" <?php echo $poste['departement'] == 'COMMERCIAL' ? 'selected' : ''; ?>>Commercial</option>
                    <option value="ADMINISTRATION" <?php echo $poste['departement'] == 'ADMINISTRATION' ? 'selected' : ''; ?>>Administration</option>
                    <option value="DIRECTION" <?php echo $poste['departement'] == 'DIRECTION' ? 'selected' : ''; ?>>Direction</option>
                </select>

                <label>Salaire de base (FCFA)</label>
                <input type="number" name="salairebase" min="0" step="1000" 
                       value="<?php echo $poste['salairebase']; ?>" required>

                <label>Description (optionnelle)</label>
                <textarea name="description" rows="3"><?php echo htmlspecialchars($poste['description'] ?? ''); ?></textarea>

                <button type="submit">Mettre à jour</button>
                <a href="poste.php" class="btn-retour">Annuler</a>
            </form>
        </div>
    </div>
</body>
</html>