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

    private function login() {
        $this->tbs->LoadTemplate("../template/login.tpl.html");
        $this->tbs->Show();
    }

    private function sign() {
        $this->tbs->LoadTemplate("../template/sign.tpl.html");
        $this->tbs->Show();
    }

    private function addChallenge() {
        $this->tbs->LoadTemplate("../template/addChallenge.tpl.html");
        $this->tbs->Show();
    }

    private function default() {
        $this->tbs->LoadTemplate("../template/accueil.tpl.html");
        $this->tbs->Show();
    }

    private function allChallenges() {
        $this->tbs->LoadTemplate("../template/challenges.tpl.html");
        $data = new AccessChallenge($this->PDO);
        $data->getAllChallenges();
        
        $this->tbs->Show();
    }

    private function challenge($id) {
        $this->tbs->LoadTemplate("../template/challengeid.tpl.html");
        $data = new AccessChallenge($this->PDO);
        $data->getChallenge($id);
        $this->tbs->Show();
    }



    // private function testLog() {
    //     if (isset($_GET["username"]) && isset($_GET["password"])) {
    //         $username = $_GET["username"];
    //         $password = $_GET["password"];
    //         $email = "";
    //         $user = new User($username, $password, $email);
            
            
    //     }
    // }

    public function engine($accChal, $accUser) {
        if (isset($_GET["route"])) {
            $action = $_GET["route"];
        } else {
            $action = "";
        }

        switch ($action) {
            case 'enregistrer':
                $this->sign();
                break;

            case 'login':
                $this->login();
                break;

            case 'addChallenge':
                $this->addChallenge();
                break;

            case 'challenges':
                $this->allChallenges();
                break;
            
            case 'challenge':
                if (isset($_GET["id"])) {
                    $this->challenge($_GET["id"]);
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                }
                break;

            // case 'testLog':
            //     $this->testLog();
            //     break;
            
            default:
                $this->default();
                break;
        }
    }


}


?>