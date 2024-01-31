<?php
include("config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST["nombre"]);   
    $artista = mysqli_real_escape_string($conn, $_POST["artista"]); 
    $album = mysqli_real_escape_string($conn, $_POST["album"]); 
    $revisado = $_POST["revisado"] ? 1 : 0;
    $rate = mysqli_real_escape_string($conn, $_POST["rate"]);

    $sql = "INSERT INTO cancion (nombre, artista, album, revisado, rate) VALUES ('$nombre', '$artista', '$album', '$revisado', '$rate')";
    
    if (!$conn->query($sql)) {
        echo "Error al insertar la cancion: " . $conn->error;
        exit;
    }
    $conn->close();
    echo "Canción guardada con éxito";
} else {
    echo "Acceso no permitido";
}
?>
