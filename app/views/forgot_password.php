<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../app/models/User.php';
    require_once '../app/controllers/UserController.php';
    require_once '../app/controllers/AuthController.php';

    $user = new models\User();
    $authController = new controllers\AuthController($user);

    //TODO Implementar la lógica para enviar un correo electrónico al usuario con un enlace para cambiar la contraseña
}

?>

<div class="min-h-screen px-10 flex items-center justify-center bg-cover bg-center bg-[url('https://cdn.pixabay.com/photo/2016/08/24/16/20/books-1617327_1280.jpg')]">
    <div class="text-center px-20 py-12 rounded backdrop-blur-[9px] bg-primary/[0.95]">
        <div class="flex items-center flex-col max-w-[42rem] bg-transparent">
            <img src="img/logo_white.svg" class="h-20 pl-4" alt="BookReaders_logo">
            <hr class="border-2 border-white w-[22rem] mb-4">                
            <div class="mt-7 w-full">
                <form action="" method="POST">
                    <p class="mb-7 text-xl text-background">
                        Ingresa tu correo electrónico y te enviaremos un <br>enlace para cambiar tu contraseña
                    </p>
                    <div class="mb-10">
                        <input placeholder="Correo electrónico" type="email" class="p-3 rounded w-full bg-background" id="username" name="username" required>
                    </div>                 
                    <div class="flex justify-center">
                        <button type="submit" class="text-xl w-fit font-semibold px-6 py-2 rounded bg-accent text-black hover:text-slate-700">Restaurar contraseña</button>
                    </div>

                    <?php
                    if (!empty($errorMessage)) {
                        echo "<div class='text-red-600 mt-4'>$errorMessage</div>";
                    }
                    ?>
                </form>
            </div>
            
            <div class="flex mt-7 flex-col justify-center items-center gap-1">
                <a href="login" class="text-xl w-fit py-2 rounded text-background ">Volver a <span class="font-semibold hover:underline">Iniciar sesión</span></a>
                <a href="landing" class="text-xl w-fit py-2 rounded text-background hover:underline font-semibold">Volver al inicio</a>
            </div>
        </div>
    </div>
</div>