<?php

require_once "../app/models/List.php";
require_once "../app/controllers/ListController.php";
require_once "../app/models/User.php";
require_once "../app/controllers/UserController.php";
require_once "../app/models/UserFollowLists.php";
//TODO- Actualizar fuente de BooksInList
require_once "../app/models/BookInList.php";
include_once "../app/helpers/format_followers.php";

session_start();

if (!isset($_SESSION['userData'])) {
	header('Location: login');
	exit;
}

$listController = new controllers\ListController(new models\ListModel());
$userController = new controllers\UserController(new models\User());
$UFLController = new models\UFLModel();
//TODO- Actualizar fuente de BooksInList
$BILController = new models\BILModel();

//TODO- Hacer llamada al top50 desde ListController

if (isset($_GET['search']) && !empty($_GET['search'])) {
	$listasp = $listController->searchListLike($_GET['search']);
	$getmode = true;
} else {
	//TODO- Cambiar a getMostFollowed(
	$listasp = $listController->getMostFollowed();
	$getmode = false;
}
//Igual hay alguna forma mejor de hacer esto, pero funciona y chilling
foreach ($listasp as $key => $list) {
	$listasp[$key]['ownerName'] = $userController->getUsernameById($list['id_user']);
}

?>

<?php include_once 'partials/header.php'; ?>

<div class="my-14 container mx-auto min-h-screen">
    <div class="w-3/4 mx-auto">
		<!-- Print todas las listas-->
		<div class="flex justify-between">
			<div>
				<h1 class="text-3xl font-bold text-gray-900">Explorar listas creadas por la comunidad</h1>
			</div>
			<!-- absolutamente robado parte 365 -->
			<div class="w-2/3 md:mx-auto">
				<div class="flex items-center w-full gap-20">
					<form action="lists" method="GET" class="flex items-center gap-5 w-full pb-1 border-b-2 border-primary">
						<input type="text" name="search" class="w-full py-2 bg-transparent outline-none" placeholder="Busca lista por nombre o usuario">
						<button type="submit"><img src="img/lupa.svg" alt=""></button>
					</form>
				</div>
			</div>
		</div>
        <div class="grid grid-cols-2 justify-items-center gap-10 mt-10">
            <?php foreach ($listasp as $key => $list): ?>
                <div class="container">
                    <div class="bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg w-full">
						<div class="flex fex-row gap-1">
							<div class="justify-self-center my-4 ml-4 w-[105px]">
								<img src="<?= $list_pic = !empty($list['list_pic']) ? $list['list_pic'] : 'img/fk_placeholder.png';?>" alt="book" class="object-cover w-40">
							</div>
							<div class="p-4 flex flex-col">
                                <a href="list?id=<?= $list['id_list'] ?>" class="text-lg font-extrabold text-gray-900"><?= $list['list_name'] ?></a>
								<div class="flex items-center gap-2 my-2 ml-1">
									<img src="img/users.svg" alt="user" class="w-4 h-4">
									<a href="profile?id=<?= $list['id_user'] ?>" class="text-sm text-black font-semibold"><?= $list['ownerName'] ?></a>
								</div>
								<div class="flex items-center gap-2 my-2 ml-1">
									<img src="img/followers.svg" alt="followers" class="w-4 h-4">
									<p class="text-sm text-black font-semibold"><?= formatFollowers($list['followersNum'])?></p> <!-- Mover el seguidor/seguidores a una funcion perhaps -->	
								</div>
								<div class="flex items-center gap-2 my-2 ml-1">
									<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
									<p class="text-sm text-black font-semibold"><?= $list['BILCount'] ?></p>
								</div> 
								<!-- implementar descirpción de listas; igual droppear porque implementarlo + actualizar serái aburrido -->
								<div class="w-[400px] max-w-[400px]">
									<p class="text-sm text-black break-words"> <?= $list['list_description'] ?></p> <!--si la descripción cambia de tamaño literalmente explota-->
								</div>
							</div>
						</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include_once 'partials/footer.php'; ?>