<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class Booktest
{  
    private $conn;
    private $table_name = "books";

    // Propiedades del usuario
    public $id_book;
    public $isbn;
    public $title;
    public $author;
    public $genre;
	public $editorial;
	public $image;

    //Constructor to connect to the database
    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

	public function getbookinfo($id_book){
		$query = "SELECT * FROM " . $this->table_name . " WHERE id_book = :id_book";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':id_book', $id_book);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	public function getBookTitle($isbn){
		$query = "SELECT title FROM " . $this->table_name . " WHERE isbn = :isbn";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':isbn', $isbn);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	public function getBookAuthor($isbn){
		$query = "SELECT author FROM " . $this->table_name . " WHERE isbn = :isbn";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':isbn', $isbn);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	public function getBookPublisher($isbn){
		$query = "SELECT editorial FROM " . $this->table_name . " WHERE isbn = :isbn";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':isbn', $isbn);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}

	public function getBookGenre($isbn){
		$query = "SELECT genre FROM " . $this->table_name . " WHERE isbn = :isbn";
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':isbn', $isbn);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$genre = $genre = preg_replace('/[^a-zA-Z0-9, ]/', '', $row['genre']); //LOLOLOLOLOLOLOL
		return $genre;
	}
}
?>