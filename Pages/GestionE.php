<?php
// Pages/GestionE.php
session_start();
require_once '../config/auth.php';
require_once '../config/employee_functions.php';

checkAuth('Admin'); // Seul l'admin peut gérer les employés

$employeeManager = new EmployeeManager();
$postes = $employeeManager->getAllPostes();

// Traitement de l'ajout d'employé
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $result = $employeeManager->addEmployee($_POST);
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    }
}

// Recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$employees = $employeeManager->getAllEmployees($search);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style1.css">
    <title>Gestion des employés</title>
    <style>
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-box input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .search-box button {
            padding: 8px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-box button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn-edit, .btn-delete {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            color: white;
        }
        .btn-edit {
            background-color: #28a745;
        }
        .btn-edit:hover {
            background-color: #218838;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .btn-view {
            background-color: #17a2b8;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            color: white;
        }
        .btn-view:hover {
            background-color: #138496;
        }
    </style>
</head>
<body>
    <header>
        <h1>Gestion des employés</h1>
        <div class="user-info">
            Bienvenue, <?php echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']; ?>
            <a href="../logout.php" class="btn-deconnexion">Déconnexion</a>
        </div>
    </header>

    <nav>
        <a href="DashboardAdmin.php">Dashboard</a>
        <a href="GestionE.php" class="active">Employés</a>
        <a href="poste.php">Postes</a>
        <a href="conge.php">Congés</a>
        <a href="paie.php">Paie</a>
        <a href="contract.php">Contrats</a>
    </nav>

    <div class="container">
        <?php if($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Ajouter un employé</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="datenaissance">Date de naissance</label>
                        <input type="date" id="datenaissance" name="datenaissance">
                    </div>
                    <div class="form-group">
                        <label for="datembauche">Date d'embauche *</label>
                        <input type="date" id="datembauche" name="datembauche" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="idposte">Poste *</label>
                        <select id="idposte" name="idposte" required>
                            <option value="">Sélectionnez un poste</option>
                            <?php foreach($postes as $poste): ?>
                                <option value="<?php echo $poste['idposte']; ?>">
                                    <?php echo htmlspecialchars($poste['libelle']); ?> - 
                                    <?php echo htmlspecialchars($poste['departement']); ?> 
                                    (<?php echo number_format($poste['salairebase'], 0, ',', ' '); ?> FCFA)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <textarea id="adresse" name="adresse" rows="3"></textarea>
                </div>

                <button type="submit" class="btn-submit">Ajouter l'employé</button>
            </form>
        </div>

        <div class="card">
            <h2>Liste des employés</h2>
            
            <form method="GET" action="" class="search-box">
                <input type="text" name="search" placeholder="Rechercher par nom, prénom ou email..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Rechercher</button>
                <?php if($search): ?>
                    <a href="GestionE.php" class="btn-clear">Effacer</a>
                <?php endif; ?>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Poste</th>
                        <th>Département</th>
                        <th>Date d'embauche</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($employees) > 0): ?>
                        <?php foreach($employees as $emp): ?>
                        <tr>
                            <td><?php echo $emp['matricule']; ?></td>
                            <td><?php echo htmlspecialchars($emp['nom']); ?></td>
                            <td><?php echo htmlspecialchars($emp['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($emp['email']); ?></td>
                            <td><?php echo htmlspecialchars($emp['telephone'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($emp['poste_libelle']); ?></td>
                            <td><?php echo htmlspecialchars($emp['departement']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($emp['datembauche'])); ?></td>
                            <td class="action-buttons">
                                <a href="view_employee.php?id=<?php echo $emp['matricule']; ?>" class="btn-view">Voir</a>
                                <a href="edit_employee.php?id=<?php echo $emp['matricule']; ?>" class="btn-edit">Modifier</a>
                                <a href="delete_employee.php?id=<?php echo $emp['matricule']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet employé ?')">Désactiver</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center;">Aucun employé trouvé</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>