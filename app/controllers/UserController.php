<?php


namespace controllers;

use models\User;
use models\ListModel;
use controllers\ListController;

require_once "../app/models/User.php";
require_once "../app/models/List.php";
require_once "../app/controllers/ListController.php";

class UserController
{
    private $userModel;
	private $listModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    public function createUser($username, $email, $password, $role)
    {
        $this->userModel->username = $username;
        $this->userModel->email = $email;
        $this->userModel->password = $password;
        $this->userModel->role = $role;

		$createdUserId = $this->userModel->createUser();
        if ($createdUserId != false) 
		{
			$listController = new ListController(new ListModel());
			$listController->createBasicLists($createdUserId);
            return true;
        } 
		else 
		{
            return false;
        }
    }

	public function readByUserID($user_id)
	{
		if ($user_id == null) {
			echo "El ID de usuario no puede estar vacío";
			return false;
		}
		return $this->userModel->readByUserID($user_id);
	}

	public function readByUsernameOrEmail($username)
	{
		if ($username == null)
		{
			echo "El nombre de usuario no puede estar vacío";
			return false;
		}
		return $this->userModel->readByUsernameOrEmail($username);
	}

	public function readAllUsers()
	{
		//TODO - Funcional pero mal implementado
		return $this->userModel->readAll();
	}

	public function updateUser($user_id, $username, $email, $password, $role)
	{
		//All other fields will be nulled, so I only check for user_id
		if ($user_id == null) 
		{
			return false;
		}
		if ($this->userModel->updateUser($user_id, $username, $email, $password, $role)) 
		{
			return true;
		} 
		else 
		{
			return false;
		}
	}

	public function deleteUser($user_id)
	{
		if ($user_id == null) 
		{
			echo "El ID de usuario no puede estar vacío";
			return false;
		}
		if ($this->userModel->deleteUser($user_id)) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}

    public function isUsernameExists($username)
    {
        if ($username == null)
		{
			echo "El nombre de usuario no puede estar vacío";
			return false;
		}
        return $this->userModel->isUsernameExists($username);
    }

    public function isEmailExists($email)
    {
        if ($email == null)
		{
			echo "El email no puede estar vacío";
			return false;
		}
        return $this->userModel->isEmailExists($email);
    }

	public function isUsernameExistsForUpdate($user_id, $username)
	{
		if ($username == null || $user_id == null)
		{
			echo "El nombre de usuario no puede estar vacío";
			return false;
		}
		return $this->userModel->isUsernameExistsForUpdate($user_id, $username);
	}

	public function isEmailExistsForUpdate($user_id, $email)
	{
		//TODO - Funcional pero mal implementado
		return $this->userModel->isEmailExistsForUpdate($user_id, $email);
	}

	public function getUserNameById($user_id)
	{
		if ($user_id == null) 
		{
			echo "El ID de usuario no puede estar vacío";
			return false;
		}
		return $this->userModel->getUserNameById($user_id);
	}

	public function userExists($user_id)
	{
		if ($user_id == null) 
		{
			echo "El ID de usuario no puede estar vacío";
			return false;
		}
		return $this->userModel->userExists($user_id);
	}

	public function searchUserByName($username)
	{
		if ($username == null) 
		{
			echo "El nombre de usuario no puede estar vacío";
			return false;
		}
		return $this->userModel->searchUserByName($username);
	}

	public function updateUserProfileImage($user_id, $profile_image)
	{
		if ($user_id == null) 
		{
			echo "El ID de usuario no puede estar vacío";
			return false;
		}
		return $this->userModel->updateUserProfileImage($user_id, $profile_image);
	}
}