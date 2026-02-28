<?php
// config/contract_functions.php
require_once 'db.php';

class ContratManager {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Récupérer tous les contrats avec les infos employés
    public function getAllContrats() {
        $query = "SELECT c.*, e.nom, e.prenom, e.email 
                  FROM contrat c 
                  INNER JOIN employee e ON c.matricule = e.matricule 
                  ORDER BY c.datedebut DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les contrats actifs
    public function getContratsActifs() {
        $query = "SELECT c.*, e.nom, e.prenom 
                  FROM contrat c 
                  INNER JOIN employee e ON c.matricule = e.matricule 
                  WHERE c.statut = 'Actif' 
                  ORDER BY c.datedebut DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer un contrat par son ID
    public function getContratById($idcontrat) {
        $query = "SELECT c.*, e.nom, e.prenom, e.email 
                  FROM contrat c 
                  INNER JOIN employee e ON c.matricule = e.matricule 
                  WHERE c.idcontrat = :idcontrat";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idcontrat', $idcontrat);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les contrats d'un employé
    public function getContratsByEmploye($matricule) {
        $query = "SELECT * FROM contrat 
                  WHERE matricule = :matricule 
                  ORDER BY datedebut DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ajouter un contrat
    public function addContrat($matricule, $typecontrat, $datedebut, $datefin, $salairebase) {
        try {
            // Vérifier si l'employé existe
            $checkEmploye = "SELECT matricule, nom, prenom FROM employee WHERE matricule = :matricule";
            $stmtEmploye = $this->db->prepare($checkEmploye);
            $stmtEmploye->bindParam(':matricule', $matricule);
            $stmtEmploye->execute();
            
            if($stmtEmploye->rowCount() == 0) {
                return ['success' => false, 'message' => 'Employé non trouvé'];
            }
            
            // Vérifier si l'employé a déjà un contrat actif
            $checkActif = "SELECT idcontrat FROM contrat 
                          WHERE matricule = :matricule AND statut = 'Actif'";
            $stmtActif = $this->db->prepare($checkActif);
            $stmtActif->bindParam(':matricule', $matricule);
            $stmtActif->execute();
            
            if($stmtActif->rowCount() > 0) {
                return ['success' => false, 'message' => 'Cet employé a déjà un contrat actif'];
            }
            
            // Déterminer le statut initial
            $statut = 'Actif';
            $aujourdhui = date('Y-m-d');
            
            if($datefin && $datefin < $aujourdhui) {
                $statut = 'Terminé';
            }
            
            // Insérer le contrat
            $query = "INSERT INTO contrat (matricule, typecontrat, datedebut, datefin, salairebase, statut) 
                      VALUES (:matricule, :typecontrat, :datedebut, :datefin, :salairebase, :statut)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':matricule', $matricule);
            $stmt->bindParam(':typecontrat', $typecontrat);
            $stmt->bindParam(':datedebut', $datedebut);
            $stmt->bindParam(':datefin', $datefin);
            $stmt->bindParam(':salairebase', $salairebase);
            $stmt->bindParam(':statut', $statut);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Contrat ajouté avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de l\'ajout'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Mettre à jour un contrat
    public function updateContrat($idcontrat, $typecontrat, $datedebut, $datefin, $salairebase) {
        try {
            // Déterminer le statut
            $statut = 'Actif';
            $aujourdhui = date('Y-m-d');
            
            if($datefin && $datefin < $aujourdhui) {
                $statut = 'Terminé';
            }
            
            $query = "UPDATE contrat SET 
                      typecontrat = :typecontrat,
                      datedebut = :datedebut,
                      datefin = :datefin,
                      salairebase = :salairebase,
                      statut = :statut
                      WHERE idcontrat = :idcontrat";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idcontrat', $idcontrat);
            $stmt->bindParam(':typecontrat', $typecontrat);
            $stmt->bindParam(':datedebut', $datedebut);
            $stmt->bindParam(':datefin', $datefin);
            $stmt->bindParam(':salairebase', $salairebase);
            $stmt->bindParam(':statut', $statut);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Contrat mis à jour avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Résilier un contrat
    public function resiliateContrat($idcontrat) {
        try {
            $query = "UPDATE contrat SET statut = 'Résilié', datefin = CURDATE() 
                      WHERE idcontrat = :idcontrat";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idcontrat', $idcontrat);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Contrat résilié avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la résiliation'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Supprimer un contrat
    public function deleteContrat($idcontrat) {
        try {
            $query = "DELETE FROM contrat WHERE idcontrat = :idcontrat";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idcontrat', $idcontrat);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Contrat supprimé avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la suppression'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Renouveler un contrat
    public function renouvelerContrat($idcontrat, $nouvelleDateFin) {
        try {
            $query = "UPDATE contrat SET 
                      statut = 'Renouvelé',
                      datefin = :datefin
                      WHERE idcontrat = :idcontrat";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idcontrat', $idcontrat);
            $stmt->bindParam(':datefin', $nouvelleDateFin);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Contrat renouvelé avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors du renouvellement'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Obtenir les statistiques des contrats
    public function getStatsContrats() {
        $stats = [];
        
        // Total des contrats
        $query = "SELECT COUNT(*) as total FROM contrat";
        $stmt = $this->db->query($query);
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Contrats par type
        $query = "SELECT typecontrat, COUNT(*) as total FROM contrat GROUP BY typecontrat";
        $stmt = $this->db->query($query);
        $stats['par_type'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Contrats par statut
        $query = "SELECT statut, COUNT(*) as total FROM contrat GROUP BY statut";
        $stmt = $this->db->query($query);
        $stats['par_statut'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Contrats expirant dans les 30 jours
        $query = "SELECT COUNT(*) as total FROM contrat 
                  WHERE datefin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                  AND statut = 'Actif'";
        $stmt = $this->db->query($query);
        $stats['expirant_bientot'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return $stats;
    }
}
?>