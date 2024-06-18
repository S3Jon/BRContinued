<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/controllers/BookController.php';
require_once '../app/models/Book.php';

session_start();

$bookController = new controllers\BookController(new models\Book());
$books = $bookController->getTop50Books();

$genre = $_GET['genre'] ?? null;
if ($genre) {
    $books = $bookController->getBooksByGenre($genre);
}

$search = $_GET['search'] ?? null;
if ($search) {
    $books = $bookController->searchBooks($search);
}

?>

<?php require_once 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="w-3/4 mx-auto">
        <!-- Header buscador de libros -->
        <div class="flex flex-col md:flex-row md:items-center w-full gap-5 md:gap-20">
            <h1 class="font-serif4 font-bold text-3xl min-w-fit">Explorar libros</h1>
            <form id="searchForm" action="" method="GET" class="flex items-center gap-5 w-full pb-1 border-b-2 border-primary">
                <input type="text" id="searchInput" autocomplete="off" name="search" class="w-full py-2 bg-transparent outline-none" placeholder="Busca un libro, autor/a o editorial">
                <button type="submit" class="min-w-4 min-h-4"><img class="min-w-4 min-h-4" src="img/lupa.svg" alt="lupa.svg"></button>
            </form>
        </div>

        <!-- Lista de libros -->
        <div id="bookList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 justify-items-center gap-10 mt-10">
            <?php foreach ($books as $book) : ?>
                <form action="book" method="POST" class="cursor-pointer">
                    <input type="hidden" name="isbn" value="<?= $book['isbn']?>">
                    <div class="flip-container">
                        <div class="flip-card">
                            <div class="flip-front bg-white w-full h-full">
                                <img class="w-full h-full" src="<?= $book['image']?>" alt="<?= $book['image']?>">
                            </div>
                            <div class="flip-back">
                                <div class="flex flex-col items-center justify-center h-full gap-5 p-3">
                                    <h2 class="font-serif4 font-bold text-xl"><?= $book['title']?></h2>
                                    <p class="font-serif font-light text-sm"><?= $book['author']?></p>
                                    <p class="font-serif font-light text-sm"><?= $book['editorial']?></p>
                                    <div class="flex items center gap-1">
                                        <?php if ($book['rating'] > 0) : ?>
                                            <p class = "text-xs"><?= number_format($book['rating'], 1)?></p>
                                            <img class="w-3 h-3" src="img/star.svg" alt="star">
                                        <?php else : ?>
                                            <p class="text-xs">Sin reseñas!</p>
                                        <?php endif; ?>
                                        <?php if ($book['review_count'] > 0) : ?>
                                            <p class="text-xs"> (<?= $book['review_count'] ?>)</p>
                                        <?php endif; ?>
                                    </div>
                                    <a href="book?isbn=<?= $book['isbn'] ?>" class="bg-primary/40 text-white px-5 py-2 rounded-full">Ver más</a>
                                </div>
                            </div>
                        </div>
                    </div> 
                </form>
            <?php endforeach; ?>
        </div>
        
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>

<script>
document.getElementById('searchInput').addEventListener('input', function() {
    let searchValue = this.value;

    // Crear una solicitud AJAX para buscar libros
    fetch(`?search=${searchValue}`)
        .then(response => response.text())
        .then(data => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(data, 'text/html');

            let newBookList = doc.getElementById('bookList').innerHTML;

            document.getElementById('bookList').innerHTML = newBookList;
        });
});
</script>
