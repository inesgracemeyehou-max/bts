<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="..\css\style1.css">
    <title>Employés</title>
</head>
<body>
    
    <header>
    <h1>Gestion des employés</h1>
</header>

<nav>
    <a href="Dashboard.html">Dashboard admin</a>
</nav>

<div class="container">
    <div class="card">
        <h2>Ajouter un employé</h2>
        <form>
            <label>Nom</label>
            <input type="text">

            <label>Poste</label>
            <input type="text">

            <label>Salaire</label>
            <input type="number">

            <button>Ajouter</button>
        </form>
    </div>

    <div class="card">
        <h2>Liste des employés</h2>
        <input type="search"> 
        <button> Rechercher </button>
        <table>
            <tr>
                <th>Nom</th>
                <th>Poste</th>
                <th>Salaire</th>
            </tr>
            <tr>
                <td>  </td>
                <td>  </td>
                <td>  </td>
            </tr>
        </table>
    </div>
</div>

    
   
</body>
</html>