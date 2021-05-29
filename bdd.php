<?php
    session_start();

    try{
        $bdd = new PDO('mysql:host=localhost;dbname=noel;charset=utf8mb4', 'root', 'biscuit2');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(Exception $e){
        die('Erreur : '.$e->getMessage());
    }

?>
