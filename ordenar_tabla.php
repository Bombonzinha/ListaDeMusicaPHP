<?php
include("config/database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $column = $_POST["column"];
    $order = $_POST["order"];

    $sql = "SELECT * FROM cancion ORDER BY $column $order";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td contenteditable='true'>" . $row["nombre"] . "</td>";
        echo "<td contenteditable='true'>" . $row["artista"] . "</td>";
        echo "<td contenteditable='true'>" . $row["album"] . "</td>";
        echo "<td><input type='checkbox' class='revisadoCheckbox' data-id='" . $row["id"] . "' " . ($row["revisado"] ? "checked" : "") . "></td>";
        echo "<td class='rate' contenteditable='true'>" . $row["rate"] . "</td>";
        echo "<td><button class='eliminar-btn' onclick='eliminarCancion(" . $row["id"] . ")'>Eliminar</button></td>";
        echo "</tr>";
    }
    $conn->close();
}
?>
