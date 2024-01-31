<?php
    $db_server = "localhost";
    $db_username = "root";
    $db_pass = "";
    $db_name = "musica";
    $conn = "";
    try {
    $conn = mysqli_connect($db_server, $db_username, $db_pass, $db_name);
    } catch(mysqli_sql_exception){
        echo "No conectado"."<br>";
    }
    /* if($conn){
        echo "Conectado"."<br>";
    } */
?>

