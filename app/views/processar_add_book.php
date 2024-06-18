<?php

require_once '../app/models/Book.php';
require_once '../app/controllers/BookController.php';

session_start();
if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'admin') {
    header('Location: home');
    exit;
}

// add book 
$bookController = new controllers\BookController(new models\Book());
$books = $bookController->readAllBooks();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn = $_POST['isbn'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $genre = json_encode($genre);
    $editorial = $_POST['editorial'];

    //guardar la imagen en la carpeta uploads y guardar la ruta en la base de datos
    $imagePost = $_FILES['image']['name'];
    $url = 'uploads/' . $imagePost;
    move_uploaded_file($_FILES['image']['tmp_name'], $url);

    $image = $url;    

    if ($bookController->createBook($isbn, $title, $author, $genre, $editorial, $image)) {
        echo "Libro añadido con éxito.";
        header("Location: adminpanel");
        exit;
    } else {
        echo "Error al añadir el libro.";
        header("Location: adminpanel");
        exit;
    }    
}