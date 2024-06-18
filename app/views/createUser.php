<?php

require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";

use models\User;
use controllers\UserController;

session_start();

unset($_SESSION['success']);
unset($_SESSION['alert']);

if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'admin') {
    header('Location: home');
    exit;
}

if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['repeatPassword'])){
    
    $user = new User();

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeatPassword = $_POST['repeatPassword'];
    $role = isset($_POST['role']) ? $_POST['role'] : 'user';
    
    $userController = new UserController($user);

    if ($userController->isUsernameExists($username)) {
        $_SESSION['alert'] = "El nombre de usuario ya existe";
    } else if ($userController->isEmailExists($email)) {
        $_SESSION['alert'] = "El email ya existe";
    } else if ($password !== $repeatPassword) {
        $_SESSION['alert'] = "Las contraseñas no coinciden";
    }
    else {
        if($userController->createUser($username, $email, $password, $role)) {
            $_SESSION['success'] = "Usuario registrado correctamente";            
        } else {
            $_SESSION['alert'] = "Error al registrar el usuario";
        }
    }
    
    // Verificar si hay un mensaje de éxito
    $successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';

    // Verificar si hay un mensaje de alerta si no hay un mensaje de éxito
    $errorMessage = !empty($successMessage) ? '' : (isset($_SESSION['alert']) ? $_SESSION['alert'] : '');
}

?>

<div class="min-h-screen px-10 flex items-center justify-center bg-cover bg-center bg-[url('https://cdn.pixabay.com/photo/2016/08/24/16/20/books-1617327_1280.jpg')]">
    <div class="text-center px-20 py-12 rounded backdrop-blur-[9px] bg-primary/[0.95]">
        <div class="flex items-center flex-col max-w-[42rem] bg-transparent">
            <img src="img/logo_white.svg" class="h-20 pl-4" alt="BookReaders_logo">
            <hr class="border-2 border-white w-[22rem] mb-4">
            <div class="mt-7 w-full">
                <form action="" method="POST">
                    <div class="mb-4">
                        <input placeholder="Nombre de usuario" type="text" class="p-3 rounded w-full bg-background" id="username" name="username" required>
                    </div>
                    <div class="mb-4">
                        <input placeholder="Correo electrónico" type="email" class="p-3 rounded w-full bg-background" id="email" name="email" required>
                    </div>
					<div class="mb-4">
						<select class="p-3 rounded w-full bg-background" id="role" name="role">
							<option value="user">Usuario</option>
							<option value="admin">Administrador</option>
						</select>
					</div>
                    <div class="mb-4">
                        <input placeholder="Contraseña" type="password" class="p-3 rounded w-full bg-background" id="password" name="password" required>
                    </div>
                    <div class="mb-10">
                        <input placeholder="Repetir contraseña" type="password" class="p-3 rounded w-full bg-background" id="repeatPassword" name="repeatPassword" required>
                    </div>
                    <div class="flex justify-center">
                        <button href="register" class="text-xl w-fit font-semibold px-6 py-2 rounded bg-accent text-black hover:text-slate-700">Añadir usuario</button>
                    </div>

                    <?php
                    if (!empty($successMessage)) {
                        echo "<div class='text-green-600 mt-4'>$successMessage</div>";
                    } else if (!empty($errorMessage)) {
                        echo "<div class='text-red-600 mt-4'>$errorMessage</div>";
                    }
                    ?>
                </form>
            </div>
            
            <div class="flex mt-7 flex-col justify-center items-center gap-1">
                <a href="adminpanel" class="text-xl w-fit py-2 rounded text-background hover:underline font-semibold">Volver a adminpanel</a>
            </div>
        </div>
    </div>
</div>