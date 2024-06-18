<?php

session_start();

if (!isset($_SESSION['userData'])) {
    header('Location: landing');
    exit;
}

if (isset($_SESSION['userData']) && !empty($_SESSION['userData']['username'])) {
	$sessiont = $_SESSION['userData']['role'] == 'admin' ? 2 : 1;
} else {
	$sessiont = 3;	
}

if ($sessiont < 3) {
	$mensaje = "¡Bienvenido, " . $_SESSION['userData']['username'] . "!";
} else {
	$mensaje = "¡Bienvenido!";
}

require_once '../app/controllers/BookController.php';
require_once '../app/models/Book.php';
require_once '../app/controllers/ListController.php';
require_once '../app/models/List.php';
require_once '../app/controllers/ReviewController.php';
require_once '../app/models/Review.php';
require_once '../app/helpers/time_difference.php';
require_once '../app/helpers/genre_string.php';

$bookController = new controllers\BookController(new models\Book());
$listController = new controllers\ListController(new models\ListModel());
$reviewController = new controllers\ReviewController(new models\Review());

$books = $bookController->getLast10Books();

$reviews = $reviewController->getLast10Reviews();

$lists = $listController->getLast10PublicLists();

$activity = array_merge($books, $reviews, $lists);

// Ordenar la actividad por fecha de creación mas reciente y limitar a 10 elementos
usort($activity, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$activity = array_slice($activity, 0, 10);

?>

<?php include_once 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="md:mx-auto">
        <div class="flex gap-8 justify-center">
            <!-- Book image and actions -->
            <aside class="flex flex-col justify-center items-center gap-8 h-fit w-64 px-12 py-10 bg-accent/50 border-2 border-accent rounded-md">
                <h1 class="text-3xl font-bold font-serif4 text-center">Libro de la semana</h1>
                <img src="uploads/kafka.svg" alt="Book cover" class="w-48 h-64 object-cover">
                <div class="flex flex-col items-center w-full">
                    <h2 class="font-bold truncate" title="Titulo">The Metamorphosis</h2>
                    <a href="profile?id=1" class="truncate" title="Autor">Kafka</a>
                    <hr class="mt-2 w-full h-[2px] bg-accent border-accent">
                    <div class="max-h-24 mt-6 overflow-hidden text-ellipsis">
                        <p class="text-center line-clamp-3" title="Descripción">
                            Gregor Samsa, un modesto viajante de comercio, se despierta una mañana convertido en un insecto.
                        </p>
                    </div>
                    <button class="mt-6 w-full p-2 bg-primary text-background font-semibold rounded-md">Ver más</button>
                    <p class="mt-8 text-center">
                        ¿Quieres destacar tu libros? ¡Contacta con nosotros y te informaremos!
                    </p>
                    <button class="mt-6 w-full p-2 bg-white text-primary font-semibold rounded-md">Contactar</button>
                </div>
            </aside>
            <!-- Book details -->
            <section class="w-2/4">
                <h1 class="text-3xl font-bold font-serif4">La Comunidad de Lector@s</h1>
                <div class="flex justify-between items-center mb-3">
                    <p>
                        ¡Hola <?php echo $_SESSION['userData']['username'] ?? 'lector/a' ?>! Mira lo que está pasando en BookReaders
                    </p>
                    <!-- <div class="flex">
                        <button class="ml-4 px-5 py-2 bg-accent text-primary font-semibold rounded-full">Más recientes</button>
                        <button class="ml-4 px-5 py-2 border-2 border-primary text-primary font-semibold rounded-full">Siguiendo</button>
                    </div> -->
                </div>
                <div class="flex flex-col">
                    <?php foreach ($activity as $act) : ?>
                        <?php if (isset($act['id_book'])) : ?>
                            <div class="flex gap-4 items-start mt-4 p-4 bg-primary/15 rounded-lg">
                                <a href="book?isbn=<?= $act['isbn'] ?>" class="flex gap-4 items-start">
                                    <img src="<?php echo $act['image'] ?>" alt="Book cover" class="w-32 h-40 object-cover rounded-lg">
                                </a>
                                <div class="flex flex-col gap-4 w-full">
                                    <div class="flex items-center justify-between">
                                        <a href="profile?id=<?= $act['author'] ?>" class="font-bold"><?= $act['author'] ?> ha publicado un libro. <span class="font-normal"><?= timeDifference($act['created_at']) ?></span></a>
                                        <span class="ml-4 px-5 py-1 border-2 border-primary text-primary font-semibold rounded-full">Libro</span>
                                    </div>
                                    <h3 class="truncate" title="<?= $act['title'] ?>"><?= $act['title'] ?></h3>
                                    <p class="text-ellipsis" title="<?= genreString($act['genre']) ?>"><?= genreString($act['genre']) ?></p>
                                    <p class="text-ellipsis" title="<?= $act['editorial'] ?>"><?= $act['editorial'] ?></p>
                                </div>
                            </div>
                        <?php elseif (isset($act['id_review'])) : ?>
                            <div class="flex gap-4 items-start mt-4 p-4 bg-primary/15 rounded-lg">
                                <a href="book?isbn=<?= $act['isbn'] ?>" class="flex gap-4 items-start">
                                    <img src="<?php echo $act['image'] ?>" alt="Book cover" class="w-32 h-40 object-cover rounded-lg">
                                </a>
                                <div class="flex flex-col gap-4 w-full">
                                    <div class="flex items-center justify-between">
                                        <a href="profile?id=<?= $act['id_user'] ?>" class="font-bold"><?= $act['username'] ?> ha publicado una reseña. <span class="font-normal"><?= timeDifference($act['created_at']) ?></span></a>
                                        <!-- <h2 class="font-bold truncate" title="<?php echo $act['comment'] ?>"><?php echo $act['comment'] ?></h2> -->
                                        <p class="ml-4 px-5 py-1 border-2 border-primary text-primary font-semibold rounded-full">Reseña</p>
                                    </div>
                                    <div class="flex items-center gap-1" title="<?php echo $act['rating'] ?>">
                                        <p><?php echo $act['rating'] ?></p>
                                        <img src="img/star.svg" alt="Estrella" class="w-4 h-4 pb-[1.5px]">
                                    </div>
                                    <p class="text-ellipsis" title="<?php echo $act['comment'] ?>"><?php echo $act['comment'] ?></p>
                                </div>
                            </div>
                        <?php elseif (isset($act['id_list'])) : ?>
                            <div class="flex gap-4 items-start mt-4 p-4 bg-primary/15 rounded-lg">
                                <a href="list?id=<?= $act['id_list'] ?>" class="flex gap-4 items-start">
                                    <img src="<?php if (isset($act['book_image'])) echo $act['book_image']; else echo 'img/book-placeholder.jpeg'; ?>" alt="Book cover" class="w-32 h-40 object-cover rounded-lg">
                                </a>
                                <div class="flex flex-col justify-between gap-4 w-full">
                                    <div class="flex items-center justify-between">
                                        <a href="profile?id=<?= $act['id_user'] ?>" class="font-bold"><?= $act['username'] ?> ha creado una lista. <span class="font-normal"><?= timeDifference($act['created_at']) ?></span></a>
                                        <span class="ml-4 px-5 py-1 border-2 border-primary text-primary font-semibold rounded-full">Lista</span>
                                    </div>
                                    <p class="text-ellipsis" title="<?php echo $act['list_name'] ?>">
                                        <?php if ($act['list_name'] != '') echo $act['list_name'];
                                        else echo 'Sin titulo'; ?>
                                    <p class="text-ellipsis" title="<?php echo $act['list_description'] ?>">
                                        <?php if ($act['list_description'] != '') echo $act['list_description'];
                                        else echo 'Sin descripción'; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php include_once 'partials/footer.php'; ?>