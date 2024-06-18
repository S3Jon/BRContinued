<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class Book
{  
    private $conn;
    private $table_name = "books";

    // Propiedades del libro
    public $id_book;
    public $isbn;
    public $title;
    public $author;
    public $genre;
    public $editorial;
    public $image;
    public $rating;
    // public $tags; //TODO implementar tags

    // Constructor para conectarse a la base de datos
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Método para crear un nuevo libro
    public function createBook() //$isbn, $title, $author, $genre, $editorial, $image
    {
        try {
            // verificamos si el ISBN ya existe
            $query = "SELECT * FROM " . $this->table_name . " WHERE isbn = :isbn";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->execute();
            if($stmt->rowCount() > 0){
                return false;
            }

            // Creamos la consulta para insertar los datos
            $query = "INSERT INTO " . $this->table_name . " (isbn, title, author, genre, editorial, image) VALUES (:isbn, :title, :author, :genre, :editorial, :image)";
            
            // Preparamos la sentencia SQL para insertar los datos
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':isbn', $this->isbn);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':author', $this->author);
            $stmt->bindParam(':genre', $this->genre);
            $stmt->bindParam(':editorial', $this->editorial);
            $stmt->bindParam(':image', $this->image);

            if($stmt->execute()){
                return true;
            }

            return false;

        }catch(PDOException $e){
            echo "Error al crear un libro: " . $e->getMessage();
            die();
        }
    }

    // Método para obtener todos los libros
    public function getBooks()
    {
        try {
            $query = "SELECT b.*, AVG(r.rating) AS rating
                    FROM " . $this->table_name . " b
                    LEFT JOIN book_reviews r ON b.isbn = r.isbn
                    GROUP BY b.id_book";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener todos los libros: " . $e->getMessage();
            die();
        }
    }


    // Método para obtener un libro por su ID
    public function getBookById($id_book)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id_book = :id_book";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_book', $id_book);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener un libro por su ID: " . $e->getMessage();
            die();
        }
    }

    // Método para actualizar un libro
    public function updateBook($id_book, $isbn, $title, $author, $genre, $editorial, $image)
    {
        try {
            // Creamos la consulta para actualizar los datos
            $query = "UPDATE " . $this->table_name . " SET isbn = :isbn, title = :title, author = :author, genre = :genre, editorial = :editorial, image = :image WHERE id_book = :id_book";
            
            // Preparamos la sentencia SQL para actualizar los datos
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id_book', $id_book);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':author', $author);
            $stmt->bindParam(':genre', $genre);
            $stmt->bindParam(':editorial', $editorial);
            $stmt->bindParam(':image', $image);

            if($stmt->execute()){
                return true;
            }

            return false;

        }catch(PDOException $e){
            echo "Error al actualizar un libro: " . $e->getMessage();
            die();
        }
    }

    // Método para eliminar un libro
    public function deleteBook($isbn)
    {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE isbn = :isbn";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':isbn', $isbn);

            if($stmt->execute()){
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            echo "Error al eliminar un libro: " . $e->getMessage();
            die();
        }
    }

	//TODO- A futuro solo sacar los que tengan mas de X reviews para evitar 1 rating 5 star que sean el top
	public function getTop50Books()
	{
        try {
            $query = "
            SELECT 
                b.*, 
                COALESCE(AVG(r.rating), 0) AS rating,
                COUNT(r.rating) AS review_count
            FROM 
                " . $this->table_name . " b
            LEFT JOIN 
                book_reviews r ON b.isbn = r.isbn
            GROUP BY 
                b.id_book
            ORDER BY 
                rating DESC
            LIMIT 50";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener los 50 libros más populares: " . $e->getMessage();
            die();
        }
	}

    // Método para obtener los libros más recientes
    public function getLast10Books()
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY id_book DESC LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener los 10 libros más recientes: " . $e->getMessage();
            die();
        }
    }

	// Método para obtener los libros más populares
	public function getTopBooks()
	{
		try {
			$query = "SELECT b.*, AVG(r.rating) AS rating
					FROM " . $this->table_name . " b
					LEFT JOIN book_reviews r ON b.isbn = r.isbn
					GROUP BY b.id_book
					ORDER BY rating DESC";
			
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al obtener los libros más populares: " . $e->getMessage();
			die();
		}
	}

	// Método para obtener los libros más populares por género
	public function getTopBooksByGenre($genre)
	{
		try {
			$query = "SELECT b.*, AVG(r.rating) AS rating
					FROM " . $this->table_name . " b
					LEFT JOIN book_reviews r ON b.isbn = r.isbn
					WHERE b.genre = :genre
					GROUP BY b.id_book
					ORDER BY rating DESC";
			
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':genre', $genre);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al obtener los libros más populares por género: " . $e->getMessage();
			die();
		}
	}

	// Método para obtener los libros más populares por autor
	public function getTopBooksByAuthor($author)
	{
		try {
			$query = "SELECT b.*, AVG(r.rating) AS rating
					FROM " . $this->table_name . " b
					LEFT JOIN book_reviews r ON b.isbn = r.isbn
					WHERE b.author = :author
					GROUP BY b.id_book
					ORDER BY rating DESC";
		} catch (PDOException $e) {
			echo "Error al obtener los libros más populares por autor: " . $e->getMessage();
			die();
		}
	}

    // Método para buscar libros por su título
    public function searchBooksByTitle($title)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE title LIKE :title";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':title', '%' . $title . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su título: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su autor
    public function searchBooksByAuthor($author)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE author LIKE :author";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':author', '%' . $author . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su autor: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su género
    public function searchBooksByGenre($genre)
    {
        try {
            $query = "
                SELECT b.*, AVG(r.rating) as rating, COUNT(r.rating) as review_count
                FROM " . $this->table_name . " b
                LEFT JOIN book_reviews r ON b.isbn = r.isbn
                WHERE b.genre LIKE :genre
                GROUP BY b.isbn
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':genre', '%' . $genre . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su género: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su editorial
    public function searchBooksByEditorial($editorial)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE editorial LIKE :editorial";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':editorial', '%' . $editorial . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su editorial: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su ISBN
    public function searchBooksByIsbn()
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE isbn LIKE :isbn";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':isbn', '%' . $this->isbn . '%');
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su ISBN: " . $e->getMessage();
            die();
        }
    }

    // Método para buscar libros por su título y autor
    public function searchBooksByTitleAndAuthor($title, $author)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE title LIKE :title AND author LIKE :author";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':title', '%' . $title . '%');
            $stmt->bindValue(':author', '%' . $author . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su título y autor: " . $e->getMessage();
            die();
        }
    }

    //search by title, genre,author,editorial
    public function searchBooks($search)
    {
        try {
            $query = "SELECT b.* , AVG(r.rating) as rating, COUNT(r.rating) as review_count
                    FROM " . $this->table_name . " b
                    LEFT JOIN book_reviews r ON b.isbn = r.isbn
                    WHERE b.title LIKE :search
                    OR b.genre LIKE :search
                    OR b.author LIKE :search
                    OR b.editorial LIKE :search
                    GROUP BY b.isbn";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':search', '%' . $search . '%');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al buscar libros por su título, género, autor o editorial: " . $e->getMessage();
            die();
        }
    }

    // Método para obtener la actividad de los libros
    public function getActivity()
    {
        // try {
            
        //     $stmt = $this->conn->prepare($query);
        //     $stmt->execute();
        //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // } catch (PDOException $e) {
        //     echo "Error al obtener la actividad de los libros: " . $e->getMessage();
        //     die();
        // }
    }
}