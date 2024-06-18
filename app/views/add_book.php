<?php 

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/models/Genres.php';

session_start();
if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'admin') {
    header('Location: home');
    exit;
}

$genres = new models\Genres();
$genres = $genres->getGenres();

?>

<?php require_once 'partials/header.php'; ?>

<div class="container mx-auto my-8">
    <h2 class="text-2xl font-bold mb-4">Añadir Libro</h2>
    <form action="processar_add_book" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="isbn" class="block text-gray-700 text-sm font-bold mb-2">ISBN</label>
            <input required type="text" name="isbn" id="isbn" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Título</label>
            <input required type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label for="author" class="block text-gray-700 text-sm font-bold mb-2">Autor</label>
            <input required type="text" name="author" id="author" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label for="genre" class="block text-gray-700 text-sm font-bold mb-2">Género</label>
            <select name="genre[]" id="genre" multiple>
                <?php foreach ($genres as $genre) : ?>
                    <option value="<?= $genre['name'] ?>"><?= $genre['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="editorial" class="block text-gray-700 text-sm font-bold mb-2">Editorial</label>
            <input required type="text" name="editorial" id="editorial" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Imagen</label>
            <input required type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <button type="submit" name="add_book" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Añadir Libro</button>
    </form>
</div>

<?php require_once 'partials/footer.php'; ?>

