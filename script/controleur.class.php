<?php

require('../class/tbs_class.php');
require('../class/user.php');
require('../class/challenge.php');
require('../class/query.php');
require('../class/function.php');
require('../config.php');


class Appli {
    private $tbs;
    private $PDO;

    public function __construct($tbs, $PDO) {
        $this->tbs = $tbs;
        $this->PDO = $PDO;
    }

    private function login() { // page de connexion
        $this->tbs->LoadTemplate("../template/login.tpl.html");
        $this->tbs->Show();
    }

    private function sign() { // page d'inscription
        $this->tbs->LoadTemplate("../template/sign.tpl.html");
        $this->tbs->Show();
    }

    private function allChallenges() { // page de tous les challenges
        $this->tbs->LoadTemplate("../template/challenges.tpl.html");
        $data = new AccessChallenge($this->PDO);
        $data->getAllChallenges();
        
        $this->tbs->Show();
    }

    private function challenge($id) { // page d'un challenge (avec id en paramètre)
        $this->tbs->LoadTemplate("../template/challengeid.tpl.html");
        $data = new AccessChallenge($this->PDO);
        $data->getChallenge($id);
        $this->tbs->Show();
    }

    private function addChallenge() { // page d'ajout de challenge (formulaire)
        $this->tbs->LoadTemplate("../template/addChallenge.tpl.html");
        $this->tbs->Show();
    }

    private function addUser() { // page d'ajout d'utilisateur (formulaire)
        $this->tbs->LoadTemplate("../template/addUser.tpl.html");
        $this->tbs->Show();
    }

    private function default() { // page d'accueil
        $this->tbs->LoadTemplate("../template/accueil.tpl.html");
        $this->tbs->Show();
    }

    public function engine($accChal, $accUser) {
        if (isset($_GET["route"])) {
            $action = $_GET["route"];
        } else {
            $action = "";
        }

        switch ($action) {
            case 'sign': // page d'inscription
                $this->sign();
                break;

            case 'signProcess': // processus d'inscription
                if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
                    $accUser->addUser($_POST["username"], $_POST["password"], $_POST["email"]);
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }
                break;
    
            case 'login': // page de connexion
                $this->login();
                break;
            
            case 'loginProcess': // processus de connexion
                if (isset($_GET["username"]) && isset($_GET["password"])) {
                    $accUser->login($_GET["username"], $_GET["password"]);
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }
                break;

            case 'logout': // processus de déconnexion
                $accUser->logout();
                break;
            
            case 'all_challenges': // page de tous les challenges
                $this->allChallenges();
                break;

            case 'challenge': // page d'un challenge (avec id en paramètre)
                if (isset($_GET["id"])) {
                    $this->challenge($_GET["id"]);
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }
                break;

            case 'add_challenge': // page d'ajout de challenge (formulaire)
                $this->addChallenge();
                break;

            case 'add_challenge_process': // processus d'ajout de challenge
                if (isset($_POST["title"]) && isset($_POST["type"]) && isset($_POST["description"]) && isset($_POST["points"]) && isset($_POST["solution"]) && isset($_POST["SSH_link"])) {
                    $accChal->addChallenge($_POST["title"], $_POST["type"], $_POST["description"], $_POST["points"], $_POST["solution"], $_POST["SSH_link"]);
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }
                break;
            case 'remove_challenge': // processus de suppression de challenge
                if (isset($_GET["id"])) {
                    $accChal->removeChallenge($_GET["id"]);
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }
                break;
            
            case 'challenge_process' : // processus de validation du challenge
                if (isset($_GET["id"]) && isset($_GET["solution"])) {
                    $accChal->validateChallenge($_GET["id"], $_GET["solution"]);
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }
                break;


            case 'add_user': // page d'ajout d'utilisateur (formulaire)
                $this->addUser();
                break;

            case 'add_user_process': // processus d'ajout d'utilisateur
                if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
                    $accUser->addUser($_POST["username"], $_POST["password"], $_POST["email"]);
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }

                break;
            
            case 'profile': // page de profil // a modifier pour session et admin
                $this->tbs->LoadTemplate("../template/profile.tpl.html");
                $this->tbs->Show();
                break;
            
            
            
            
            
            
            

            default: // page d'accueil ou default a modifier pour session
                $this->default();
                break;
        }
    }


}


?>