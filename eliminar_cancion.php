<?php
include("config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    $sql = "DELETE FROM cancion WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Canción eliminada con éxito";
    } else {
        echo "Error al eliminar la canción: " . $conn->error;
    }

    $conn->close();
}
?>
