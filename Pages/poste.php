<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="..\css\style1.css">
    <title>Poste</title>
</head>
<body>
    <header>
    <h1>Gestion des Postes</h1>
</header>

<nav>
    <a href="Dashboard.html">Dashboard</a>
    <a href="GestionE.html">Employés</a>
    <a href="poste.html">Postes</a>
    <a href="contract.html">Contrats</a>
    <a href="paie.html">Paie</a>
    <a href="logout.html">Déconnexion</a>
</nav>

<div class="container">

    <div class="card">
        <h2>Ajouter un Poste</h2>
        <form method="POST" action="ajouter_poste.php">
            <label>Libellé du poste</label>
            <input type="text" name="libelle" required>

            <label>Département</label>
            <select name="Département" required>
            <option value="">--Sélectionner--</option>
            <option value="DSI">DSI</option>
            <option value="RH">RH</option>
            <option value="Marketing">Marketing</option>
        </select>

            <label>Salaire de base</label>
            <input type="number" name="salairebase" required>

            <button type="submit">Ajouter</button>
        </form>
    </div>

    <div class="card">
        <h2>Liste des Postes</h2>
        <table>
            <tr>
                <th>Libellé</th>
                <th>Département</th>
                <th>Salaire</th>
                <th>Actions</th>
            </tr>
            <tr>
                <td>Développeur</td>
                <td>Informatique</td>
                <td>400000</td>
                <td>
                    <a href="#">Modifier</a> |
                    <a href="#">Supprimer</a>
                </td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>