<?php
session_start();
require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";
require_once "../app/models/Book.php";
require_once "../app/models/List.php";
require_once "../app/controllers/ListController.php";
require_once "../app/controllers/BookController.php";
require_once "../app/models/UserFollowLists.php";
require_once "../app/models/BookInList.php";
require_once "../app/models/test_book.php";
require_once "../app/models/Followers.php";
require_once "../app/models/Review.php";
require_once "../app/controllers/ReviewController.php";

if (!isset($_SESSION['userData'])) {
	header('Location: login');
	exit();
}

$user = new models\User();
$book = new models\Book();
$list = new models\ListModel();
$bookController = new controllers\BookController($book);
$userController = new controllers\UserController($user);
$listController = new controllers\ListController($list);
$UFLController = new models\UFLModel();
$BILController = new models\BILModel();
$testbookCon = new models\Booktest();
$UFUController = new models\Followersmodel();
$reviewsController = new models\Review();

$profileMode = false;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
		$profileMode = true;
        $user_ID = $_GET['id'];
        $section = $_GET['section'] ?? '';

        $FLInfo = [];
        $userInfo = $userController->readByUserId($user_ID);
        $pLists = $listController->getUserBasicLists($user_ID);
        $createdLists = $listController->getUserTopLists($user_ID);
        $followedLists = $UFLController->getFollowedLists($user_ID);
        $followedUsers = $UFUController->getFollowedUsers($user_ID);
        $followers = $UFUController->getFollowers($user_ID);
        $reviews = $reviewsController->getUserReviews($user_ID);

		if (isset($_SESSION['userData']) && $_SESSION['userData']['id_user'] != $user_ID) {
			$reviews = array_filter($reviews, function($review) {
				return $review['visibility'] == 'public';
			});

			$createdLists = array_filter($createdLists, function($list) {
				return $list['visibility'] == 'public';
			});
		}
		
    }
	else if (isset($_GET['search'])) {
		$search = $_GET['search'];
		if (empty($search)) {
			if (!isset($_SESSION['userData'])) {
				header('Location: login');
			}
			else {
				header('Location: profile?id='.$_SESSION['userData']['id_user']);
			}
			exit();
		}
		$foundusers = $userController->searchUserByName($search);
	}
	else {
        header('Location: lists');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['userData'])) {
        echo "Error: no user session found.";
        exit();
    }
    if (isset($_POST['follow_user'])) {
        $id_user = $_POST['id_user'];
        $UFUController->followUser($_SESSION['userData']['id_user'], $id_user);
        header("Location: profile?id=$id_user");
        exit();
    }
    if (isset($_POST['unfollow_user'])) {
        $id_user = $_POST['id_user'];
        $UFUController->unfollowUser($_SESSION['userData']['id_user'], $id_user);
        header("Location: profile?id=$id_user");
        exit();
    }
}
?>

<?php include 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="md:mx-auto">
		<div class="justify-center justify-items-end p-4 flex gap-96">
			<form action="profile" method="GET" class="flex items-center gap-5 w-80 pb-1 border-b-2 border-primary">
				<input type="text" name="search" class="w-full py-2 bg-transparent outline-none" placeholder="Busca usuario">
				<button type="submit"><img src="img/lupa.svg" alt=""></button>
			</form>
			<button class="w-40 p-2 bg-accent font-semibold rounded-md" onclick="window.location.href = 'logout';">Cerrar sesión</button>
		</div>
		<?php if ($profileMode): ?>
			<?php if ($userController->readByUserID($user_ID) === false): ?>
				<p class="text-3xl text-center text-black font-bold">Usuario no encontrado</p>
			<?php else: ?>
				<div class="flex gap-12 justify-center">
					<!-- User info and actions -->
					<aside class="flex flex-col items-center gap-5 w-80 border border-borderGrey px-10 py-12">
						<img src="<?= !empty($userInfo['profile_image']) ? htmlspecialchars($userInfo['profile_image']) : 'img/maestro.svg' ?>" alt="<?= htmlspecialchars($userInfo['username']) ?>" class="w-56 h-56 object-image rounded-lg">
						<p class="text-center text-3xl text-black font-bold"><?= htmlspecialchars($userInfo['username']) ?></p>
						<?php if (isset($_SESSION['userData']) && $_SESSION['userData']['id_user'] == $user_ID): ?>
							<button class="w-4/5 p-2 bg-accent font-semibold rounded-md" onclick="window.location.href = 'modify_account';">Editar perfil</button>
						<?php elseif (!isset($_SESSION['userData'])): ?>
							<button class="w-full bg-green-500 font-semibold rounded-md" onclick="window.location.href = 'login';">Seguir</button>
						<?php else: ?>
							<?php if ($UFUController->isFollowing($_SESSION['userData']['id_user'], $user_ID)): ?>
								<form action="profile" method="POST">
									<input type="hidden" name="id_user" value="<?= $user_ID ?>">
									<button type="submit" name="unfollow_user" class="p-2 bg-red-500 text-white font-semibold rounded-md">Dejar de seguir</button>
								</form>
							<?php else: ?>
								<form action="profile" method="POST">
									<input type="hidden" name="id_user" value="<?= $user_ID ?>">
									<button type="submit" name="follow_user" class="p-2 bg-green-500 text-white font-semibold rounded-md">Seguir</button>
								</form>
							<?php endif; ?>
						<?php endif; ?>
						<div class="container">
							<div class="bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg w-full grid grid-cols-2 auto-rows-auto">
								<div class="p-4 flex flex-col items-center">
									<a href="profile?id=<?= $user_ID ?>&section=followed_users" class="text-xl text-black-700 font-bold"><?= count($followedUsers) ?></a>
									<a href="profile?id=<?= $user_ID ?>&section=followed_users" class="text-sm text-black-700 font-semibold">seguidos</a>
								</div>
								<div class="p-4 flex flex-col items-center">
									<a href="profile?id=<?= $user_ID ?>&section=followers" class="text-xl text-black-700 font-bold"><?= count($followers) ?></a>
									<a href="profile?id=<?= $user_ID ?>&section=followers" class="text-sm text-black-700 font-semibold">seguidores</a>
								</div>
								<div class="p-4 flex flex-col items-center">
									<a href="profile?id=<?= $user_ID ?>&section=listas" class="text-xl text-black-700 font-bold">
										<?php if ($createdLists): ?>
											<?= count($createdLists) ?>
										<?php else: ?>
											0
										<?php endif; ?>
									</a>
									<a href="profile?id=<?= $user_ID ?>&section=listas" class="text-sm text-black-700 font-semibold">listas</a>
								</div>
								<div class="p-4 flex flex-col items-center">
									<a href="profile?id=<?= $user_ID ?>&section=reviews" class="text-xl text-black-700 font-bold">
										<?php if ($reviews): ?>
											<?= count($reviews) ?>
										<?php else: ?>
											0
										<?php endif; ?>
									</a>
									<a href="profile?id=<?= $user_ID ?>&section=reviews" class="text-sm text-black-700 font-semibold">reseñas</a>
								</div>
							</div>
						</div>
					</aside>
					<section class="border border-borderGrey w-2/4 px-10 py-12">
						<?php if (!$section): ?>
							<div class="flex items-center">
								<a class="text-3xl font-bold text-gray-900 underline mr-2">Colección</a>
								<span class="text-3xl font-bold text-gray-900">(<?= count($pLists) ?>)</span>
							</div>
							<?php if (count($pLists) > 0) : ?>
								<div class="gap-4 mt-6 grid grid-cols-4 pl-2">
									<?php foreach ($pLists as $list) : ?>
										<div class="flex flex-col gap-1 bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg w-32">
											<a href="list?id=<?= $list['id_list'] ?>" class="text-lg font-extrabold text-gray-900"><?= $list['list_name'] ?></a>
											<div class="flex items-center gap-2 my-2 ml-1">
												<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
												<p class="text-sm text-black font-semibold"><?= $list['BILCount'] ?></p>
											</div> 
										</div>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<p class="text-lg text-gray-900">Colección privada.</p>
							<?php endif; ?>

							<div class="grid grid-cols-3 mt-6">
								<div class="flex items-center">
									<a href="profile?id=<?= $user_ID ?>&section=listas" class="text-3xl font-bold text-gray-900 underline mr-2">Listas</a>
									<span class="text-3xl font-bold text-gray-900">
											<?php if ($createdLists): ?>
												<?= "(" . count($createdLists) . ")"?>
											<?php else: ?>
												<?= "(0)" ?>
											<?php endif; ?>
									</span>
								</div>
								<?php if (isset($_SESSION['userData']) && $_SESSION['userData']['id_user'] == $user_ID): ?>
									<button class="w-fit h-max-2 p-4 bg-primary text-background font-semibold rounded-md justify-end" onclick="window.location.href = 'create_list';">Crear lista</button>
								<?php endif; ?>
							</div>
							<?php if ($createdLists && count($createdLists) > 0) : ?>
								<div class="flex gap-4 mt-4 pl-2">
									<?php foreach (array_slice($createdLists, 0, 2) as $list) : ?>
										<div class="flex gap-1 bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg p-2">
											<div class="flex items-center">
												<?php if (!empty($list['book_image'])) : ?>
													<img src="<?= $list['book_image'] ?>" alt="book cover" class="w-20 h-auto rounded-lg">
												<?php else : ?>
													<div class="w-20 h-auto rounded-lg bg-gray-200 flex items-center justify-center">No hay imagen disponible</div>
												<?php endif; ?>
											</div>
											<div class="flex flex-col">
												<a href="list?id=<?= $list['id_list'] ?>" class="text-lg font-extrabold text-gray-900"><?= $list['list_name'] ?></a>
												<div class="flex items-center gap-2 my-1 ml-1">
													<img src="img/followers.svg" alt="followers" class="w-4 h-4">
													<p class="text-sm text-black font-semibold"><?= $list['followersNum'] ?></p>
												</div>
												<div class="flex items-center gap-2 my-1 ml-1">
													<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
													<p class="text-sm text-black font-semibold"><?= $list['BILCount'] ?></p>
												</div>
												<div class="flex items-center gap-2 my-1 ml-1">
													<p class="text-sm text-black font-semibold"><?= $list['list_description'] ?></p>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<p class="text-lg text-gray-900">Este usuario no tiene listas públicas.</p>
							<?php endif; ?>
							<div class="flex items-center mt-6">
								<a href="profile?id=<?= $user_ID ?>&section=seguidas" class="text-3xl font-bold text-gray-900 underline mr-2">Listas seguidas</a>
								<span class="text-3xl font-bold text-gray-900">(<?= count($followedLists) ?>)</span>
							</div>
							<?php if (count($followedLists) > 0) : ?>
								<div class="flex gap-4 mt-6 pl-2">
									<?php foreach (array_slice($followedLists, 0, 2) as $list) : ?>
										<div class="flex gap-4 bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg p-2">
											<?php if (!empty($list['list_pic'])) : ?>
												<img src="<?= $list['list_pic'] ?>" alt="book cover" class="w-20 h-auto rounded-lg">
											<?php else : ?>
												<!-- Aquí puedes agregar un marcador de posición o mensaje si no hay imagen -->
												<div class="w-20 h-auto rounded-lg bg-gray-200 flex items-center justify-center">No hay imagen disponible</div>
											<?php endif; ?>
											<div class="flex flex-col">
												<a href="list?id=<?= $list['id_list'] ?>" class="text-lg font-extrabold text-gray-900"><?= $list['list_name'] ?></a>
												<div class="flex items-center gap-2 my-1 ml-1">
													<img src="img/users.svg" alt="user" class="w-4 h-4">
													<a href="profile?id=<?= $list['id_user'] ?>" class="text-sm text-black font-semibold"><?= $list['ownerName'] ?></a>
												</div>
												<div class="flex items-center gap-2 my-1 ml-1">
													<img src="img/followers.svg" alt="followers" class="w-4 h-4">
													<p class="text-sm text-black font-semibold"><?= $list['followersNum'] ?></p>
												</div>
												<div class="flex items-center gap-2 my-1 ml-1">
													<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
													<p class="text-sm text-black font-semibold"><?= $list['BILCount'] ?></p>
												</div>
												<p class="text-sm text-black font-semibold"><?= $list['list_description'] ?></p>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<p class="text-lg text-gray-900">Este usuario no sigue ninguna lista.</p>
							<?php endif; ?>
							<div class="flex items-center mt-6">
								<a href="profile?id=<?= $user_ID ?>&section=reviews" class="text-3xl font-bold text-gray-900 underline mr-2">Reseñas</a>
								<span class="text-3xl font-bold text-gray-900">(<?= $reviews && count($reviews) > 0 ? count($reviews) : "0" ?>)</span>
							</div>
							<?php if ($reviews && count($reviews) > 0) : ?>
								<div class="flex gap-4 mt-6 pl-2">
									<?php foreach (array_slice($reviews, 0, 2) as $review) : ?>
										<div class="flex flex-col gap-1 border border-borderGrey p-2">
											<h1 class="text-lg font-extrabold text-gray-900"><?= $review['book_title'] ?></h1>
											<div class="flex items-left gap-1">
												<p class="text-sm text-black font-semibold"><?= $review['rating'] ?></p>
												<img src="img/star.svg" alt="star" class="w-4 h-4">
											</div>
											<p class="text-sm text-black font-semibold"><?= $review['comment'] ?></p>
										</div>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<p class="text-lg text-gray-900">Este usuario no ha hecho ninguna reseña.</p>
							<?php endif; ?> 
						<?php else: ?>
							<form action="profile" method="GET" class="inline">
								<input type="hidden" name="id" value="<?= $user_ID ?>">
								<button type="submit" class="px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:bg-red-600">
									Volver
								</button>
							</form>
							<?php if ($section === 'listas'): ?>
								<?php include 'profile_derivadas/profile_lists.php'; ?>
							<?php elseif ($section === 'seguidas'): ?>
								<?php include 'profile_derivadas/profile_followed_lists.php'; ?>
							<?php elseif ($section === 'reviews'): ?>
								<?php include 'profile_derivadas/profile_reviews.php'; ?>
							<!-- Igual podemos colar ver seguidores y seguidos? -->
							<?php elseif ($section === 'followers'): ?>
								<?php include 'profile_derivadas/profile_followers.php'; ?>
							<?php elseif ($section === 'followed_users'): ?>
								<?php include 'profile_derivadas/profile_followed_users.php'; ?>
							<?php else: ?>
								<p class="text-3xl text-center text-black font-bold">Sección no encontrada</p>
							<?php endif; ?>
						<?php endif; ?>
					</section>
				</div>
			<?php endif; ?>
		<?php else: ?>
			<?php include 'profile_derivadas/profile_search.php'; ?>
		<?php endif; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
