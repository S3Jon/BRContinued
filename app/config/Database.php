<?php

namespace config;

use PDO;
use PDOException;

class Database{
    private $host = "localhost";
    private $db_name = "BookReadersDB";
    private $username = "root";
    private $password = "";
    public $conn;

    // start the connection
    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            
            $this->conn->exec("set names utf8");
        }catch(PDOException $exception){
            echo "Error de conexion: ".$exception->getMessage();
        }

        return $this->conn;
    }

    // stop the connection
    public function closeConnection(){
        $this->conn = null;
    }
    
    // create database
    public function createDatabase(){
        try {
            $this->conn = new PDO("mysql:host=".$this->host, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "CREATE DATABASE IF NOT EXISTS " . $this->db_name;
            $this->conn->exec($sql);
            
            // If exists, close the connection and open a new one
            if ($this->conn) {
                $this->getConnection();
            }
        } catch(PDOException $e) {
            echo "Error al crear la base de datos: " . $e->getMessage();
        }
    }

    // create users table
    public function createUsersTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id_user INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                username VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('user','admin') DEFAULT 'user',
                name VARCHAR(255),
                profile_image VARCHAR(255),
                metadata JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->conn->exec($sql);
            // echo "Tabla de usuarios creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de usuarios: " . $e->getMessage();
        }
    }

    // create default admin user
    public function createDefaultAdminUser(){
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = 'admin'");
            $stmt->execute();
            $existingAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$existingAdmin){
                $username = "admin";
                $email = "admin@gmail.com";
                $password = password_hash("admin", PASSWORD_DEFAULT);

                $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'admin')");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->execute();
				$this->createDemoUserBasicLists(1);

                // echo "Usuario administrador creado exitosamente.";
            } else {
                // echo "Ya existe un usuario administrador en la base de datos.";
            }
        } catch(PDOException $e) {
            echo "Error al crear el usuario administrador: " . $e->getMessage();
        }
    }

	public function createExtraUsers(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM users");
			$stmt->execute();
			$existingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
			if (count($existingUsers) < 10) {
				$extraUsers = [
					['username' => 'Ana', 'email' => 'ana@gmail.com', 'password' => password_hash('ana', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/cervantes.png'],
					['username' => 'Luis', 'email' => 'luis@gmail.com', 'password' => password_hash('luis', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/dostoyevski.png'],
					['username' => 'Carlos', 'email' => 'carlos@gmail.com', 'password' => password_hash('carlos', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/espronceda.png'],
					['username' => 'Laura', 'email' => 'laura@gmail.com', 'password' => password_hash('laura', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/goldberg.png'],
					['username' => 'Julia', 'email' => 'julia@gmail.com', 'password' => password_hash('julia', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/holmes.png'],
					['username' => 'Diego', 'email' => 'diego@gmail.com', 'password' => password_hash('diego', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/quevedo.png'],
					['username' => 'Sofia', 'email' => 'sofia@gmail.com', 'password' => password_hash('sofia', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/tenthdoc.png'],
					['username' => 'Pedro', 'email' => 'pedro@gmail.com', 'password' => password_hash('pedro', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/trajan.png'],
					['username' => 'Juan', 'email' => 'juan@gmail.com', 'password' => password_hash('juan', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/turing.png'],
					['username' => 'Maria', 'email' => 'maria@gmail.com', 'password' => password_hash('maria', PASSWORD_DEFAULT), 'role' => 'user', 'profile_image' => 'public/img/demo_user_pic/vangogh.png']
				];
	
				$stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role, profile_image) VALUES (:username, :email, :password, :role, :profile_image)");
				foreach($extraUsers as $user){
					$stmt->bindParam(':username', $user['username']);
					$stmt->bindParam(':email', $user['email']);
					$stmt->bindParam(':password', $user['password']);
					$stmt->bindParam(':role', $user['role']);
					$stmt->bindParam(':profile_image', $user['profile_image']);
					$stmt->execute();
					$id_user = $this->conn->lastInsertId();
					$this->createDemoUserBasicLists($id_user);
				}
			} else {
				// echo "Ya existen usuarios extra en la base de datos.";
			}
		} catch(PDOException $e) {
			echo "Error al crear los usuarios extra: " . $e->getMessage();
		}
	}

	// demo user basic lists
	private function createDemoUserBasicLists($id_user) {
		$visDB = 'public';
		$typeDB = ['favorite', 'read', 'want_to_read', 'reading', 'dropped'];
		$listNameDB = ['Favoritos', 'Leídos', 'Por Leer', 'Leyendo', 'Abandonados'];
		try {
			$query = "INSERT INTO lists (id_user, list_name, type, visibility) VALUES (:id_user, :list_name, :type, :visibility)";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->bindParam(':visibility', $visDB);
			for ($i = 0; $i < 5; $i++) {
				$stmt->bindParam(':list_name', $listNameDB[$i]);
				$stmt->bindParam(':type', $typeDB[$i]);
				$stmt->execute();
			}
			return true;
		} catch (PDOException $e) {
			echo "Error al crear las listas básicas del usuario demo: " . $e->getMessage();
			return false;
		}
	}

    // create followers table
    public function createFollowersTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS followers (
                id_follower INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                id_followed INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
                FOREIGN KEY (id_followed) REFERENCES users(id_user) ON DELETE CASCADE,
                UNIQUE (id_user, id_followed)
            )";
            $this->conn->exec($sql);
            // echo "Tabla de seguidores creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de seguidores: " . $e->getMessage();
        }
    }

    // create books table
    public function createBooksTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS books (
                id_book INT AUTO_INCREMENT PRIMARY KEY,
                isbn VARCHAR(255) UNIQUE NOT NULL,
                title VARCHAR(255) NOT NULL,
                author VARCHAR(255) NOT NULL,
                genre VARCHAR(255) NOT NULL,
                editorial VARCHAR(255) NOT NULL,
                image VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->conn->exec($sql);
            // echo "Tabla de libros creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de libros: " . $e->getMessage();
        }
    }

	// create genres table
	public function createGenresTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS book_genres (
                id_genre INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) UNIQUE NOT NULL
            )";
            $this->conn->exec($sql);
            // echo "Tabla de géneros creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de géneros: " . $e->getMessage();
        }
    }

	// create reviews table
    public function createReviewsTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS book_reviews (
                id_review INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                isbn VARCHAR(255) NOT NULL,
                rating INT NOT NULL,
                comment TEXT NOT NULL,
                visibility ENUM('public','private') DEFAULT 'private',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user),
                FOREIGN KEY (isbn) REFERENCES books(isbn)
            )";
            $this->conn->exec($sql);
            // echo "Tabla de reseñas creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de reseñas: " . $e->getMessage();
        }
    }

    // create userlists table
    public function createListsTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS lists (
                id_list INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                list_name VARCHAR(255) NOT NULL,
				list_description VARCHAR(300),
                type ENUM('favorite', 'read', 'want_to_read','reading','dropped') DEFAULT NULL,
                visibility ENUM('public','private') DEFAULT 'private',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
            )";
            $this->conn->exec($sql);
            // echo "Tabla de listas de usuario creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de listas de usuario: " . $e->getMessage();
        }
    }

	public function insertDefaultLists(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM lists WHERE type IS NULL");
			$stmt->execute();
			$defaultListsExist = $stmt->fetch(PDO::FETCH_ASSOC);
	
			if (!$defaultListsExist) {
				// Obtener todos los usuarios con el rol de 'user'
				$usersStmt = $this->conn->prepare("SELECT id_user, username FROM users WHERE role = 'user'");
				$usersStmt->execute();
				$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
	
				$firstWords = ['Fiesta', 'Retiro', 'Visita', 'Aventura', 'Exploración', 'Viaje', 'Descubrimiento', 'Encuentro', 'Conferencia', 'Expedición', 'Excursión', 'Misión', 'Peregrinación', 'Ruta', 'Salida', 'Tour', 'Travesía', 'Trekking', 'Vuelta', 'Expedición'];
				$connectors = ['en', 'sobre', 'hacia', 'por', 'con', 'a', 'de', 'hasta', 'durante', 'desde', 'bajo', 'contra', 'entre', 'para', 'sin'];
				$lastWords = ['futuro', 'extranjero', 'más allá', 'paraíso', 'desconocido', 'aventura', 'destino', 'nuevo horizonte', 'próxima etapa', 'cielo', 'mar', 'montaña', 'desierto', 'bosque', 'ciudad', 'mundo', 'universo', 'naturaleza', 'planeta', 'continente'];
				$descriptions = [
					'Una experiencia inolvidable.',
					'Perfecto para los aventureros.',
					'Descubre nuevos horizontes.',
					'Ideal para familias y amigos.',
					'Explora lo desconocido.',
					'Un viaje lleno de sorpresas.',
					'Para los amantes de la naturaleza.',
					'Una aventura que no te puedes perder.',
					'Descubre lugares increíbles.',
					'Un destino mágico te espera.'
				];
	
				$insertStmt = $this->conn->prepare("INSERT INTO lists (id_user, list_name, list_description, visibility) VALUES (:id_user, :list_name, :list_description, :visibility)");
	
				foreach ($users as $user) {
					$numLists = rand(5, 7); // Cada usuario tendrá entre 5 y 7 listas.
					for ($i = 0; $i < $numLists; $i++) {
						$listName = $firstWords[array_rand($firstWords)] . ' ' . $connectors[array_rand($connectors)] . ' ' . $lastWords[array_rand($lastWords)];
						$description = $descriptions[array_rand($descriptions)];
						$visibility = 'public';
	
						$insertStmt->bindParam(':id_user', $user['id_user']);
						$insertStmt->bindParam(':list_name', $listName);
						$insertStmt->bindParam(':list_description', $description);
						$insertStmt->bindParam(':visibility', $visibility);
						$insertStmt->execute();
					}
				}
	
				// echo "Listas de usuario creadas exitosamente.";
			} else {
				// echo "Las listas de usuario ya existen en la base de datos.";
			}
		} catch(PDOException $e) {
			echo "Error al crear las listas de usuario: " . $e->getMessage();
		}
	}	

    // create booklists table
    public function createBooksInListsTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS books_in_lists (
                id_bookInList INT AUTO_INCREMENT PRIMARY KEY,
                id_list INT NOT NULL,
                isbn VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_list) REFERENCES lists(id_list) ON DELETE CASCADE,
                FOREIGN KEY (isbn) REFERENCES books(isbn) ON DELETE CASCADE,
                UNIQUE (id_list, isbn)
            )";
            $this->conn->exec($sql);
            // echo "Tabla de listas de libros creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de listas de libros: " . $e->getMessage();
        }
    }

    // create userfollowlists table
    public function createUserFollowListsTable(){
        try {
            $sql = "CREATE TABLE IF NOT EXISTS user_follow_lists (
                id_followList INT AUTO_INCREMENT PRIMARY KEY,
                id_user INT NOT NULL,
                id_list INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
                FOREIGN KEY (id_list) REFERENCES lists(id_list) ON DELETE CASCADE,
                UNIQUE (id_user, id_list)
            )";
            $this->conn->exec($sql);
            // echo "Tabla de listas de usuario seguidas creada exitosamente.";
        } catch(PDOException $e) {
            echo "Error al crear la tabla de listas de usuario seguidas: " . $e->getMessage();
        }
    }

	public function createDefaultListFollows(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM user_follow_lists");
			$stmt->execute();
			$defaultListFollowsExist = $stmt->fetch(PDO::FETCH_ASSOC);
	
			if (!$defaultListFollowsExist) {
				$stmt = $this->conn->prepare("SELECT id_user FROM users WHERE role = 'user'");
				$stmt->execute();
				$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
				$stmt = $this->conn->prepare("SELECT id_list, id_user FROM lists WHERE visibility = 'public' AND type IS NULL");
				$stmt->execute();
				$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
				$insertStmt = $this->conn->prepare("INSERT INTO user_follow_lists (id_user, id_list) VALUES (:id_user, :id_list)");
	
				foreach ($users as $user) {
					foreach ($lists as $list) {
						// Asegurarse de que el propietario de la lista no la siga
						if ($user['id_user'] != $list['id_user']) {
							// 50% de probabilidad de seguir la lista
							if (rand(0, 1) == 1) {
								$insertStmt->bindParam(':id_user', $user['id_user']);
								$insertStmt->bindParam(':id_list', $list['id_list']);
								$insertStmt->execute();
							}
						}
					}
				}
	
				// echo "Listas de usuario seguidas por defecto creadas exitosamente.";
			} else {
				// echo "Las listas de usuario seguidas por defecto ya existen en la base de datos.";
			}
		} catch(PDOException $e) {
			echo "Error al crear las listas de usuario seguidas por defecto: " . $e->getMessage();
		}
	}	

	// create default books
	public function createDefaultBooks(){
		try {

			$stmt = $this->conn->prepare("SELECT * FROM books");
			$stmt->execute();
			$existingBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if($existingBooks){
				// echo "Ya existen libros en la base de datos.";
				return;
			}

			$sql_queries = [
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS123456789', 'The Girl He Never Noticed', 'Neilani Alejandrino', 'Wattpadd', 'uploads/The_Girl_He_Never_Noticed.jpg', '[\"Romance\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS234567890', 'A different virus: Heartfire', 'Crystal Scherer', 'Wattpadd', 'uploads/A_Different_Virus.jpg', '[\"Science Fiction\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS345678901', 'Rapture', 'Athena', 'Wattpadd', 'uploads/Rapture.jpg', '[\"Fantasy\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS456789012', 'Night Shift', 'Annie Crown', 'Wattpadd', 'uploads/Night_Shift.jpg', '[\"Thriller\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS567890123', 'The mistery fighter', 'Aleksandra Elin', 'Wattpadd', 'uploads/The_Mistery_Fighter.jpg', '[\"Mystery\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS678901234', 'Plunder', 'R. S. Kovach', 'Wattpadd', 'uploads/Plunder.jpg', '[\"Adventure\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS789012345', 'Of Rust and Gold', 'Aliston J. Drake', 'Wattpadd', 'uploads/Of_Rust_And_gold.jpg', '[\"Historical Fiction\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS890123456', 'The long way back', 'Winifred Bates', 'Wattpadd', 'uploads/The_Long_Way_Back.jpg', '[\"Drama\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS901234567', 'Orc wars: Uprising', 'Ghost Lord', 'Wattpadd', 'uploads/Orc_Wars_Uprising.jpg', '[\"Fantasy\"]')",
				"INSERT INTO books (isbn, title, author, editorial, image, genre) VALUES ('BRS012345678', 'Four walls', 'M. C. Roman', 'Wattpadd', 'uploads/Four_Walls.jpg', '[\"Romance\"]')",
			];
	
			// Ejecutar las consultas SQL
			foreach ($sql_queries as $query) {
				$this->conn->exec($query);
			}

		} catch(PDOException $e) {
			echo "Error al añadir los libros por defecto: " . $e->getMessage();
		}
	}

	public function createBooksInLists(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM books_in_lists");
			$stmt->execute();
			$existingBooksInLists = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
			if (count($existingBooksInLists) < 10) {
				// Obtener todas las listas
				$stmt = $this->conn->prepare("SELECT id_list FROM lists WHERE visibility = 'public' AND type IS NULL");
				$stmt->execute();
				$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
				// Obtener todos los libros
				$stmt = $this->conn->prepare("SELECT isbn FROM books");
				$stmt->execute();
				$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
				$stmt = $this->conn->prepare("INSERT INTO books_in_lists (id_list, isbn) VALUES (:id_list, :isbn)");
	
				foreach ($books as $book) {
					foreach ($lists as $list) {
						// 50% de probabilidad de que el libro esté en la lista
						if (rand(0, 1) == 1) {
							$stmt->bindParam(':id_list', $list['id_list']);
							$stmt->bindParam(':isbn', $book['isbn']);
							$stmt->execute();
						}
					}
				}
			} else {
				// echo "Ya existen libros en listas en la base de datos.";
			}
		} catch(PDOException $e) {
			echo "Error al crear los libros en listas: " . $e->getMessage();
		}
	}	

	// insert default genres
	public function insertDefaultGenres(){
		try {
			$sql_queries = [
				"INSERT INTO book_genres (name) VALUES ('Romance') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Science Fiction') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Fantasy') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Thriller') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Mystery') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adventure') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Historical Fiction') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Drama') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Horror') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Biography') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Science') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Self-Help') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Cooking') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Travel') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('History') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Children') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Religion') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Aliens') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Vampires') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Werewolves') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Zombies') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Post-Apocalyptic') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Dystopian') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Time Travel') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Superheroes') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Magic') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Dragons') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Witches') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Fairies') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Angels') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Demons') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adventures') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Academic') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adolescence') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adult') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Adult-fiction') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Anime') ON DUPLICATE KEY UPDATE id_genre = id_genre",
				"INSERT INTO book_genres (name) VALUES ('Art') ON DUPLICATE KEY UPDATE id_genre = id_genre",
			];

			// Ejecutar las consultas SQL
			foreach ($sql_queries as $query) {
				$this->conn->exec($query);
			}
		} catch(PDOException $e) {
			echo "Error al añadir los géneros por defecto: " . $e->getMessage();
		}
	}

	public function createUserFollows() {
		try {
			$stmt = $this->conn->prepare("SELECT COUNT(*) as follow_count FROM followers");
			$stmt->execute();
			$UFUexists = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($UFUexists['follow_count'] < 10) {
				$stmt = $this->conn->prepare("SELECT id_user FROM users WHERE role != 'admin'");
				$stmt->execute();
				$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$stmt = $this->conn->prepare("INSERT INTO followers (id_user, id_followed) VALUES (:id_user, :id_followed)");

				$stmt->bindParam(':id_user', $id_user);
				$stmt->bindParam(':id_followed', $id_followed);

				foreach ($users as $user) {
					foreach ($users as $followed) {
						if ($user['id_user'] != $followed['id_user']) {
							$id_user = $user['id_user'];
							$id_followed = $followed['id_user'];
							$stmt->execute();
						}
					}
				}
			} else {
				// echo "Ya existen usuarios seguidos en la base de datos.";
			}
		} catch (PDOException $e) {
			echo "Error al crear los usuarios seguidos: " . $e->getMessage();
		}
	}

	public function createReviews(){
		try {
			$stmt = $this->conn->prepare("SELECT * FROM book_reviews");
			$stmt->execute();
			$existingReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (count($existingReviews) < 10) {
				$stmt = $this->conn->prepare("SELECT id_user FROM users WHERE role = 'user'");
				$stmt->execute();
				$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt = $this->conn->prepare("SELECT isbn FROM books");
				$stmt->execute();
				$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

				$stmt = $this->conn->prepare("INSERT INTO book_reviews (id_user, isbn, rating, comment, visibility) VALUES (:id_user, :isbn, :rating, :comment, :visibility)");
				$comments = [
					"Me ha encantado este libro, lo recomiendo a todo el mundo.",
					"Un libro muy interesante, aunque el final me ha dejado un poco frío.",
					"Un libro que no me ha gustado nada, no lo recomendaría.",
					"Un libro que me ha sorprendido gratamente, lo recomendaría a todo el mundo.",
					"Un libro que me ha dejado indiferente, ni fu ni fa.",
					"Un libro que me ha parecido muy aburrido, no lo recomendaría.",
					"Un libro que me ha parecido muy interesante, lo recomendaría a todo el mundo.",
					"Un libro que me ha parecido muy malo, no lo recomendaría.",
					"Un libro que me ha parecido muy bueno, lo recomendaría a todo el mundo.",
					"Un libro que me ha parecido muy regular, no lo recomendaría."
				];
				$review_visibility = 'public';
				$review_sc = [1, 2, 3, 4, 5];
				foreach ($users as $user) {
					foreach ($books as $book) {
						$stmt->bindParam(':id_user', $user['id_user']);
						$stmt->bindParam(':isbn', $book['isbn']);
						$stmt->bindParam(':rating', $review_sc[rand(0, 4)]);
						$stmt->bindParam(':comment', $comments[rand(0, 9)]);
						$stmt->bindParam(':visibility', $review_visibility);
						$stmt->execute();
					}
				}
			} else {
				// echo "Ya existen reseñas en la base de datos.";
			}
		}catch(PDOException $e) {
			echo "Error al crear las reseñas: " . $e->getMessage();
		}
	}

	// create usrebooks table
	public function createUserBooksTable(){
		try {
			$sql = "CREATE TABLE IF NOT EXISTS user_books (
				id_user INT NOT NULL,
				isbn VARCHAR(255) NOT NULL,
				tags VARCHAR(255),
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				FOREIGN KEY (id_user) REFERENCES users(id_user),
				FOREIGN KEY (isbn) REFERENCES books(isbn),
				PRIMARY KEY (id_user, isbn)
			)";
			$this->conn->exec($sql);
			// echo "Tabla de libros de usuario creada exitosamente.";
		} catch(PDOException $e) {
			echo "Error al crear la tabla de libros de usuario: " . $e->getMessage();
		}
	}
}
?>