<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="..\css\style1.css">
    <title>Contrats</title>
</head>
<body>
    
<header>
    <h1>Gestion des Contrats des Employés</h1>
</header>

<nav>
    <a href="Dashboard.html">Dashboard admin</a>
</nav>

<div class="container">

    <!-- FORMULAIRE AJOUT CONTRAT -->
    <div class="card">
        <h2>Ajouter un Contrat</h2>
        <form method="POST" action="ajouter_contrat.html">
            <label>Matricule Employé</label>
            <input type="number" name="matricule" required>

            <label>Type de Contrat</label>
            <select name="typecontrat" required>
                <option value="">--Sélectionner--</option>
                <option value="CDI">CDI</option>
                <option value="CDD">CDD</option>
                <option value="Stage">Stage</option>
            </select>

            <label>Date de début</label>
            <input type="date" name="datedebut" required>

            <label>Date de fin</label>
            <input type="date" name="datefin">

            <label>Salaire</label>
            <input type="number" name="salaire" step="0.01" required>

            <button type="submit">Ajouter le contrat</button>
        </form>
    </div>

    <!-- TABLEAU CONTRATS EXISTANTS -->
    <div class="card">
        <h2>Liste des Contrats</h2>
        <table>
            <tr>
                <th>Matricule</th>
                <th>Type de contrat</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Salaire</th>
                <th>Actions</th>
            </tr>
            <!-- Exemple statique, à remplacer  -->
            <tr>
                <td>  </td>
                <td>  </td>
                <td>  </td>
                <td>  </td>
                <td>  </td>
                <td>
                    <a href="modifier_contrat">Modifier</a> |
                    <a href="supprimer_contrat">Supprimer</a>
                </td>
            </tr>
        </table>
    </div>
 </div>
</body>
</html>