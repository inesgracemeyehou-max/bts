-- admin_default.sql
-- Exécutez ce script une seule fois pour créer l'admin par défaut

-- Note: Assurez-vous d'avoir d'abord inséré un poste pour l'admin
INSERT INTO `poste` (`libelle`, `departement`, `salairebase`, `description`) VALUES
('Directeur RH', 'DIRECTION', 5000.00, 'Administrateur système RH');

-- Insérer l'employé admin (mot de passe hashé: admin123)
INSERT INTO `employee` (`matricule`, `idposte`, `nom`, `prenom`, `email`, `datembauche`, `statut`) VALUES
(1, 1, 'Admin', 'System', 'admin@softsec.com', '2024-01-01', 'Actif');

-- Insérer l'utilisateur admin avec mot de passe hashé (admin123)
INSERT INTO `utilisateur` (`matricule`, `login`, `motdepasse`, `role`, `actif`) VALUES
(1, 'admin@softsec.com', '$2y$10$YourHashedPasswordHere', 'Admin', 1);

-- Mettre à jour l'employé avec l'idutilisateur
UPDATE `employee` SET `idutilisateur` = 1 WHERE `matricule` = 1;