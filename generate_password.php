<?php
// generate_password.php
// À exécuter une seule fois pour générer le hash du mot de passe admin
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Mot de passe 'admin123' hashé : " . $hashed_password;
?>