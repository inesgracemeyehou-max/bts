<?php
// config/post_functions.php
require_once 'db.php';

class PosteManager {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Récupérer tous les postes
    public function getAllPostes() {
        $query = "SELECT * FROM poste ORDER BY libelle ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ajouter un poste
    public function addPoste($libelle, $departement, $salairebase, $description = '') {
        try {
            // Vérifier si le poste existe déjà
            $checkQuery = "SELECT idposte FROM poste WHERE libelle = :libelle AND departement = :departement";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':libelle', $libelle);
            $checkStmt->bindParam(':departement', $departement);
            $checkStmt->execute();
            
            if($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Ce poste existe déjà dans ce département'];
            }
            
            // Insérer le nouveau poste
            $query = "INSERT INTO poste (libelle, departement, salairebase, description) 
                      VALUES (:libelle, :departement, :salairebase, :description)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':libelle', $libelle);
            $stmt->bindParam(':departement', $departement);
            $stmt->bindParam(':salairebase', $salairebase);
            $stmt->bindParam(':description', $description);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Poste ajouté avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de l\'ajout'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Récupérer un poste par son ID
    public function getPosteById($idposte) {
        $query = "SELECT * FROM poste WHERE idposte = :idposte";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idposte', $idposte);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mettre à jour un poste
    public function updatePoste($idposte, $libelle, $departement, $salairebase, $description = '') {
        try {
            $query = "UPDATE poste SET 
                      libelle = :libelle,
                      departement = :departement,
                      salairebase = :salairebase,
                      description = :description
                      WHERE idposte = :idposte";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idposte', $idposte);
            $stmt->bindParam(':libelle', $libelle);
            $stmt->bindParam(':departement', $departement);
            $stmt->bindParam(':salairebase', $salairebase);
            $stmt->bindParam(':description', $description);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Poste mis à jour avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Supprimer un poste
    public function deletePoste($idposte) {
        try {
            // Vérifier si des employés sont affectés à ce poste
            $checkQuery = "SELECT COUNT(*) as total FROM employee WHERE idposte = :idposte";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':idposte', $idposte);
            $checkStmt->execute();
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if($result['total'] > 0) {
                return ['success' => false, 'message' => 'Impossible de supprimer : ce poste est attribué à ' . $result['total'] . ' employé(s)'];
            }
            
            // Supprimer le poste
            $query = "DELETE FROM poste WHERE idposte = :idposte";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':idposte', $idposte);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Poste supprimé avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la suppression'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Obtenir le nombre d'employés par poste
    public function getEmployesCountByPoste($idposte) {
        $query = "SELECT COUNT(*) as total FROM employee WHERE idposte = :idposte AND statut = 'Actif'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idposte', $idposte);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>