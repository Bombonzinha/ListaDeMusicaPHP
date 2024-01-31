<?php 
/*
-AGREGAR CANCION CON UN POPUP
*/
include("config/database.php");
$sql = "SELECT * FROM cancion";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <div id="main-content">
        <input type="text" id="searchInput" placeholder="Buscar">   
        <table>
            <thead>
                <tr>
                    <th data-order="asc" data-column="id">ID</th>
                    <th data-order="asc" data-column="nombre">Nombre</th>
                    <th data-order="asc" data-column="artista">Artista</th>
                    <th data-order="asc" data-column="album">Álbum</th>
                    <th data-order="asc" data-column="revisado">Revisado</th>
                    <th data-order="asc" data-column="rate">Rate</th>
                    <th>X</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td contenteditable='true'>" . $row["nombre"] . "</td>";
                    echo "<td contenteditable='true'>" . $row["artista"] . "</td>";
                    echo "<td contenteditable='true'>" . $row["album"] . "</td>";
                    echo "<td><input type='checkbox' class='revisadoCheckbox' data-id='" . $row["id"] . "' " . ($row["revisado"] ? "checked" : "") . "></td>";
                    echo "<td class='rate' contenteditable='true' oninput='validarNumero(this)'>" . $row["rate"] . "</td>";
                    echo "<td><button class='eliminar-btn' onclick='eliminarCancion(" . $row["id"] . ")'>Eliminar</button></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <div id="bottom-buttons">
            <button id="guardarBtn">Guardar</button>
            <button id="agregarBtn" >Agregar Cancion</button>
        </div>
    </div>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="agregarForm">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre">

                <label for="artista">Artista:</label>
                <input type="text" id="artista" name="artista">

                <label for="artista">Album:</label>
                <input type="text" id="album" name="album">

                <label for="artista">Revisado:</label>
                <input type="checkbox" id="revisado" name="revisado">

                <label for="rate">Rate:</label>
                <input type="number" id="rate" name="rate">

                <button type="button" id="agregarCancionBtn">Agregar</button>
                <button type="button" class="close">Cancelar</button>
            </form>
        </div>
    </div>
    <script>
        // ORDENAR POR COLUMNA
        $(document).ready(function() {
            $('th[data-order]').on('click', function() {
                var column = $(this).data('column');
                var order = $(this).data('order');
                
                $.ajax({
                    type: "POST",
                    url: "ordenar_tabla.php",
                    data: { column: column, order: order },
                    success: function(response) {
                        $('tbody').html(response);
                        console.log("Tabla ordenara por: " + column + " en orden: " +order);
                    },
                    error: function(error) {
                        console.log("Error al ordenar la tabla: " + error);
                    }
                });

                $(this).data('order', order === 'asc' ? 'desc' : 'asc');
            });
        });
        // BUSCAR
        $(document).ready(function() {
            $('#searchInput').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();

                $('tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
                });
            });
        });
        // ELIMINAR
        function eliminarCancion(id) {
            var confirmacion = confirm("¿Seguro que quieres eliminar esta canción?");

            if (confirmacion) {
                $.ajax({
                    type: "POST",
                    url: "eliminar_cancion.php",
                    data: { id: id },
                    success: function (response) {
                        alert(response); 
                        location.reload();
                    },
                    error: function (error) {
                        alert("Error al eliminar la canción");
                    }
                });
            } else {
                console.log("Eliminación cancelada por el usuario");
            }
        }
        window.addEventListener('resize', function () {
            var tbody = document.querySelector('tbody');
            tbody.style.height = (window.innerHeight - tbody.offsetTop) + 'px';
        });
        // probar
        /* $('.revisadoCheckbox').on('change', function() {
            var id = $(this).data('id');
            var revisado = $(this).prop('checked');

            console.log("Cambiado revisado para ID " + id + ": " + revisado);
        }); */
        // Esto es para detectas qué filas fueron modificadas
        var filasModificadas = [];
        $('tbody').on('input change', 'td[contenteditable="true"], .revisadoCheckbox', function() {
            var fila = $(this).closest('tr');
            if (!fila.hasClass('modificada')) {
                fila.addClass('modificada');
                filasModificadas.push(fila);
            }
        });
        // GUARDAR EN LA BASE DE DATOS
        $('#guardarBtn').on('click', function() {
            if (confirm("¿Seguro que quieres guardar los cambios?")) { 
                var datos = [];

                filasModificadas.forEach(function(fila) {
                    var filaObj = {};

                    filaObj.id = fila.find('td:eq(0)').text();
                    filaObj.nombre = fila.find('td:eq(1)').text()
                    filaObj.artista = fila.find('td:eq(2)').text()
                    filaObj.album = fila.find('td:eq(3)').text()
                    filaObj.revisado = fila.find('td:eq(4) input.revisadoCheckbox').prop('checked'); // Almacenar como booleano
                    filaObj.rate = fila.find('td.rate').text();
                    datos.push(filaObj);
                });

                $.ajax({
                    type: "POST",
                    url: "guardar_canciones.php",  
                    data: { datos: JSON.stringify(datos) },
                    success: function(response) {
                        console.log("Datos guardados con éxito");
                        console.log(response);
                        filasModificadas = [];
                    },
                    error: function(error) {
                        console.log("Error al guardar los datos");
                    }
                });
            }
        });
        function validarNumero(input) {
            input.textContent = input.textContent.replace(/[^0-9.]/g, '');

            if (input.textContent.split('.').length > 2) {
                input.textContent = input.textContent.replace(/\.+$/, '');
            }
        }
        // AGREGAR
        $(document).ready(function() {
            // MODAL
            $("#agregarBtn").on("click", function() {
                $("#myModal").css("display", "block");
            });

            // OCULTAR MODAL
            $(".close").on("click", function() {
                $("#myModal").css("display", "none");
            });

            // AGREGAR
            $("#agregarCancionBtn").on("click", function() {
                var nombre = $("#nombre").val();
                var artista = $("#artista").val();
                var album = $("#album").val();
                var revisado = $("#revisado").prop("checked") ? 1 : 0;;
                var rate = $("#rate").val();
                if (nombre && artista) {
                    if (confirm("¿Seguro que quieres agregar esta canción?")) {
                        $.ajax({
                            type: "POST",
                            url: "agregar_cancion.php",
                            data: {
                                nombre: nombre,
                                artista: artista,
                                album: album,
                                revisado: revisado,
                                rate: rate
                            },
                            success: function(response) {
                                console.log("Datos agregados con éxito");
                            },
                            error: function(error) {
                                console.error("Error al agregar los datos: " + error);
                            }
                        });

                        $("#myModal").css("display", "none");
                    }
                } else {
                    alert("Nombre y Artista son obligatorios");
                }
            });
        });
    </script>
</body>
</html>