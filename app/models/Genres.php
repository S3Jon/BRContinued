<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class Genres
{  
    private $conn;
    private $table_name = "book_genres";

    public $id_genre;
    public $name;

    public function __construct()
    {
        $this->conn = (new Database())->getConnection();
    }

    public function getGenres()
    {
        try {
            $query = "SELECT * FROM $this->table_name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Error al obtener géneros: " . $e->getMessage();
            die();
        }
    }
}

?>