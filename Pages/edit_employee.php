<?php
// Pages/edit_employee.php
session_start();
require_once '../config/auth.php';
require_once '../config/employee_functions.php';

checkAuth('Admin');

$employeeManager = new EmployeeManager();
$postes = $employeeManager->getAllPostes();

$matricule = isset($_GET['id']) ? $_GET['id'] : 0;
$employee = $employeeManager->getEmployeeById($matricule);

if(!$employee) {
    header("Location: GestionE.php?error=not_found");
    exit();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $employeeManager->updateEmployee($matricule, $_POST);
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
        $employee = $employeeManager->getEmployeeById($matricule); // Rafraîchir les données
    } else {
        $message = $result['message'];
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style1.css">
    <title>Modifier employé</title>
    <style>
        /* Mêmes styles que GestionE.php */
    </style>
</head>
<body>
    <header>
        <h1>Modifier employé</h1>
        <div class="user-info">
            <?php echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']; ?>
            <a href="../logout.php" class="btn-deconnexion">Déconnexion</a>
        </div>
    </header>

    <nav>
        <a href="DashboardAdmin.php">Dashboard</a>
        <a href="GestionE.php">Employés</a>
        <a href="poste.php">Postes</a>
        <a href="conge.php">Congés</a>
        <a href="paie.php">Paie</a>
        <a href="contract.php">Contrats</a>
    </nav>

    <div class="container">
        <div class="card">
            <h2>Modifier les informations de <?php echo $employee['prenom'] . ' ' . $employee['nom']; ?></h2>
            
            <?php if($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($employee['nom']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($employee['prenom']); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($employee['telephone'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="datenaissance">Date de naissance</label>
                        <input type="date" id="datenaissance" name="datenaissance" value="<?php echo $employee['datenaissance']; ?>">
                    </div>
                </div>

                <div class="form-row">
    <div class="form-group">
        <label for="idposte">Poste *</label>
        <select id="idposte" name="idposte" required>
            <option value="">Sélectionnez un poste</option>
            <?php 
            foreach($postes as $poste):
                $selected = ($poste['idposte'] == $employee['idposte']) ? 'selected' : '';
                $libelle = htmlspecialchars($poste['libelle']);
                $departement = htmlspecialchars($poste['departement']);
                $salaire = number_format($poste['salairebase'], 0, ',', ' ');
            ?>
                <option value="<?= $poste['idposte'] ?>" <?= $selected ?>>
                    <?= $libelle ?> - <?= $departement ?> (<?= $salaire ?> FCFA)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <textarea id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($employee['adresse'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn-submit">Mettre à jour</button>
                <a href="GestionE.php" class="btn-cancel">Annuler</a>
            </form>
        </div>
    </div>
</body>
</html>