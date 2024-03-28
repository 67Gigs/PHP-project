<?php

require('../class/tbs_class.php');
require('../class/user.php');
require('../class/challenge.php');
require('../class/query.php');
require('../class/function.php');
require('../config.php');


$tbs = new clsTinyButStrong;


try {
    $PDO = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
    $etatConnexion = "Connexion réussie";
    $cible = $_SERVER["PHP_SELF"];
    
} catch (Exception $e) {
    die('Error : ' . $e->getMessage());
}


?>