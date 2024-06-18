<?php

require_once '../app/controllers/ListController.php';
require_once '../app/models/List.php';
require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';

session_start();

if (!isset($_SESSION['userData'])) {
    header('Location: login');
    exit;
}

$listController = new controllers\ListController(new models\ListModel());

// Get list
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id_list = $_GET['id'];
        $list = $listController->getListById($id_list);
        if (!$list || !$_SESSION['userData'] || $list['id_user'] !== $_SESSION['userData']['id_user']) {
            header('Location: lists');
            exit;
        }
    } else {
        header('Location: lists');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_list'])) {
        $id_list = $_POST['id_list'];
        $list_name = substr($_POST['list_name'], 0, 255); // Por sea caso
        $description = substr($_POST['description'], 0, 300); // ^^
        $visibility = $_POST['visibility'];

        $listController->updateList($id_list, $list_name, $description, $visibility);
        header("Location: list?id=$id_list");    
    }
}

?>

<?php include_once 'partials/header.php'; ?>

<script>
    function validarFormulario() {
        var list_name = document.getElementById('list_name').value;
        var description = document.getElementById('description').value;
        var visibility = document.getElementById('visibility').value;

        if (list_name.length > 255) {
            alert('El nombre de la lista no puede tener más de 255 caracteres.');
            return false;
        }

        if (description.length > 300) {
            alert('La descripción no puede tener más de 300 caracteres.');
            return false;
        }

        return true;
    }
</script>

<div class="my-14 container mx-auto min-h-screen">
    <div class="w-3/4 mx-auto border border-borderGrey p-8">
        <div class="flex justify-between">
            <div class="w-2/6">
                <h1 class="text-3xl font-bold text-gray-900">Editar lista</h1>
            </div>
            <div class="w-1/6">
                <a href="list?id=<?php echo $list['id_list']; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Volver</a>
            </div>
        </div>
        <form action="list_edit" method="POST" onsubmit="return validarFormulario()">
            <input type="hidden" name="id_list" value="<?php echo $list['id_list']; ?>">
            <div class="mt-6">
                <label for="list_name" class="block text-sm font-medium text-gray-700">Nombre de la lista</label>
                <input type="text" name="list_name" id="list_name" value="<?php echo htmlspecialchars($list['list_name']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($list['list_description']); ?>" placeholder="" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="mt-6">
                <label for="visibility" class="block text-sm font-medium text-gray-700">Visibilidad</label>
                <select name="visibility" id="visibility" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    <option value="public" <?php echo $list['visibility'] === 'public' ? 'selected' : ''; ?>>Pública</option>
                    <option value="private" <?php echo $list['visibility'] === 'private' ? 'selected' : ''; ?>>Privada</option>
                </select>
            </div>
			<div class="pt-8">
            	<button type="submit" name="update_list" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Guardar cambios</button>
			</div>
        </form>
    </div>
</div>


<?php include_once 'partials/footer.php'; ?>
