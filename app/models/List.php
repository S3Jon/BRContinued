<?php

namespace models;
use PDO;

require_once "../app/config/Database.php";

use config\Database;
use PDOException;

class ListModel //List está reservado por PHP
{
	private $conn;
	private $table = 'lists';

	public $id_list;
	public $id_user;
	public $list_name;
	// public $type;	   //le importa al usuario?
	public $visibility;
	public $created_at;
	//public $updated_at;  //no se si es necesario

	// Conn
	public function __construct()
	{
		$db = new Database();
		$this->conn = $db->getConnection();
	}

	public function getPublicLists()
	{
		try {
			$query = '
				SELECT l.*, 
					COUNT(bil.isbn) AS BILCount,
					COALESCE(followersCount.followersNum, 0) AS followersNum,
					MAX(b.image) AS book_image
				FROM lists l
				LEFT JOIN books_in_lists bil ON l.id_list = bil.id_list
				LEFT JOIN books b ON bil.isbn = b.isbn
				LEFT JOIN (
					SELECT id_list, COUNT(id_list) AS followersNum
					FROM user_follow_lists
					GROUP BY id_list
				) AS followersCount ON l.id_list = followersCount.id_list
				WHERE l.visibility = "public" AND l.type IS NULL
				GROUP BY l.id_list
				ORDER BY l.created_at DESC, followersNum DESC';

			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al cargar listas publicas: " . $e->getMessage();
		}
	}

	public function getLast10PublicLists()
	{
		try {
			$query = '
				SELECT l.*, 
					COUNT(bil.isbn) AS BILCount,
					COALESCE(followersCount.followersNum, 0) AS followersNum,
					MAX(b.image) AS book_image,
					u.username
				FROM lists l
				LEFT JOIN books_in_lists bil ON l.id_list = bil.id_list
				LEFT JOIN books b ON bil.isbn = b.isbn
				LEFT JOIN users u ON l.id_user = u.id_user
				LEFT JOIN (
					SELECT id_list, COUNT(id_list) AS followersNum
					FROM user_follow_lists
					GROUP BY id_list
				) AS followersCount ON l.id_list = followersCount.id_list
				WHERE l.visibility = "public" AND l.type IS NULL
				GROUP BY l.id_list
				ORDER BY l.created_at DESC
				LIMIT 10';

			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al cargar listas publicas: " . $e->getMessage();
		}
	}

	public function deleteList($id_list)
	{
		try {
			$query = 'DELETE FROM ' . $this->table . ' WHERE id_list = :id_list';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
			echo "Error al borrar la lista: " . $e->getMessage();
		}
	}

	public function createList($id_user, $list_name, $list_description, $visibility)
	{
		try {
			$query = 'INSERT INTO ' . $this->table . ' (id_user, list_name, list_description, visibility) VALUES (:id_user, :list_name, :list_description, :visibility)';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->bindParam(':list_name', $list_name);
			$stmt->bindParam(':list_description', $list_description);
			$stmt->bindParam(':visibility', $visibility);
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
			echo "Error al crear la lista: " . $e->getMessage();
		}
	}	

	public function getListById($id_list)
	{
		try {
			$query = "
				SELECT lists.*, 
					   COALESCE(BILCount.book_count, 0) AS BILCount,
					   COALESCE(followersCount.followersNum, 0) AS followersNum,
					   (SELECT books.image
						FROM books_in_lists
						JOIN books ON books_in_lists.isbn = books.isbn
						WHERE books_in_lists.id_list = lists.id_list
						ORDER BY books_in_lists.isbn ASC
						LIMIT 1) AS list_pic
				FROM lists
				LEFT JOIN (
					SELECT id_list, COUNT(DISTINCT isbn) AS book_count
					FROM books_in_lists
					GROUP BY id_list
				) AS BILCount ON lists.id_list = BILCount.id_list
				LEFT JOIN (
					SELECT id_list, COUNT(id_list) AS followersNum
					FROM user_follow_lists
					GROUP BY id_list
				) AS followersCount ON lists.id_list = followersCount.id_list
				WHERE lists.id_list = :id_list
				GROUP BY lists.id_list";
	
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar los datos de la lista: " . $e->getMessage();
			return [];
		}
	}
	

	public function getUserBasicLists($id_user)
	{
		try {
			$query = '
				SELECT l.*, COUNT(bil.isbn) AS BILCount
				FROM ' . $this->table . ' l
				LEFT JOIN books_in_lists bil ON l.id_list = bil.id_list
				WHERE l.id_user = :id_user AND l.type IS NOT NULL
				GROUP BY l.id_list';
	
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar las listas básicas del usuario: " . $e->getMessage();
		}
	}

	//esta devuelve todas sus listas; list.php ya mira si es publica/privada y el que la mira es el propietario
	public function getUserLists($id_user)
	{
		try {
			$query = 'SELECT * FROM ' . $this->table . ' WHERE id_user = :id_user AND type IS NULL';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar las listas del usuario: " . $e->getMessage();
		}
	}

	public function getUserPublicLists($id_user)
	{
		try {
			$query = '
				SELECT l.*, 
					   COUNT(bil.isbn) AS BILCount,
					   COALESCE(followersCount.followersNum, 0) AS followersNum
				FROM ' . $this->table . ' l
				LEFT JOIN books_in_lists bil ON l.id_list = bil.id_list
				LEFT JOIN (
					SELECT id_list, COUNT(id_list) AS followersNum
					FROM user_follow_lists
					GROUP BY id_list
				) AS followersCount ON l.id_list = followersCount.id_list
				WHERE l.id_user = :id_user AND l.visibility = "public" AND l.type IS NULL
				GROUP BY l.id_list';
	
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar las listas públicas del usuario: " . $e->getMessage();
		}
	}

	public function createBasicLists($id_user)
	{
		$visDB = 'public';
		$typeDB = ['favorite', 'read', 'want_to_read', 'reading', 'dropped'];
		$listNameDB = ['Favoritos', 'Leídos', 'Por Leer', 'Leyendo', 'Abandonados'];
		try {
			$query = 'INSERT INTO ' . $this->table . ' (id_user, list_name, type, visibility) VALUES (:id_user, :list_name, :type, :visibility)';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->bindParam(':visibility', $visDB);
			for ($i = 0; $i < count($typeDB); $i++) {
				$stmt->bindParam(':list_name', $listNameDB[$i]);
				$stmt->bindParam(':type', $typeDB[$i]);
				$stmt->execute();
			}
			return true;
		} catch (PDOException $e) {
			echo "Error al crear las listas básicas: " . $e->getMessage();
		}
	}

	public function getListName($id_list)
	{
		try {
			$query = 'SELECT list_name FROM ' . $this->table . ' WHERE id_list = :id_list';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar el nombre de la lista: " . $e->getMessage();
		}
	}

	public function getListOwnerID($id_list)
	{
		try {
			$query = 'SELECT id_user FROM ' . $this->table . ' WHERE id_list = :id_list';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_list', $id_list);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar el propietario de la lista: " . $e->getMessage();
		}
	}

	public function getMostFollowed()
	{
		try {
			$query = "
				SELECT lists.*, 
					   COUNT(user_follow_lists.id_list) AS followersNum,
					   COALESCE(BILCount.BILCount, 0) AS BILCount,
					   (SELECT books.image
						FROM books_in_lists
						JOIN books ON books_in_lists.isbn = books.isbn
						WHERE books_in_lists.id_list = lists.id_list
						ORDER BY books_in_lists.isbn ASC
						LIMIT 1) AS list_pic
				FROM lists
				LEFT JOIN user_follow_lists ON lists.id_list = user_follow_lists.id_list
				LEFT JOIN (
					SELECT id_list, COUNT(DISTINCT isbn) AS BILCount
					FROM books_in_lists
					GROUP BY id_list
				) AS BILCount ON lists.id_list = BILCount.id_list
				WHERE lists.visibility = 'public' 
				  AND lists.type IS NULL
				GROUP BY lists.id_list
				ORDER BY followersNum DESC
				LIMIT 50";
			
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
		} catch (PDOException $e) {
			echo "Error al obtener listas más seguidas: " . $e->getMessage();
			die();
		}
	}
	
	

	public function getUserMostFollowed($id_user) //Para profile
	{
		try {
			$query = "SELECT lists.*, COUNT(user_follow_lists.id_list) AS followersNum
					  FROM lists 
					  LEFT JOIN user_follow_lists ON lists.id_list = user_follow_lists.id_list 
					  WHERE lists.id_user = :id_user 
					  AND lists.type IS NULL
					  GROUP BY lists.id_list 
					  ORDER BY COUNT(user_follow_lists.id_list) DESC 
					  LIMIT 50";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
		} catch (PDOException $e) {
			echo "Error al obtener listas más seguidas: " . $e->getMessage();
			die();
		}
	}

	public function updateList($id_list, $list_name, $list_description, $visibility)
	{
		try {
			$query = 'UPDATE ' . $this->table . ' 
				SET list_name = COALESCE(:list_name, list_name), 
					list_description = :list_description, 
					visibility = COALESCE(:visibility, visibility) 
				WHERE id_list = :id_list';
			$stmt = $this->conn->prepare($query);

			$stmt->bindParam(':list_name', $list_name);
			$stmt->bindParam(':list_description', $list_description);
			$stmt->bindParam(':visibility', $visibility);
			$stmt->bindParam(':id_list', $id_list);
	
			$stmt->execute();
			return true;
		} catch (PDOException $e) {
			echo "Error al actualizar la lista: " . $e->getMessage();
			return false;
		}
	}

	public function searchListLike($search)
	{
		try {
			$query = "
				SELECT lists.*, users.username,
					   COALESCE(BILCount.book_count, 0) AS BILCount,
					   COALESCE(followersCount.followersNum, 0) AS followersNum,
					   (SELECT books.image
						FROM books_in_lists
						JOIN books ON books_in_lists.isbn = books.isbn
						WHERE books_in_lists.id_list = lists.id_list
						ORDER BY books_in_lists.isbn ASC
						LIMIT 1) AS list_pic
				FROM lists
				JOIN users ON lists.id_user = users.id_user
				LEFT JOIN (
					SELECT id_list, COUNT(DISTINCT isbn) AS book_count
					FROM books_in_lists
					GROUP BY id_list
				) AS BILCount ON lists.id_list = BILCount.id_list
				LEFT JOIN (
					SELECT id_list, COUNT(id_list) AS followersNum
					FROM user_follow_lists
					GROUP BY id_list
				) AS followersCount ON lists.id_list = followersCount.id_list
				WHERE (lists.list_name LIKE :search OR users.username LIKE :search)
				  AND lists.visibility = 'public'
				  AND lists.type IS NULL
				GROUP BY lists.id_list, users.username";
	
			$stmt = $this->conn->prepare($query);
			$searchParam = "%" . $search . "%";
			$stmt->bindValue(':search', $searchParam, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
			return [];
		}
	}

	public function getUserTopLists($id_user)
	{
		try {
			$query = '
				SELECT l.*, 
					COUNT(bil.isbn) AS BILCount,
					COALESCE(followersCount.followersNum, 0) AS followersNum,
					MAX(b.image) AS book_image
				FROM ' . $this->table . ' l
				LEFT JOIN books_in_lists bil ON l.id_list = bil.id_list
				LEFT JOIN books b ON bil.isbn = b.isbn
				LEFT JOIN (
					SELECT id_list, COUNT(id_list) AS followersNum
					FROM user_follow_lists
					GROUP BY id_list
				) AS followersCount ON l.id_list = followersCount.id_list
				WHERE l.id_user = :id_user AND l.type IS NULL
				GROUP BY l.id_list
				ORDER BY followersNum DESC';

				
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo "Error al recuperar las listas principales del usuario: " . $e->getMessage();
		}
	}

	public function getAllUserLists($id_user)
	{
		try {
			$query = 'SELECT * FROM ' . $this->table . ' WHERE id_user = :id_user';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	
	
	

	//Para un futuro?
	/* 
	public function getListByUID($id_user)
	{
		try {
			$query = 'SELECT * FROM ' . $this->table . ' WHERE id_user = :id_user';
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id_user', $id_user);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
	*/
}