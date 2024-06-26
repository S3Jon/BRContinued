<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class User
{  
    private $conn;
    private $table_name = "users";

    // Propiedades del usuario
    public $user_id;
    public $username;
    public $email;
    public $password;
    public $role;

    //Constructor to connect to the database
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    //Method to create a new user in the database
    public function createUser()
    {
        try {
            //Check if username or email already exist
            if ($this->isUsernameExists($this->username) || $this->isEmailExists($this->email)) {            
                return false;
            }
            
            $query = "INSERT INTO " . $this->table_name . " (username, email, password, role, profile_image) VALUES (:username, :email, :password, :role, :profile_image)";
            
            // Preparamos la sentencia SQL para insertar los datos
            $stmt = $this->conn->prepare($query);
            
            // Sanitizar los datos
            $this->username = htmlspecialchars(strip_tags($this->username));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->password = htmlspecialchars(strip_tags($this->password));
            $this->role = htmlspecialchars(strip_tags($this->role));

			//Default profile picture
			$profile_image = 'public/img/user_placeholder.png';
            
            // Vincular los datos
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':email', $this->email);
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':role', $this->role);
			$stmt->bindParam(':profile_image', $profile_image);

            // Ejecutar la consulta
            if($stmt->execute()) {
				$user_id = $this->conn->lastInsertId();
                return $user_id;
            }
            
            return false;
            
        } catch (PDOException $e) {
            echo "Error al crear usuario: " . $e->getMessage();
            die();
        }
    }

    //Method to obtain a user's information by user_id
    public function readByUserId($user_id)
    {
        try {
            //Prepare the query to get a user by their ID
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE id_user = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            //Get next row of execution results as associative array
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener usuario por ID: " . $e->getMessage();
            die();
        }
    }

    //Method to obtain user information by username or email (identifier)
    public function readByUsernameOrEmail($identifier)
    {
        try {
            //Prepare the query to obtain a user by their username or email
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :identifier OR email = :identifier");
            $stmt->bindParam(':identifier', $identifier);
            $stmt->execute();

            //Get next row of execution results as associative array
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener usuario por nombre de usuario o email: " . $e->getMessage();
            die();
        }
    }

    //Method to get all users
    public function readAll()
    {
        try {
            //Prepare the query to obtain a user by their username or email
            $stmt = $this->conn->prepare("SELECT * FROM users");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener todos los usuarios: " . $e->getMessage();
            die();
        }
    }

    //Method to update a user's information by user_id
    //Default to null for the user to give it a new value
    public function updateUser($user_id, $username = null, $email = null, $password = null, $role = null)
    {
        try {
            //Check if the username or email already exists for other users
            if ($this->isUsernameExistsForUpdate($user_id, $username) || $this->isEmailExistsForUpdate($user_id, $email)) {
                
                //TODO - Necesitamos caso por caso para saber si el USER o el EMAIL ya existen
                return false;
            }

			//Sanitize data
			$username = htmlspecialchars(strip_tags($username));
			$email = htmlspecialchars(strip_tags($email));
			$role = htmlspecialchars(strip_tags($role));
			$password = htmlspecialchars(strip_tags($password));

            //Prepare the query to update user information
            $stmt = $this->conn->prepare("UPDATE users SET username = COALESCE(:username, username), email = COALESCE(:email, email), password = COALESCE(:password, password), role = COALESCE(:role, role) WHERE id_user = :user_id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            echo "Error al actualizar usuario: " . $e->getMessage();
            die();
        }
    }

    //Method to delete a user from the database by its user_id
    public function deleteUser($user_id)
    {
        try {
            //Prepare the query to delete a user by their ID
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id_user = :user_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            echo "Error al eliminar usuario: " . $e->getMessage();
            die();
        }
    }

    //Method to check if a username already exists in the database
    public function isUsernameExists($username)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    //Method to check if an email already exists in the database
    public function isEmailExists($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    //Method to check if a username already exists in the database when updating
    public function isUsernameExistsForUpdate($user_id, $username)
    {
        //TODO - Diria que es el mismo que el de arriba isUsernameExists. Lo revisamos
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username AND id_user != :user_id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    //Method to check if an email already exists in the database when updating
    public function isEmailExistsForUpdate($user_id, $email)
    {
        //TODO - Diria que es el mismo que el de arriba isEmailExists. Lo revisamos
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email AND id_user != :user_id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

	public function getUserNameById($user_id)
	{
		try {
			$stmt = $this->conn->prepare("SELECT username FROM users WHERE id_user = :user_id");
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			return implode($stmt->fetch(PDO::FETCH_ASSOC));
		} catch (PDOException $e) {
			echo "Error al obtener el nombre de usuario por ID: " . $e->getMessage();
			die();
		}
	}

	public function userExists($user_id)
	{
		try {
			$stmt = $this->conn->prepare("SELECT * FROM users WHERE id_user = :user_id");
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			echo "Error al comprobar si el usuario existe: " . $e->getMessage();
			die();
		}
	}

	public function searchUserByName($username)
	{
		try {
			$query = "
				SELECT u.id_user, u.username, u.profile_image, 
					   COALESCE(f.followersNum, 0) AS followersNum,
					   COALESCE(l.publicListsCount, 0) AS publicListsCount
				FROM users u
				LEFT JOIN (
					SELECT id_followed, COUNT(*) AS followersNum
					FROM followers
					GROUP BY id_followed
				) AS f ON u.id_user = f.id_followed
				LEFT JOIN (
					SELECT id_user, COUNT(*) AS publicListsCount
					FROM lists
					WHERE visibility = 'public' AND type IS NULL
					GROUP BY id_user
				) AS l ON u.id_user = l.id_user
				WHERE u.username LIKE :search
				ORDER BY followersNum DESC";
			
			$stmt = $this->conn->prepare($query);
			$searchParam = "%" . $username . "%";
			$stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al buscar usuario por nombre: " . $e->getMessage();
			die();
		}
	}

	public function updateUserProfileImage($user_id, $profile_image)
	{
		try {
			$stmt = $this->conn->prepare("UPDATE users SET profile_image = :profile_image WHERE id_user = :user_id");
			$stmt->bindParam(':profile_image', $profile_image);
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
			echo "Error al actualizar la imagen de perfil del usuario: " . $e->getMessage();
			die();
		}
	}
}
?>