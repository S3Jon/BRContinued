<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/controllers/BookController.php';
require_once '../app/models/Book.php';
require_once '../app/controllers/ReviewController.php';
require_once '../app/models/Review.php';
require_once '../app/models/BookInList.php';

session_start();
if (!isset($_SESSION['userData'])) {
    header('Location: login');
    exit;
}

$bookController = new controllers\BookController(new models\Book());
$reviewController = new controllers\ReviewController(new models\Review());
$userController = new controllers\UserController(new models\User());
$BILController = new models\BILModel();

$book = $bookController->readBook($_GET['isbn']);
$reviews = $reviewController->getReviews($_GET['isbn']);
if (isset($_SESSION['userData'])){
	$bookInList = $BILController->getUserListsWithBookStatus($_SESSION['userData']['id_user'], $_GET['isbn']);
	foreach ($bookInList as $list)
	{
		if ($list['type'] === 'favorite')
		{
			$favorite = $list;
			break;
		}
	}
	/* var_dump($bookInList);
	if (isset($favorite))
	{
		var_dump($favorite);
	} */
}

//reviws visibility control 
$reviews = array_filter($reviews, function ($review) {
    if ($review['visibility'] === 'private' && $review['id_user'] === $_SESSION['userData']['id_user']) {
        return true;
    }
    return $review['visibility'] === 'public';
});

$canReview = true;
$currentUserId = $_SESSION['userData']['id_user'];

foreach ($reviews as $review) {
    if ($review['id_user'] == $currentUserId) {
        $canReview = false;
        break;
    }
}

$title = isset($book['title']) ? $book['title'] : 'Título no disponible';
$author = isset($book['author']) ? $book['author'] : 'Autor no disponible';
$description = isset($book['description']) ? $book['description'] : 'Descripción no disponible';
$image = isset($book['image']) ? $book['image'] : 'uploads/book_placeholder.jpg';
$genres = json_decode(isset($book['genre'])) ? json_decode($book['genre']) : [];
$reviews = isset($reviews) ? $reviews : [];
$totalReviews = count($reviews);

$rating = 0;

if (count($reviews) > 0) {
    $totalRating = 0;
    foreach ($reviews as $review) {
        $totalRating += $review['rating'];
    }
    $rating = number_format($totalRating / count($reviews), 2);
}

// Ordenar reviws por fecha 
$currentUserReview = null;
$otherReviews = [];

// Separar la reseña del usuario actual del resto
foreach ($reviews as $key => $review) {
    if ($review['id_user'] === $currentUserId) {
        $currentUserReview = $review;
    } else {
        $otherReviews[] = $review;
    }
}

// Ordenar las demás reseñas por fecha
usort($otherReviews, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Colocar la reseña del usuario actual al inicio, si existe
if ($currentUserReview) {
    array_unshift($otherReviews, $currentUserReview);
}

// Actualizar la variable $reviews con el nuevo orden
$reviews = $otherReviews;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $data = [
        'id_user' => $_SESSION['userData']['id_user'],
        'isbn' => $_GET['isbn'],
        'rating' => isset($_POST['rate']) ? $_POST['rate'] : 1,
        'comment' => $_POST['comment'],
        'visibility' => $_POST['review']
    ];

    if ($reviewController->createReview($data)) {
        header('Location: book?isbn=' . $_GET['isbn']);
        exit;
    } else {
        // echo 'Error al publicar la reseña';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_review'])) {
    $data = [
        'id_review' => $_POST['id_review'],
        'comment' => $_POST['comment'],
        'rating' => $_POST['rate'],
        'visibility' => $_POST['review']
    ];

    if ($reviewController->updateReview($data)) {
        header('Location: book?isbn=' . $_GET['isbn']);
        exit;
    } else {
        // echo 'Error al actualizar la reseña';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review'])) {
    echo 'delete';
    $data = [
        'id_review' => $_POST['id_review']
    ];

    if ($reviewController->deleteReview($data)) {
        header('Location: book?isbn=' . $_GET['isbn']);
        exit;
    } else {
        // echo 'Error al eliminar la reseña';
    }
}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_favourite'])) {
		$favorite = $_POST['favourite_list'];
		$isbn = $_GET['isbn'];
		if ($BILController->toggleBookInlist($favorite, $isbn)) {
			header('Location: book?isbn=' . $isbn);
			exit;
		} else {
			echo 'Error al togglear favorito';
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_list'])) {
    $listIds = $_POST['lists'];
    $isbn = $_GET['isbn'];
    foreach ($listIds as $listId) {
        $BILController->addBook($listId, $isbn);
    }
    header('Location: book?isbn=' . $isbn);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $idList = $_POST['idList'];
    $isbn = $_POST['isbn'];

    if ($action === 'add') {
        $BILController->addBook($idList, $isbn);
    } elseif ($action === 'remove') {
        $BILController->removeBook($idList, $isbn);
    }
}

?>

<?php include_once 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="md:mx-auto">
        <div class="flex gap-12 justify-center">
            <!-- Book image and actions -->
            <aside class="flex flex-col items-center gap-5 w-56">
                <img src="<?= $image ?>" alt="<?= $title ?>" class="w-56 h-80 object-image rounded-lg">
				<form action="" class="w-4/5" method="POST">
					<input type="hidden" name="favourite_list" value="<?= $favorite['id_list'] ?>">                    
                    <button type="submit" name="toggle_favourite" id="toggle_favourite" class="w-full p-2 text-white font-semibold rounded-md <?= $favorite['status'] ? 'bg-red-400' : 'bg-accent' ?>" class="bg-primary text-background font-semibold py-2 rounded-md w-full"><?= $favorite['status'] ? 'Quitar de favoritos' : 'Añadir a favoritos' ?></button>
				</form>
                <button id="addToListBtn" class="w-4/5 p-2 bg-primary text-background font-semibold rounded-md">Añadir a una lista</button>
            </aside>
            <!-- Book details -->
            <section class="border border-borderGrey w-2/4 px-10 py-12">
                <header class="flex justify-between">
                    <div class="flex flex-col gap-1">
                        <h1 class="font-extrabold font-serif4 text-3xl"><?= $title ?></h1>
                        <p><?= $author ?></p>
                    </div>
                    <div class="flex flex-col items-end">
                        <div class="flex items-center gap-2 ">
                            <img src="img/star.svg" alt="">
                            <span class="pt-1 text-3xl font-bold"><?= $rating ?></span>
                        </div>
                        <p class="mr-[2px]"><?= $totalReviews ?> votos</p>
                    </div>
                </header>
                <div class="flex gap-4 mt-6">
                    <?php if (count($genres) > 0) : ?>
                        <?php foreach ($genres as $genre) : ?>
                            <a href="explore?genre=<?= $genre ?>" class="px-[12px] py-[7px] border-2 font-semibold text-[12px] border-primary rounded-[25px] cursor-pointer"><?= $genre ?></a>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <span class="text-black rounded-lg">Sin género</span>
                    <?php endif; ?>
                </div>
                <p class="mt-8"><?= $description ?></p>

                <!-- Add Review -->
                <?php if($canReview) : ?>
                    <section class="mt-10 bg-[rgba(36,38,51,0.15)] px-10 py-7 rounded-lg">
                        <form action="" class="flex flex-col gap-7" method="POST">
                            <div class="flex items-center gap-3">
                                <p class="mt-1">Tu voto:</p>
                                <div class="flex flex-row-reverse gap-3 rating-container">
                                    <input type="radio" id="star5" name="rate" value="5" hidden />
                                    <label for="star5" title="5 star"><img class="cursor-pointer" src="img/star2.svg" alt="5 star"></label>
                                    <input type="radio" id="star4" name="rate" value="4" hidden/>
                                    <label for="star4" title="4 stars"><img class="cursor-pointer" src="img/star2.svg" alt="3 stars"></label>
                                    <input type="radio" id="star3" name="rate" value="3" hidden/>
                                    <label for="star3" title="3 stars"><img class="cursor-pointer" src="img/star2.svg" alt="3 stars"></label>
                                    <input type="radio" id="star2" name="rate" value="2" hidden/>
                                    <label for="star2" title="2 stars"><img class="cursor-pointer" src="img/star2.svg" alt="2 stars"></label>
                                    <input type="radio" id="star1" name="rate" value="1" hidden/>
                                    <label for="star1" title="1 stars"><img class="cursor-pointer" src="img/star2.svg" alt="1 stars"></label>
                                </div>
                            </div>
                            <textarea name="comment" id="comment" placeholder="Si lo deseas, escribe aquí tu reseña" cols="5" rows="4" class="rounded-lg p-3"></textarea>
                            <div class="flex justify-between">
                                <div class="flex items-center gap-2">
                                    <label for="private" class="cursor-pointer">Reseña privada</label>
                                    <input type="radio" id="private" name="review" value="private" class="cursor-pointer mr-10 mt-[2px] w-5 h-5" checked />
                                    <label for="public" class="cursor-pointer">Reseña pública</label>
                                    <input type="radio" id="public" name="review" value="public" class="cursor-pointer mt-[2px] w-5 h-5" />
                                </div>
                                <button type="submit" name="submit" class="bg-accent text-black font-bold py-2 px-6 rounded-lg">Publicar</button>
                            </div>
                        </form>
                    </section>
                <?php endif; ?>

                <!-- Reviews -->
                <section class="mt-10">
                    <h2><?= count($reviews) > 1 ? count($reviews) . ' reseñas de usuarios' : count($reviews) . ' reseña de usuario' ?></h2>
                    <?php if (count($reviews) > 0) : ?>
                        <?php foreach ($reviews as $review) : ?>
                            <div class="my-3 border-t border-t-borderGrey p-6 last:pb-0">
                                <div class="flex justify-between">
                                    <div class="flex gap-3">
                                        <img src="<?= $userController->readByUserID($review['id_user'])['profile_image'] ?>" alt="user" class="w-10">
                                        <div class="flex flex-col">
                                            <a href="profile?id=<?= $review['id_user'] ?>" class="font-bold"><?=
                                                $userController->readByUserID($review['id_user'])['username']
                                            ?></a>
                                            <p class="text-sm"><?= $review['created_at'] ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <img src="img/star.svg" alt="rating" class="w-5">
                                        <span class="font-bold pt-1"><?= isset($review['rating']) && $review['rating'] !== null ? $review['rating'] . '/5' : '1/5' ?></span>
                                    </div>
                                </div>
                                <?php if ($review['id_user'] === $currentUserId): ?>
                                    <form action="" method="POST" class="mt-4">
                                        <input type="hidden" name="id_review" value="<?= $review['id_review'] ?>">
                                        <div class="flex items-center gap-3 mb-5">
                                            <p class="mt-1">Tu voto:</p>
                                            <div class="flex flex-row-reverse gap-3 rating-container">
                                                <input type="radio" id="star5" name="rate" value="5" hidden <?= $review['rating'] == 5 ? 'checked' : '' ?> />
                                                <label for="star5" title="5 star"><img class="cursor-pointer" src="img/star2.svg" alt="5 star"></label>
                                                <input type="radio" id="star4" name="rate" value="4" hidden <?= $review['rating'] == 4 ? 'checked' : '' ?> />
                                                <label for="star4" title="4 stars"><img class="cursor-pointer" src="img/star2.svg" alt="4 stars"></label>
                                                <input type="radio" id="star3" name="rate" value="3" hidden <?= $review['rating'] == 3 ? 'checked' : '' ?> />
                                                <label for="star3" title="3 stars"><img class="cursor-pointer" src="img/star2.svg" alt="3 stars"></label>
                                                <input type="radio" id="star2" name="rate" value="2" hidden <?= $review['rating'] == 2 ? 'checked' : '' ?> />
                                                <label for="star2" title="2 stars"><img class="cursor-pointer" src="img/star2.svg" alt="2 stars"></label>
                                                <input type="radio" id="star1" name="rate" value="1" hidden <?= $review['rating'] == 1 ? 'checked' : '' ?> />
                                                <label for="star1" title="1 star"><img class="cursor-pointer" src="img/star2.svg" alt="1 star"></label>
                                            </div>
                                        </div>
                                        <textarea name="comment" class="w-full border border-gray-300 p-2"><?= $review['comment'] ?></textarea>
                                        <div class="flex justify-between">
                                            <div class="flex items-center gap-2">
                                                <label for="private" class="cursor-pointer">Reseña privada</label>
                                                <input type="radio" id="private" name="review" value="private" class="cursor-pointer mr-10 mt-[2px] w-5 h-5" <?= $review['visibility'] == 'private' ? 'checked' : '' ?> />
                                                <label for="public" class="cursor-pointer">Reseña pública</label>
                                                <input type="radio" id="public" name="review" value="public" class="cursor-pointer mt-[2px] w-5 h-5" <?= $review['visibility'] == 'public' ? 'checked' : '' ?> />
                                            </div>
                                            <div class="flex gap-4">
                                                <button type="submit" name="update_review" class="mt-2 px-4 py-2 bg-blue-400 text-white rounded">Editar</button>
                                                <button type="submit" name="delete_review" class="mt-2 px-4 py-2 bg-red-400 text-white rounded">Eliminar</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <p class="mt-4"><?= htmlspecialchars($review['comment']) ?></p>
                                <?php endif; ?>
                            </div>                        
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="mt-6">No hay reseñas para mostrar.</p>
                    <?php endif; ?>
                </section>
            </section>
        </div>
    </div>
</div>

<div id="myModal" class="fixed inset-0 z-50 items-center flex justify-center hidden">
  <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
  <div class="modal-container bg-white w-5/6 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
    <div class="modal-close absolute top-0 right-0 cursor-pointer flex flex-col items-center mt-4 mr-4 text-white text-sm z-50">
      <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
        <path d="M10.589 9.293l4.95-4.95a1 1 0 1 0-1.414-1.414l-4.95 4.95-4.95-4.95A1 1 0 1 0 4.05 4.95l4.95 4.95-4.95 4.95a1 1 0 1 0 1.414 1.414l4.95-4.95 4.95 4.95a1 1 0 1 0 1.414-1.414l-4.95-4.95z"/>
      </svg>
      <span class="text-sm">(Esc)</span>
    </div>
    <div class="modal-content py-4 text-left px-6">
      <form  action="" method="post" id="addToListForm" class="flex flex-col gap-4">
        <?php foreach ($bookInList as $list) : ?>
            <div class="flex items-center gap-2" id="listCheckbox">
              <input type="checkbox" name="lists[]" value="<?= $list['id_list'] ?>" id="<?= $list['id_list'] ?>" <?= $list['status'] ? 'checked' : '' ?>>
              <label for="<?= $list['id_list'] ?>"><?= $list['list_name'] ?></label>
            </div>
        <?php endforeach; ?>
        <!-- <button type="submit" name="add_to_list" class="bg-primary text-background font-semibold py-2 rounded-md">Añadir a la lista</button> -->
      </form>
    </div>
  </div>
</div>



<?php include_once 'partials/footer.php'; ?>


<script>
document.addEventListener('DOMContentLoaded', function () {
  const addToListBtn = document.getElementById('addToListBtn');
  const modal = document.getElementById('myModal');
  const closeBtn = document.querySelector('.modal-close');
  const addToListForm = document.getElementById('addToListForm');

  addToListBtn.addEventListener('click', function () {
    modal.classList.remove('hidden');
    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', outsideClick);
    document.body.classList.add('modal-active');
  });

  function closeModal() {
    modal.classList.add('hidden');
    closeBtn.removeEventListener('click', closeModal);
    window.removeEventListener('click', outsideClick);
    document.body.classList.remove('modal-active');
  }

  function outsideClick(e) {
    if (e.target === modal) {
      closeModal();
    }
  }

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeModal();
    }
  });

    const listCheckboxes = document.querySelectorAll('#listCheckbox input[type="checkbox"]');
    listCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const idList = this.value;
            const isbn = '<?= $_GET['isbn'] ?>';

            let action = this.checked ? 'add' : 'remove';

            if (idList === '<?= $favorite['id_list'] ?>') {
                const toggleFavouriteBtn = document.getElementById('toggle_favourite');
                toggleFavouriteBtn.innerText = this.checked ? 'Quitar de favoritos' : 'Añadir a favoritos';
                toggleFavouriteBtn.classList.toggle('bg-red-400');
                toggleFavouriteBtn.classList.toggle('bg-accent');
            }

            fetch('book', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: action,
                    idList: idList,
                    isbn: isbn
                })
            })
            .then(response => response.text())
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

    
});
</script>