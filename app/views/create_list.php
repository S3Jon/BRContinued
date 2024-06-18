	<?php

	session_start();

	require_once "../app/models/User.php";
	require_once "../app/controllers/UserController.php";
	require_once "../app/models/List.php";
	require_once "../app/controllers/ListController.php";

	$user = new models\User();
	$list = new models\ListModel();
	$userController = new controllers\UserController($user);
	$listController = new controllers\ListController($list);

	if (!isset($_SESSION['userData'])) {
		header('Location: login');
		exit;
	}


	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST['create_list'])) {
			if (empty($_POST['$description'])) {
				$_POST['description'] = '';
			}
			$listController->createList($_SESSION['userData']['id_user'], $_POST['list_name'], $_POST['description'], $_POST['visibility']);
		}
	}

	?>

	<?php require_once 'partials/header.php'; ?>

	<div class="my-14 container mx-auto min-h-screen">
		<div class="w-3/4 mx-auto">
			<div class="flex justify-between">
				<div class="w-2/6">
					<h1 class="text-3xl font-bold text-gray-900">Crear lista</h1>
				</div>
			</div>
			<form action="" method="POST" onsubmit="return validarFormulario()">
				<div class="mt-6">
					<label for="list_name" class="block text-sm font-medium text-gray-700">Nombre de la lista</label>
					<input type="text" name="list_name" id="list_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
				</div>
				<div class="mt-6">
					<label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
					<input type="text" name="description" id="description" placeholder="" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
				</div>
				<div class="mt-6">
					<label for="visibility" class="block text-sm font-medium text-gray-700">Visibilidad</label>
					<select name="visibility" id="visibility" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
						<option value="public">Pública</option>
						<option value="private">Privada</option>
					</select>
				</div>
				<button type="submit" name="create_list" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-6">Crear lista</button>
			</form>
		</div>
	</div>

	<?php include 'partials/footer.php'; ?>

	<script>
		function validarFormulario() {
			let list_name = document.getElementById('list_name').value;
			let visibility = document.getElementById('visibility').value;

			if (list_name === '' || visibility === '') {
				alert('Todos los campos son obligatorios');
				return false;
			}

			return true;
		}
	</script>
