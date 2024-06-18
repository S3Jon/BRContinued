<?php

namespace controllers;
use models\Book;

class BookController {

    private $bookModel;

    public function __construct(Book $book)
    {
        $this->bookModel = new $book;
    }

    public function createBook($isbn, $title, $author, $genre, $editorial, $image){
        $this->bookModel->isbn = $isbn;
        $this->bookModel->title = $title;
        $this->bookModel->author = $author;
        $this->bookModel->genre = $genre;
        $this->bookModel->editorial = $editorial;
        $this->bookModel->image = $image;

        if ($this->bookModel->createBook())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function readAllBooks()
    {
        return $this->bookModel->getBooks();
    }

    public function readBook($isbn)
    {
        $this->bookModel->isbn = $isbn;
        return $this->bookModel->searchBooksByIsbn();
    }

    public function updateBook($id_book, $isbn, $title, $author, $genre, $editorial, $image)
    {
        $this->bookModel->id_book = $id_book;
        $this->bookModel->isbn = $isbn;
        $this->bookModel->title = $title;
        $this->bookModel->author = $author;
        $this->bookModel->genre = $genre;
        $this->bookModel->editorial = $editorial;
        $this->bookModel->image = $image;

        if ($this->bookModel->updateBook($id_book, $isbn, $title, $author, $genre, $editorial, $image))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function deleteBook($isbn)
    {
        if ($this->bookModel->deleteBook($isbn))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

	public function getTop50Books()
	{
		return $this->bookModel->getTop50Books();
	}

    public function getLast10Books()
    {
        return $this->bookModel->getLast10Books();
    }

    public function getBooksByGenre($genre)
    {
        return $this->bookModel->searchBooksByGenre($genre);
    }

    public function searchBooks($search)
    {
        return $this->bookModel->searchBooks($search);
    }

    public function getActivity(){
        return $this->bookModel->getActivity();        
    }

}