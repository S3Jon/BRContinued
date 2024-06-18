<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class UFLModel
{
	private $conn;
	private $table_name = "user_follow_lists";

	public $id_followList;
	public $id_user;
	public $id_list;

	public function __construct()
	{
		$database = new Database();
		$this->conn = $database->getConnection();
	}

	public function followList($id_user, $id_list)
	{
		try {
			$query = "INSERT INTO " . $this->table_name . " (id_user, id_list) VALUES (:id_user, :id_list)";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
		} catch (PDOException $e) {
			echo "Error al seguir lista: " . $e->getMessage();
			die();
		}
	}

	public function unfollowList($id_user, $id_list)
	{
		try {
			$query = "DELETE FROM " . $this->table_name . " WHERE id_user = :id_user AND id_list = :id_list";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
		} catch (PDOException $e) {
			echo "Error al dejar de seguir lista: " . $e->getMessage();
			die();
		}
	}

	public function getFollowedLists($id_user)
	{
		try {
			$query = "
				SELECT l.*, 
					   COALESCE(BILCount.book_count, 0) AS BILCount,
					   COALESCE(followersCount.followersNum, 0) AS followersNum,
					   (SELECT books.image
						FROM books_in_lists
						JOIN books ON books_in_lists.isbn = books.isbn
						WHERE books_in_lists.id_list = l.id_list
						ORDER BY books_in_lists.isbn ASC
						LIMIT 1) AS list_pic,
					   u.username AS ownerName
				FROM user_follow_lists ufl
				JOIN lists l ON ufl.id_list = l.id_list
				JOIN users u ON l.id_user = u.id_user
				LEFT JOIN (
					SELECT id_list, COUNT(DISTINCT isbn) AS book_count
					FROM books_in_lists
					GROUP BY id_list
				) AS BILCount ON l.id_list = BILCount.id_list
				LEFT JOIN (
					SELECT id_list, COUNT(id_list) AS followersNum
					FROM user_follow_lists
					GROUP BY id_list
				) AS followersCount ON l.id_list = followersCount.id_list
				WHERE ufl.id_user = :id_user
				GROUP BY l.id_list";
			
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al obtener listas seguidas: " . $e->getMessage();
			die();
		}
	}

	public function isFollowing($id_user, $id_list)
	{
		try {
			$query = "SELECT COUNT(*) AS isFollowing FROM " . $this->table_name . " WHERE id_user = :id_user AND id_list = :id_list";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return $row['isFollowing'];
		} catch (PDOException $e) {
			echo "Error al comprobar si sigue lista: " . $e->getMessage();
			die();
		}
	}

	public function getFollowersNumber($id_list)
	{
		try {
			$query = "SELECT COUNT(*) AS followers FROM " . $this->table_name . " WHERE id_list = :id_list";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return $row['followers'];
		} catch (PDOException $e) {
			echo "Error al obtener seguidores: " . $e->getMessage();
			die();
		}
	}

	//TODO- Rework
	public function getMostFollowed()
	{
		//$tabla_listas = "lists";
		try {
			$query = "SELECT lists.id_list 
			FROM lists 
			LEFT JOIN user_follow_lists ON lists.id_list = user_follow_lists.id_list 
			WHERE lists.visibility = 'public' 
			AND lists.type IS NULL
			GROUP BY lists.id_list 
			ORDER BY COUNT(user_follow_lists.id_list) DESC 
			LIMIT 50;";
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $row;
		} catch (PDOException $e) {
			echo "Error al obtener listas más seguidas: " . $e->getMessage();
			die();
		}
	}
}