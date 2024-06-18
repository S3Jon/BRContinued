<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';
require_once '../app/controllers/ListController.php';
require_once '../app/models/List.php';
//TODO: Modificar en el futuro;  fase test
require_once '../app/models/test_book.php';
require_once '../app/models/BookInList.php';
require_once '../app/models/UserFollowLists.php';
//En el futuro incluir controlador/modelo de libros

session_start();

if (!isset($_SESSION['userData'])) {
	header('Location: login');
	exit;
}

$listController = new controllers\ListController(new models\ListModel());
$userController = new controllers\UserController(new models\User());
$bookController = new models\Booktest();
$BILController = new models\BILModel();
$UFLController = new models\UFLModel();



//get
$listOS = false;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if (isset($_GET['id'])) {
		$id_list = $_GET['id'];
		$searchQuery = $_GET['search'] ?? '';
		
		$listaInfo = $listController->getListById($id_list);
		if (!$listaInfo) {
			header('Location: lists');
			exit;
		}
		if ($searchQuery) {
			$BILInfo = $BILController->searchBookInList($id_list, $searchQuery);
		} else {
			$BILInfo = $BILController->getListBooksInfo($id_list);
		}
		$nombreLista = isset($listaInfo['list_name']) ? $listaInfo['list_name'] : 'Error al cargar el titulo';
		$propietarioLista = $userController->getUsernameById($listaInfo['id_user']);
		$visibilidadLista = isset($listaInfo['visibility']) ? $listaInfo['visibility'] : 'Error al cargar la visibilidad';

		//Medida extra de seguridad: Si la lista no es pública y no tienes sesión/tu id de user no coincide, no puedes ver la lista
		$listOS = false; //List OwnerShip
		if (isset($_SESSION['userData']) && ($_SESSION['userData']['id_user'] == $listaInfo['id_user']))
		{
			$listOS = true;
		}
	}
	else {
		//header('Location: lists');
		//exit;
	}
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$id_list = $_POST['id_list'];
	if (isset($_POST['delete_book'])) {
		$isbn = $_POST['isbn'];
		if ($BILController->removeBook($id_list, $isbn)) {
			header("Location: list?id=$id_list");
		} else {
			echo "Error al eliminar el libro.";
		}
	}
	if (isset($_POST['delete_list'])) {
		if ($listController->deleteList($id_list)) {
			$upl = $_SESSION['userData']['id_user'];
			header("Location: profile?id=$upl");
			exit;
		} else {
			echo "Error al eliminar la lista.";
		}
	}
	else if (isset($_POST['follow_list'])) {
		if (!isset($_SESSION['userData'])) //Por redundancia, aunque no debería pasar
		{
			header('Location: login');

		}
		else
		{
			$UFLController->followList($_SESSION['userData']['id_user'], $id_list);
			header("Location: list?id=$id_list");
		}
	}
	else if (isset($_POST['unfollow_list'])) {
		$UFLController->unfollowList($_SESSION['userData']['id_user'], $id_list);
		header("Location: list?id=$id_list");
	}
}

?>

<?php include_once 'partials/header.php'; ?>

<?php if ($visibilidadLista != "public" && !$listOS) { include 'list_derivadas/list_forbidden.php'; }
else { ?>
	<div class="my-14 container mx-auto min-h-screen">
		<div class="w-3/4 mx-auto">
			<div class="flex justify-between">
				<div class="w=2/6">
					<h1 class="text-3xl font-bold text-gray-900">Explorar listas creadas por la comunidad</h1>
				</div>
				<!-- absolutamente robado parte 365 -->
				<div class="w-2/3 md:mx-auto">
					<div class="flex items-center w-full gap-20">
					<form action="list" method="GET" class="flex items-center gap-5 w-full pb-1 border-b-2 border-primary">
						<input type="hidden" name="id" value="<?= htmlspecialchars($id_list) ?>">
						<input type="text" name="search" class="w-full py-2 bg-transparent outline-none" placeholder="Busca libro">
						<button type="submit"><img src="img/lupa.svg" alt=""></button>
					</form>
					</div>
				</div>
			</div>
			<div class="flex gap-12 justify-center pt-8">
				<aside>
					<div class="bg-white shadow-md rounded-lg max-w-sm w-full">
						<div class="p-4">
							<?php include 'list_derivadas/list_info.php'; ?>
						</div>
					</div>
				</aside>
				<section class="pt-8 px-10 py-12 w-5/6">
					<?php if ($searchQuery): ?>
						<div>
							<h2 class="text-lg font-semibold text-gray-900">Resultados de la búsqueda</h2>
							<p class="text-sm text-gray-600">Resultados para: <?= htmlspecialchars($searchQuery) ?></p>
							<button onclick="window.location.href = 'list?id=<?= $id_list ?>'" class="px-2 py-1 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:bg-primary-dark">Mostrar todos los libros</button>
						</div>
					<?php endif; ?>
					<div class ="grid grid-cols-2 justify-items-start gap-4">
					<?php foreach ($BILInfo as $key => $book): ?>
						<div class="container">
							<div class="flex border border-borderGrey rounded-lg p-2">
								<!-- Left column for the book cover -->
								<div class="flex-shrink-0">
									<img src="<?= htmlspecialchars($book['image'] ?? 'Public/image/default_cover.svg') ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="w-20 h-auto rounded-lg">
								</div>
								<!-- Right column for the book details -->
								<div class="flex flex-col gap-1 p-2">
									<a href="book?isbn=<?= $book['isbn'] ?>" class="text-lg font-extrabold text-gray-900"><?= htmlspecialchars($book['title']) ?></a>
									<p class="text-sm text-gray-600"><?= htmlspecialchars($book['author']) ?></p>
									<p class="text-sm text-gray-600">
										<?php
										$genres = json_decode($book['genre'], true);
										if (json_last_error() === JSON_ERROR_NONE) {
											echo htmlspecialchars(implode(', ', $genres));
										} else {
											echo htmlspecialchars($book['genre']);
										}
										?>
									</p>
									<div class="flex items-center gap-2 my-2 ml-1">
										<?php if ($book['average_rating'] > 0): ?>
											<p class="text-sm text-gray-600"><?= number_format($book['average_rating'], 1) ?></p>
											<img src="img/star.svg" alt="star" class="w-4 h-4">
										<?php else: ?>
											<p class="text-sm text-gray-600">Sin reseñas</p>
										<?php endif; ?>
									</div>
									<?php if ($listOS): ?>
										<form action="list" method="POST">
											<input type="hidden" name="id_list" value="<?= $id_list ?>">
											<input type="hidden" name="isbn" value="<?= $book['isbn'] ?>">
											<button type="submit" onclick="return confirm('¿Seguro que quieres eliminar este libro?');" name="delete_book" class="px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:bg-red-600">Eliminar libro</button>
										</form>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
					</div>
				</section>
			</div>
		</div>
	</div>
<?php } ?>

<?php include_once 'partials/footer.php'; ?>

<script>
	function reset() {
		const element = document.getElementById('book' + isbn);
		element.parentNode.removeChild(element);
	}
</script>