<?php 
include("config.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_GET['action']) && isset($_GET['id'])){
    $action = $_GET['action'];
    $id = $_GET['id'];
    
    if($action == 'refuse'){
        $sql = 'DELETE FROM postule WHERE id='.$id.'';
        $result = mysqli_query($conDb, $sql);
        header("location:demandes.php");
        exit();
    }
    elseif($action == 'accept') {
        // 1. Validate and sanitize the ID
        $id = intval($id);
        if($id <= 0) {
            die("Invalid application ID");
        }

        // 2. Check if the application exists
        $sql = "SELECT * FROM postule WHERE id = $id";
        $result = mysqli_query($conDb, $sql);
        
        if(!$result) {
            die("Database query failed: " . mysqli_error($conDb));
        }
        
        if(mysqli_num_rows($result) == 0) {
            die("No application found with ID: $id");
        }
        
        $application = mysqli_fetch_assoc($result);
        
        // 3. Verify all required fields exist
        $required_fields = [
            'prenom', 'nom', 'cin', 'email', 'telephone', 'sexe', 
            'diplome', 'etablissement', 'mot_de_pass', 'type_id'
        ];
        
        foreach($required_fields as $field) {
            if(!isset($application[$field])) {
                die("Missing required field in application: $field");
            }
        }
        
        // 4. First check if the type_id exists in type_stage table
        $type_id = intval($application['type_id']);
        $check_type = mysqli_query($conDb, "SELECT id FROM type_stage WHERE id = $type_id");
        
        if(mysqli_num_rows($check_type) == 0) {
            die("Invalid type_id: $type_id. This type of internship doesn't exist.");
        }
        


  



        // 5. Prepare data for insertion (with proper escaping)
        $insert_data = [
            'prenom' => mysqli_real_escape_string($conDb, trim($application['prenom'])),
            'nom' => mysqli_real_escape_string($conDb, trim($application['nom'])),
            'cin' => mysqli_real_escape_string($conDb, trim($application['cin'])),
            'email' => mysqli_real_escape_string($conDb, trim($application['email'])),
            'telephone' => mysqli_real_escape_string($conDb, trim($application['telephone'])),
            'sexe' => mysqli_real_escape_string($conDb, $application['sexe']),
            'diplome' => mysqli_real_escape_string($conDb, trim($application['diplome'])),
            'etablissement' => mysqli_real_escape_string($conDb, trim($application['etablissement'])),
            'mot_de_passe' => password_hash($application['mot_de_pass'], PASSWORD_DEFAULT),
            'image_url' => mysqli_real_escape_string($conDb, $application['img_url']),
            'type_id' => $type_id,
        ];
        
        // 6. Build and execute the INSERT query
        $columns = implode(", ", array_keys($insert_data));
        $values = "'" . implode("', '", array_values($insert_data)) . "'";
        $insert_query = "INSERT INTO stagiaire ($columns) VALUES ($values)";
        $postule_id = mysqli_insert_id($conDb);

        // Récupérer l'id du type 'stagiaire'
        $sql_ty = 'SELECT id FROM type_user WHERE nom="stagiaire"';
        $resultat_type = mysqli_query($conDb, $sql_ty);
        $type_row = mysqli_fetch_assoc($resultat_type); // mysqli_fetch_assoc, pas ->fetch_assoc()
        $type_id = $type_row['id'];

        // Construire la requête INSERT proprement :
        $sqlc = "INSERT INTO utilisateurs (
            nom, prenom, cin, email, telephone, mot_de_passe, identifiant, photo, postule_id, type
        ) VALUES (
            '{$application['nom']}', 
            '{$application['prenom']}', 
            '{$application['cin']}', 
            '{$application['email']}', 
            '{$application['telephone']}', 
            '{$application['mot_de_pass']}', 
            '{$application['prenom']} {$application['nom']}', 
            '{$application['img_url']}', 
            '$postule_id', 
            '$type_id'
        )";

        // Exécuter la requête :
        mysqli_query($conDb, $sqlc);

        if(mysqli_query($conDb, $insert_query)) {
            // 7. Delete the application after successful insertion
            $delete_query = "DELETE FROM postule WHERE id = $id";
            if(!mysqli_query($conDb, $delete_query)) {
                error_log("Failed to delete application: " . mysqli_error($conDb));
            }
            
            // 8. Redirect with success message
            header("Location: demandes.php");
            exit();
        } else {
            die("Error inserting record: " . mysqli_error($conDb));
        }
    }
}
?>