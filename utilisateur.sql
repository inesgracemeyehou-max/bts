-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 24 fév. 2026 à 12:53
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bd_personnel`
--

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `idutilisateur` int(11) NOT NULL,
  `matricule` int(11) DEFAULT NULL,
  `login` varchar(50) NOT NULL,
  `motdepasse` varchar(255) NOT NULL,
  `role` enum('Admin','RH','Manager','Employé') DEFAULT 'Employé',
  `derniereconnexion` datetime DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`idutilisateur`, `matricule`, `login`, `motdepasse`, `role`, `derniereconnexion`, `actif`, `created_at`) VALUES
(1, 1, 'admin@softsec.com', '$2y$10$GD6ApVypDU4r7zRjWUzgAOIse1mnFgbB/GMwMxzbrr/aABNrI4EtK', 'Admin', '2026-02-24 12:24:24', 1, '2026-02-24 10:49:11'),
(2, 2, 'wdows280@gmail.com', '$2y$10$x/f/dNI9UK85OWGBqt1wA.2oGBdE9HAynAo0MLndQ0ilZD113w5dO', 'Employé', NULL, 1, '2026-02-24 11:24:56'),
(3, 3, 'wdows280@gmail.co', '$2y$10$AsFLk6ssWFA2VMrHrgwzS.D6ckRpv3Ik1ylIK//Tc6sE7BWAEO2vi', 'Employé', NULL, 1, '2026-02-24 11:29:14');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`idutilisateur`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `FK_correspondre_employee` (`matricule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `idutilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `FK_correspondre_employee` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
