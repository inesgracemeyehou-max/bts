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
-- Structure de la table `absence`
--

DROP TABLE IF EXISTS `absence`;
CREATE TABLE IF NOT EXISTS `absence` (
  `idabsence` int NOT NULL AUTO_INCREMENT,
  `matricule` int NOT NULL,
  `dateabsence` date NOT NULL,
  `typeabsence` enum('Congé','Maladie','Retard','Formation','Autorisation','Absence injustifiée') COLLATE utf8mb4_general_ci NOT NULL,
  `justifiee` tinyint(1) DEFAULT '1',
  `commentaire` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idabsence`),
  KEY `FK_absence_employee` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `conge`
--

DROP TABLE IF EXISTS `conge`;
CREATE TABLE IF NOT EXISTS `conge` (
  `idconge` int NOT NULL AUTO_INCREMENT,
  `matricule` int NOT NULL,
  `datedebut` date NOT NULL,
  `datefin` date NOT NULL,
  `nbjours` int GENERATED ALWAYS AS (DATEDIFF(datefin, datedebut) + 1) STORED,
  `motif` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `typeconge` enum('Payé','Maladie','Sans solde','Maternité','Paternité','Formation') COLLATE utf8mb4_general_ci DEFAULT 'Payé',
  `etat` enum('En attente','Approuvé','Refusé','Annulé') COLLATE utf8mb4_general_ci DEFAULT 'En attente',
  `datevalidation` datetime DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`idconge`),
  KEY `FK_faire` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contrat`
--

DROP TABLE IF EXISTS `contrat`;
CREATE TABLE IF NOT EXISTS `contrat` (
  `idcontrat` int NOT NULL AUTO_INCREMENT,
  `matricule` int NOT NULL,
  `typecontrat` enum('CDI','CDD','Stage','Prestation','Alternance') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `datedebut` date NOT NULL,
  `datefin` date DEFAULT NULL,
  `salairebase` decimal(10,2) NOT NULL,
  `statut` enum('Actif','Terminé','Renouvelé','Résilié') COLLATE utf8mb4_general_ci DEFAULT 'Actif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcontrat`),
  KEY `FK_avoir` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `matricule` int NOT NULL AUTO_INCREMENT,
  `idposte` int NOT NULL,
  `idutilisateur` int DEFAULT NULL,
  `nom` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `datenaissance` date DEFAULT NULL,
  `adresse` varchar(254) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `datembauche` date NOT NULL,
  `statut` enum('Actif','Inactif','Congé longue durée','Démissionné','Retraité') COLLATE utf8mb4_general_ci DEFAULT 'Actif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`matricule`),
  UNIQUE KEY `email` (`email`),
  KEY `FK_occuper` (`idposte`),
  KEY `FK_correspondre` (`idutilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paie`
--

DROP TABLE IF EXISTS `paie`;
CREATE TABLE IF NOT EXISTS `paie` (
  `idpaie` int NOT NULL AUTO_INCREMENT,
  `matricule` int NOT NULL,
  `mois` tinyint NOT NULL,
  `annee` smallint NOT NULL,
  `salairebase` decimal(10,2) NOT NULL,
  `prime` decimal(10,2) DEFAULT '0.00',
  `deduction` decimal(10,2) DEFAULT '0.00',
  `montantbrut` decimal(10,2) GENERATED ALWAYS AS (salairebase + prime) STORED,
  `montantnet` decimal(10,2) NOT NULL,
  `datepaiement` date DEFAULT NULL,
  `paiementeffectue` tinyint(1) DEFAULT '0',
  `modepaiement` enum('Virement','Chèque','Espèces') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idpaie`),
  UNIQUE KEY `unique_paie_employee` (`matricule`,`mois`,`annee`),
  KEY `FK_recevoir` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pointage`
--

DROP TABLE IF EXISTS `pointage`;
CREATE TABLE IF NOT EXISTS `pointage` (
  `idpointage` int NOT NULL AUTO_INCREMENT,
  `matricule` int NOT NULL,
  `datepointage` date NOT NULL,
  `heurearrivee` time DEFAULT NULL,
  `heuredepart` time DEFAULT NULL,
  `heuressupplementaires` decimal(4,2) GENERATED ALWAYS AS (GREATEST(0, TIMESTAMPDIFF(HOUR, CONCAT(datepointage, ' ', heuredepart), CONCAT(datepointage, ' ', '18:00:00')) * -1)) STORED,
  `statut` enum('Présent','Absent','Retard','Départ anticipé') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`idpointage`),
  UNIQUE KEY `unique_pointage` (`matricule`,`datepointage`),
  KEY `FK_pointage_employee` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `poste`
--

DROP TABLE IF EXISTS `poste`;
CREATE TABLE IF NOT EXISTS `poste` (
  `idposte` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(254) COLLATE utf8mb4_general_ci NOT NULL,
  `departement` enum('DSI','RH','MARKETING','COMMERCIAL','ADMINISTRATION','DIRECTION') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `salairebase` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`idposte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `idutilisateur` int NOT NULL AUTO_INCREMENT,
  `matricule` int DEFAULT NULL,
  `login` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `motdepasse` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('Admin','RH','Manager','Employé') COLLATE utf8mb4_general_ci DEFAULT 'Employé',
  `derniereconnexion` datetime DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idutilisateur`),
  UNIQUE KEY `login` (`login`),
  KEY `FK_correspondre_employee` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `soldeconge`
--

CREATE TABLE IF NOT EXISTS `soldeconge` (
  `idsolde` int NOT NULL AUTO_INCREMENT,
  `matricule` int NOT NULL,
  `annee` smallint NOT NULL,
  `joursacquis` decimal(5,2) DEFAULT '25.00',
  `jourspris` decimal(5,2) DEFAULT '0.00',
  `joursrestants` decimal(5,2) GENERATED ALWAYS AS (`joursacquis` - `jourspris`) STORED,
  PRIMARY KEY (`idsolde`),
  UNIQUE KEY `unique_solde` (`matricule`,`annee`),
  KEY `FK_solde_employee` (`matricule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `absence`
--
ALTER TABLE `absence`
  ADD CONSTRAINT `FK_absence_employee` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `conge`
--
ALTER TABLE `conge`
  ADD CONSTRAINT `FK_faire` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `contrat`
--
ALTER TABLE `contrat`
  ADD CONSTRAINT `FK_avoir` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `FK_correspondre` FOREIGN KEY (`idutilisateur`) REFERENCES `utilisateur` (`idutilisateur`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_occuper` FOREIGN KEY (`idposte`) REFERENCES `poste` (`idposte`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `paie`
--
ALTER TABLE `paie`
  ADD CONSTRAINT `FK_recevoir` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `pointage`
--
ALTER TABLE `pointage`
  ADD CONSTRAINT `FK_pointage_employee` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `soldeconge`
--
ALTER TABLE `soldeconge`
  ADD CONSTRAINT `FK_solde_employee` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `FK_correspondre_employee` FOREIGN KEY (`matricule`) REFERENCES `employee` (`matricule`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;