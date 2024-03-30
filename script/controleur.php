<?php

session_start();
require('../class/tbs_class.php');
require('../class/user.php');
require('../class/challenge.php');
require('../class/query.php');
require('../class/function.php');
require('../config.php');
require('controleur.class.php');


$tbs = new clsTinyButStrong;
$tbs->SetOption(array('var_mode' => 0));


try {
    $PDO = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
    $cible = $_SERVER["PHP_SELF"];
    $Appli = new Appli($tbs, $PDO);
    $accChal = new AccessChallenge($PDO);
    $accUser = new AccessUser($PDO);
    $Appli->engine($accChal, $accUser);
    
} catch (Exception $e) {
    die('Error : ' . $e->getMessage());
}

