<?php
include("config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datos = json_decode($_POST["datos"], true);

    if ($datos === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "console.log('Error al decodificar los datos JSON')";
        exit;
    }
    $revisado = -1;
    foreach ($datos as $fila) {
        $id = $fila["id"];
        $nombre = mysqli_real_escape_string($conn, $fila["nombre"]);   
        $artista = mysqli_real_escape_string($conn, $fila["artista"]); 
        $album = mysqli_real_escape_string($conn, $fila["album"]); 
        $revisado = $fila["revisado"] ? 1 : 0;
        $rate = mysqli_real_escape_string($conn, $fila["rate"]);

        $sql = "UPDATE cancion SET nombre='$nombre', artista='$artista', album='$album', revisado='$revisado', rate='$rate' WHERE id='$id'";
        
        if (!$conn->query($sql)) {
            echo "Error al guardar las canciones: " . $conn->error;
            exit;
        }
        /*
        $stock = $_POST["stock"] === "1" ? true : false;
        $habilitado = $_POST["habilitado"] === "1" ? true : false;
        
        $query = "INSERT INTO productos (nombre, descripcion, id_categoria, precio, stock, reg_date, habilitado) 
            VALUES ('$nombre', '$descripcion', '$id_categoria', '$precio', '$stock', NOW(), '$habilitado')";
         */
    }
    $conn->close();
    echo "Lista guardada con éxito";
} else {
    echo "Acceso no permitido";
}
?>
