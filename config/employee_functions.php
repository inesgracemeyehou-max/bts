<?php
// config/employee_functions.php
require_once 'db.php';

class EmployeeManager {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Récupérer tous les employés
    public function getAllEmployees($search = '') {
        $query = "SELECT e.*, p.libelle as poste_libelle, p.departement, p.salairebase 
                  FROM employee e 
                  INNER JOIN poste p ON e.idposte = p.idposte 
                  WHERE e.statut = 'Actif'";
        
        if(!empty($search)) {
            $query .= " AND (e.nom LIKE :search OR e.prenom LIKE :search OR e.email LIKE :search)";
        }
        
        $query .= " ORDER BY e.nom ASC";
        
        $stmt = $this->db->prepare($query);
        
        if(!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bindParam(':search', $searchTerm);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer tous les postes pour le select
    public function getAllPostes() {
        $query = "SELECT idposte, libelle, departement, salairebase FROM poste ORDER BY libelle ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ajouter un employé
    public function addEmployee($data) {
        try {
            // Vérifier si l'email existe déjà
            $checkQuery = "SELECT matricule FROM employee WHERE email = :email";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':email', $data['email']);
            $checkStmt->execute();
            
            if($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }
            
            // Insérer le nouvel employé
            $query = "INSERT INTO employee (idposte, nom, prenom, datenaissance, adresse, telephone, email, datembauche, statut) 
                      VALUES (:idposte, :nom, :prenom, :datenaissance, :adresse, :telephone, :email, :datembauche, 'Actif')";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':idposte', $data['idposte']);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':prenom', $data['prenom']);
            $stmt->bindParam(':datenaissance', $data['datenaissance']);
            $stmt->bindParam(':adresse', $data['adresse']);
            $stmt->bindParam(':telephone', $data['telephone']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':datembauche', $data['datembauche']);
            
            if($stmt->execute()) {
                $matricule = $this->db->lastInsertId();
                
                // Créer automatiquement un compte utilisateur pour l'employé
                $this->createUserAccount($matricule, $data['email'], $data['nom'], $data['prenom']);
                
                return ['success' => true, 'message' => 'Employé ajouté avec succès', 'matricule' => $matricule];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de l\'ajout'];
            }
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    // Créer un compte utilisateur pour l'employé
    private function createUserAccount($matricule, $email, $nom, $prenom) {
        // Générer un mot de passe par défaut (à changer à la première connexion)
        $defaultPassword = password_hash('Password123!', PASSWORD_DEFAULT);
        
        $query = "INSERT INTO utilisateur (matricule, login, motdepasse, role, actif) 
                  VALUES (:matricule, :login, :motdepasse, 'Employé', 1)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->bindParam(':login', $email);
        $stmt->bindParam(':motdepasse', $defaultPassword);
        $stmt->execute();
        
        // Récupérer l'idutilisateur créé
        $idutilisateur = $this->db->lastInsertId();
        
        // Mettre à jour l'employé avec l'idutilisateur
        $updateQuery = "UPDATE employee SET idutilisateur = :idutilisateur WHERE matricule = :matricule";
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->bindParam(':idutilisateur', $idutilisateur);
        $updateStmt->bindParam(':matricule', $matricule);
        $updateStmt->execute();
    }
    
    // Récupérer un employé par son matricule
    public function getEmployeeById($matricule) {
        $query = "SELECT e.*, p.libelle as poste_libelle, p.departement 
                  FROM employee e 
                  INNER JOIN poste p ON e.idposte = p.idposte 
                  WHERE e.matricule = :matricule";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Mettre à jour un employé
    public function updateEmployee($matricule, $data) {
        $query = "UPDATE employee SET 
                  idposte = :idposte,
                  nom = :nom,
                  prenom = :prenom,
                  datenaissance = :datenaissance,
                  adresse = :adresse,
                  telephone = :telephone,
                  email = :email
                  WHERE matricule = :matricule";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':idposte', $data['idposte']);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':datenaissance', $data['datenaissance']);
        $stmt->bindParam(':adresse', $data['adresse']);
        $stmt->bindParam(':telephone', $data['telephone']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':matricule', $matricule);
        
        if($stmt->execute()) {
            return ['success' => true, 'message' => 'Employé mis à jour avec succès'];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
        }
    }
    
    // Supprimer un employé (désactiver)
    public function deleteEmployee($matricule) {
        $query = "UPDATE employee SET statut = 'Inactif' WHERE matricule = :matricule";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        
        if($stmt->execute()) {
            return ['success' => true, 'message' => 'Employé désactivé avec succès'];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de la désactivation'];
        }
    }
}
?>