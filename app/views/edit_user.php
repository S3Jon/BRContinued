<?php

require_once '../app/controllers/UserController.php';
require_once '../app/models/User.php';

session_start();

if (!isset($_SESSION['userData']) || $_SESSION['userData']['role'] !== 'admin') {
    header('Location: home');
    exit;
}

$userController = new controllers\UserController(new models\User());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_user'])) {
        $id_user = $_POST['id_user'];

        $user = $userController->readByUserID($id_user);

    } else if (isset($_POST['update_user'])) {
        $id_user = $_POST['id_user'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        //TODO - Si no hay password, no actualizarlo. Actualmente se carga la password si esta vacio.
        $password = $_POST['new_password'];
        $role = $_POST['role'];

        if ($userController->updateUser($id_user, $username, $email, $password, $role)) {
            echo "Usuario actualizado con éxito.";
            header("Location: adminpanel");
            exit;
        } else {
            echo "Error al actualizar el usuario.";
        }

    }
}

?>

<div class="flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded shadow-2xl w-1/3">
        <h2 class="text-2xl font-bold mb-4">Editar Usuario</h2>
        <form action="edit_user" method="post" class="mb-4">
            <input type="hidden" name="id_user" value="<?php echo $user['id_user']; ?>" class="hidden">
            <input type="text" name="username" value="<?php echo $user['username']; ?>" class="block mb-2 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-blue-500" placeholder="Nombre de Usuario">
            <input type="email" name="email" value="<?php echo $user['email']; ?>" class="block mb-2 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-blue-500" placeholder="Correo Electrónico">
            <input type="password" name="new_password" placeholder="Nueva Contraseña" class="block mb-2 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-blue-500">
            <select name="role" class="block mb-2 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-blue-500">
                <option value="user" <?php if ($user['role'] === 'user') echo 'selected'; ?>>Usuario</option>
                <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Administrador</option>
            </select>
            <button type="submit" name="update_user" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Actualizar</button>
        </form>
        <a href="adminpanel" class="text-blue-500">Volver</a>
    </div>
</div>