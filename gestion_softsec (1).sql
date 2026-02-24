-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 24 fév. 2026 à 10:01
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_softsec`
--

-- --------------------------------------------------------

--
-- Structure de la table `conge`
--

DROP TABLE IF EXISTS `conge`;
CREATE TABLE IF NOT EXISTS `conge` (
  `idconge` int NOT NULL,
  `matricule` int NOT NULL,
  `datedebut` datetime DEFAULT NULL,
  `datefin` datetime DEFAULT NULL,
  `motif` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `etat` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`idconge`),
  KEY `FK_faire` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contrat`
--

DROP TABLE IF EXISTS `contrat`;
CREATE TABLE IF NOT EXISTS `contrat` (
  `idcontrat` int NOT NULL,
  `matricule` int NOT NULL,
  `typecontrat` enum('CDI','CDD','Stage','') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `datedebut` datetime DEFAULT NULL,
  `datefin` datetime DEFAULT NULL,
  `salaire` float DEFAULT NULL,
  PRIMARY KEY (`idcontrat`),
  KEY `FK_avoir` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `matricule` int NOT NULL,
  `idposte` int NOT NULL,
  `idutilisateur` int DEFAULT NULL,
  `nom` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prenom` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `datenaissance` datetime DEFAULT NULL,
  `adresse` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`matricule`),
  KEY `FK_occuper` (`idposte`),
  KEY `FK_correspondre` (`idutilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paie`
--

DROP TABLE IF EXISTS `paie`;
CREATE TABLE IF NOT EXISTS `paie` (
  `idpaie` int NOT NULL,
  `matricule` int NOT NULL,
  `mois` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `annee` int DEFAULT NULL,
  `montantbrut` float DEFAULT NULL,
  `montantnet` float DEFAULT NULL,
  `prime` float DEFAULT NULL,
  `paiementeffectue` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idpaie`),
  KEY `FK_recevoir` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `poste`
--

DROP TABLE IF EXISTS `poste`;
CREATE TABLE IF NOT EXISTS `poste` (
  `idposte` int NOT NULL,
  `libelle` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `departement` enum('DSI','RH','MARKETING','') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `salairebase` float DEFAULT NULL,
  PRIMARY KEY (`idposte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `idutilisateur` int NOT NULL,
  `matricule` int NOT NULL,
  `login` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `motdepasse` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`idutilisateur`),
  KEY `FK_correspondre_employee` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `conge`
--
ALTER TABLE `conge`
  ADD CONSTRAINT `FK_faire` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`);

--
-- Contraintes pour la table `contrat`
--
ALTER TABLE `contrat`
  ADD CONSTRAINT `FK_avoir` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`);

--
-- Contraintes pour la table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `FK_correspondre` FOREIGN KEY (`idutilisateur`) REFERENCES `utilisateur` (`idutilisateur`),
  ADD CONSTRAINT `FK_occuper` FOREIGN KEY (`idposte`) REFERENCES `poste` (`idposte`);

--
-- Contraintes pour la table `paie`
--
ALTER TABLE `paie`
  ADD CONSTRAINT `FK_recevoir` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `FK_correspondre_employee` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
