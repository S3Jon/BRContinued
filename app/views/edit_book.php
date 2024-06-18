<?php

require_once '../app/controllers/BookController.php';
require_once '../app/models/Book.php';
require_once '../app/models/Genres.php';

session_start();

if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'admin') {
    header('Location: home');
    exit;
}

$bookController = new controllers\BookController(new models\Book());
$genres = new models\Genres();
$genres = $genres->getGenres();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_book'])) {
        $isbn = $_POST['isbn'];

        $book = $bookController->readBook($isbn);

    } else if (isset($_POST['update_book'])) {
        $id_book = $_POST['id_book'];
        $isbn = $_POST['isbn'];
        $title = $_POST['title'];
        $author = $_POST['author'];
        $genre = json_encode($_POST['genre']);
        var_dump($genre);
        $editorial = $_POST['editorial'];
        $image = $_POST['image'];

        if ($bookController->updateBook($id_book, $isbn, $title, $author, $genre, $editorial, $image)) {
            echo "Libro actualizado con éxito.";
            header("Location: adminpanel");
            exit;
        } else {
            echo "Error al actualizar el libro.";
        }

    }
}

?>

<div class="flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded shadow-2xl w-1/3">
        <h2 class="text-2xl font-bold mb-4">Editar Libro</h2>
        <form action="edit_book" method="post" class="mb-4">
            <input type="hidden" name="id_book" value="<?php echo $book['id_book']; ?>">
            <label for="isbn" class="text-sm text-gray-600">ISBN</label>
            <input type="text" name="isbn" value="<?php echo $book['isbn']; ?>" class="w-full block mb-2 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-blue-500">
            <label for="title" class="text-sm text-gray-600">Título</label>
            <input type="text" name="title" value="<?php echo $book['title']; ?>" class="w-full block mb-2 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-blue-500" placeholder="Título">
            <label for="author" class="text-sm text-gray-600">Autor</label>
            <input type="text" name="author" value="<?php echo $book['author']; ?>" class="w-full block mb-2 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-blue-500" placeholder="Autor">
            <label for="genre" class="text-sm text-gray-600">Género</label>
            <select name="genre[]" id="genre" class="w-full block mb-2 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500" multiple>
                <?php foreach ($genres as $genre) : ?>
                    <option value="<?php echo $genre['name']; ?>" <?php if (in_array($genre['name'], json_decode($book['genre']))) echo 'selected'; ?>><?php echo $genre['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <label for="editorial" class="text-sm text-gray-600">Editorial</label>
            <input type="text" name="editorial" value="<?php echo $book['editorial']; ?>" class="w-full block mb-2 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-blue-500" placeholder="Editorial">
            <label for="image" class="text-sm text-gray-600">Imagen</label>
            <input type="text" name="image" value="<?php echo $book['image']; ?>" class="w-full block mb-4 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-blue-500" placeholder="Imagen">
            <button type="submit" name="update_book" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Actualizar</button>
        </form>
        <a href="adminpanel" class="text-blue-500">Volver</a>
    </div>
</div>