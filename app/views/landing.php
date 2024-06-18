<?php 

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';

session_start();

$userController = new controllers\UserController(new models\User());

if (isset($_SESSION['userData'])) {
    header('Location: home');
}

?>

<header>
    <nav class="bg-primary p-8">
        <div class="flex items-center justify-between mx-4">
            <a href="landing"><img src="img/logo_white.svg" class="h-11" alt="BookReaders_logo"></a>
            <div class="flex items-center gap-10">
                <a href="<?= isset($_SESSION['userData']) ? 'home' : 'landing'?>" class="text-xl font-semibold text-slate-200 hover:text-blue-400">Inicio</a>
                <a href="explore" class="text-xl font-semibold text-slate-200 hover:text-blue-400">Explorar</a>
                <a href="contacto" class="text-xl font-semibold text-slate-200 hover:text-blue-400">Contacto</a>
                <a href="register"
                    class="text-xl font-semibold px-6 py-2 rounded bg-accent text-primary hover:text-white">Regístrate</a>
            </div>
        </div>
    </nav>
</header>

<div
    class="min-h-[90vh] px-10 flex items-center justify-center bg-cover bg-center bg-[url('https://cdn.pixabay.com/photo/2016/08/24/16/20/books-1617327_1280.jpg')]">
    <div class="text-center bg-slate-700 px-20 py-12 rounded backdrop-blur-[5px] bg-white/90">
        <div class="flex flex-col max-w-[42rem]">
            <img src="img/logo_black.svg" class="h-20" alt="BookReaders_logo">
            <p class="mt-7 text-xl">
                ¡Únete a nuestra comunidad de lectores!
            </p>
            <p class="mb-10 text-xl">
                Descubre nuevos libros, comparte reseñas y conéctate con otros amantes de la lectura.
                Regístrate ahora para acceder a todo el contenido y formar parte de esta apasionante comunidad. ¡No te
                lo pierdas!
            </p>
            <div class="flex flex-col justify-center items-center gap-3">
                <a href="register"
                    class="text-xl w-fit font-semibold px-6 py-2 rounded bg-accent text-primary hover:text-white">Quiero
                    registrarme</a>
                <a href="explore" class="text-xl w-fit px-6 py-2 rounded text-primary hover:underline">prefiero<span
                        class="font-semibold"> Explorar</span></a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'partials/footer.php'; ?>