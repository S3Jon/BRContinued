<?php
// Verifica si se ha enviado un archivo
if(isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0){
    $target_dir = "uploads/"; // Directorio donde se almacenarán las imágenes

    // Verifica si el directorio no existe y lo crea
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Crea el directorio con permisos de lectura, escritura y ejecución para todos los usuarios
    }

    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Verifica si el archivo es una imagen real o un archivo de imagen falso
    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "El archivo no es una imagen.";
        $uploadOk = 0;
    }
    
    // Verifica si el archivo ya existe
    if (file_exists($target_file)) {
        echo "El archivo ya existe.";
        $uploadOk = 0;
    }
    
    
    // Permitir solo ciertos formatos de archivo
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Solo se permiten archivos JPG, JPEG, PNG & GIF.";
        $uploadOk = 0;
    }
    
    // Verifica si $uploadOk es igual a 0 por algún error
    if ($uploadOk == 0) {
        echo "La imagen no se ha subido.";
    // Si todo está bien, intenta subir el archivo
    } else {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            echo "La imagen ". htmlspecialchars( basename( $_FILES["profile_image"]["name"])). " ha sido subida.";
        } else {
            echo "Ha ocurrido un error al subir la imagen.";
        }
    }
} else {
    echo "No se ha seleccionado ninguna imagen.";
}
?>

<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="profile_image" accept="image/*">
    <input type="submit" value="Subir Imagen" name="submit">
    <input type="reset" value="Limpiar">
</form>