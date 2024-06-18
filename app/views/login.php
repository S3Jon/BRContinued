<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../app/models/User.php';
    require_once '../app/controllers/UserController.php';
    require_once '../app/controllers/AuthController.php';

    $user = new models\User();
    $authController = new controllers\AuthController($user);

    if ($authController->login()) {
        header('Location: home');
    } else {
        $errorMessage = "Usuario o contraseña incorrectos";
    }
}
session_start();

if (isset($_SESSION['userData'])) {
	header('Location: home');
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
                        <input placeholder="Correo electrónico" type="email" class="p-3 rounded w-full bg-background" id="email" name="email" required>
                    </div>
                    <div class="mb-10">
                        <input placeholder="Contraseña" type="password" class="p-3 rounded w-full bg-background" id="password" name="password" required>
                    </div>                        
                    <div class="flex justify-center">
                        <button type="submit" class="text-xl w-fit font-semibold px-6 py-2 rounded bg-accent text-black hover:text-slate-700">Iniciar sesion</button>
                    </div>

                    <?php
                    if (!empty($errorMessage)) {
                        echo "<div class='text-red-600 mt-4'>$errorMessage</div>";
                    }
                    ?>
                </form>
            </div>
            
            <div class="flex mt-7 flex-col justify-center items-center gap-1">                    
                <a href="register" class="text-xl w-fit py-2 rounded text-background ">¿No tienes cuenta? <span class="font-semibold hover:underline">Regístrate</span></a>
                <a href="forgot_password" class="text-xl w-fit py-2 rounded text-background ">¿Olvidaste tu contraseña? <span class="font-semibold hover:underline">Cambiar contraseña</span></a>
                <a href="landing" class="text-xl w-fit py-2 rounded text-background hover:underline font-semibold">Volver al inicio</a>
            </div>
        </div>
    </div>
</div>
