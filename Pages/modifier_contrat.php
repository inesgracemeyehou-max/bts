<?php
// Pages/modifier_contrat.php
session_start();
require_once '../config/auth.php';
require_once '../config/contract_functions.php';

checkAuth('Admin');

$contratManager = new ContratManager();

$idcontrat = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($idcontrat <= 0) {
    header("Location: contract.php?error=ID de contrat invalide");
    exit();
}

$contrat = $contratManager->getContratById($idcontrat);

if(!$contrat) {
    header("Location: contract.php?error=Contrat non trouvé");
    exit();
}

// Récupérer la liste des employés
$db = (new Database())->getConnection();
$queryEmployes = "SELECT matricule, nom, prenom FROM employee WHERE statut = 'Actif' ORDER BY nom";
$stmtEmployes = $db->prepare($queryEmployes);
$stmtEmployes->execute();
$employes = $stmtEmployes->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $typecontrat = $_POST['typecontrat'];
    $datedebut = $_POST['datedebut'];
    $datefin = !empty($_POST['datefin']) ? $_POST['datefin'] : null;
    $salairebase = floatval($_POST['salairebase']);
    
    // Validation
    $errors = [];
    
    if(empty($typecontrat)) {
        $errors[] = "Le type de contrat est requis";
    }
    
    if(empty($datedebut)) {
        $errors[] = "La date de début est requise";
    }
    
    if($salairebase <= 0) {
        $errors[] = "Le salaire doit être supérieur à 0";
    }
    
    if($datefin && strtotime($datefin) < strtotime($datedebut)) {
        $errors[] = "La date de fin doit être postérieure à la date de début";
    }
    
    if(empty($errors)) {
        $result = $contratManager->updateContrat($idcontrat, $typecontrat, $datedebut, $datefin, $salairebase);
        
        if($result['success']) {
            header("Location: contract.php?success=" . urlencode($result['message']));
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
    <title>Modifier un contrat</title>
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
        .info-employe {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Modifier un contrat</h1>
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
            <h2>Modifier le contrat</h2>
            
            <div class="info-employe">
                <strong>Employé:</strong> <?php echo htmlspecialchars($contrat['prenom'] . ' ' . $contrat['nom']); ?><br>
                <strong>Matricule:</strong> <?php echo $contrat['matricule']; ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($contrat['email']); ?>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label>Type de Contrat</label>
                <select name="typecontrat" required>
                    <option value="">--Sélectionner--</option>
                    <option value="CDI" <?php echo $contrat['typecontrat'] == 'CDI' ? 'selected' : ''; ?>>CDI</option>
                    <option value="CDD" <?php echo $contrat['typecontrat'] == 'CDD' ? 'selected' : ''; ?>>CDD</option>
                    <option value="Stage" <?php echo $contrat['typecontrat'] == 'Stage' ? 'selected' : ''; ?>>Stage</option>
                    <option value="Prestation" <?php echo $contrat['typecontrat'] == 'Prestation' ? 'selected' : ''; ?>>Prestation</option>
                    <option value="Alternance" <?php echo $contrat['typecontrat'] == 'Alternance' ? 'selected' : ''; ?>>Alternance</option>
                </select>

                <label>Date de début</label>
                <input type="date" name="datedebut" value="<?php echo $contrat['datedebut']; ?>" required>

                <label>Date de fin</label>
                <input type="date" name="datefin" value="<?php echo $contrat['datefin'] ?? ''; ?>">

                <label>Salaire de base (FCFA)</label>
                <input type="number" name="salairebase" step="1000" min="0" 
                       value="<?php echo $contrat['salairebase']; ?>" required>

                <button type="submit">Mettre à jour</button>
                <a href="contract.php" class="btn-retour">Annuler</a>
            </form>
        </div>
    </div>
</body>
</html>