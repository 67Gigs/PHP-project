<?php

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
        
        if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") {
            $this->tbs->LoadTemplate("../template/challengesAdmin.tpl.html");
        } else {
            $this->tbs->LoadTemplate("../template/challenges.tpl.html");
        }
        $data = new AccessChallenge($this->PDO);
        $data->getAllChallenges();
        $titles = [];
        $types = [];
        $descriptions = [];
        $points = [];
        $user = [];
        $difficulty = [];
        $id = [];
        $validated = [];
    
        foreach ($data->getAllChallenges() as $challenge) {
            $titles[] = $challenge->getTitle();
            $types[] = $challenge->getType();
            $descriptions[] = $challenge->getDescription();
            $points[] = $challenge->getPoints();
            $difficulty[] = $challenge->getDifficulty();
            $id[] = $challenge->getId();
            $user[] = $challenge->getUser();
        }

        // verify if the challenge is validated by the user
        foreach ($data->getAllChallenges() as $challenge) {
            $req = "SELECT * FROM user_challenge WHERE username = '".$_SESSION['username']."' AND challenge_id = '".$challenge->getId()."'";
            $res = $this->PDO->prepare($req);
            $res->execute();
            $data = $res->fetch();
            if ($data) {
                $validated[] = "Validé";
            } else {
                $validated[] = "Non validé";
            }
        }

        $this->tbs->MergeBlock('title', $titles);
        $this->tbs->MergeBlock('type', $types);
        $this->tbs->MergeBlock('description', $descriptions);
        $this->tbs->MergeBlock('points', $points);
        $this->tbs->MergeBlock('difficulty', $difficulty);
        $this->tbs->MergeBlock('id', $id);
        $this->tbs->MergeBlock('valide', $validated);

        
        $this->tbs->Show();
    }

    private function challenge($id) { // page d'un challenge (avec id en paramètre)
        if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") {
            $this->tbs->LoadTemplate("../template/challengeAdmin.tpl.html");
        } else {
            $this->tbs->LoadTemplate("../template/challenge.tpl.html");
        }
        $data = new AccessChallenge($this->PDO);
        $challenge = $data->getChallenge($id);
        if ($challenge) {
            $this->tbs->MergeBlock('title', $challenge->getTitle());
            $this->tbs->MergeBlock('type', $challenge->getType());
            $this->tbs->MergeBlock('description', $challenge->getDescription());
            $this->tbs->MergeBlock('points', $challenge->getPoints());
            $this->tbs->MergeBlock('difficulty', $challenge->getDifficulty());
            $this->tbs->MergeBlock('id', $challenge->getId());
            $this->tbs->MergeBlock('solution', $challenge->getSolution());
            $this->tbs->MergeBlock('SSH_link', $challenge->getSSH_link());
        }

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

    private function profile($accUser) { // page de profil ou de profil admin
        if (isset($_SESSION["role"])) {
            
            if ($_SESSION["role"] == "admin") {
                $this->tbs->LoadTemplate("../template/profileAdmin.tpl.html");
            } else {
                $this->tbs->LoadTemplate("../template/profile.tpl.html");
            }
            $user = $accUser->getUser($_SESSION["username"]);
    
            $username = $user->getUsername();
            $email = $user->getEmail();
            $role = $user->getRole();
            $score = $user->getScore();

            $this->tbs->Show();
        } else {
            $this->default();
        }
    }

    private function updateEmail($accUser) { // processus de mise à jour de l'email
        if (isset($_SESSION["username"]) && isset($_POST["email"])) {
            $accUser->updateEmail($_SESSION["username"], $_POST["email"]);
            $this->profile($accUser);
        } else {
            $cible = $_SERVER["PHP_SELF"];
            $this->default();
        }
    }

    private function updatePassword($accUser) { // processus de mise à jour du mot de passe
        if (isset($_SESSION["username"]) && isset($_POST["password"])) {
            if ($accUser->getUser($_SESSION["username"])->getPassword() == $_POST["ancient_password"]) {
                $accUser->updatePassword($_SESSION["username"], $_POST["password"]);
                $message = "Mot de passe mis à jour";
            } else {
                $message = "Mot de passe incorrect";
            }
            $this->profile($accUser);
        } else {
            $cible = $_SERVER["PHP_SELF"];
            $this->default();
        }
    }

    private function default() { // page d'accueil
        if (isset($_SESSION["username"])) {
            $this->tbs->LoadTemplate("../template/accueilLogged.tpl.html");
        } else {
            $this->tbs->LoadTemplate("../template/accueil.tpl.html");
        }
        $this->tbs->Show();
    }



    public function engine($accChal, $accUser) {
        if (isset($_GET["route"])) {
            $action = $_GET["route"];
        } else {
            $action = "";
        }

        switch ($action) { // routeur
            case 'sign': // page d'inscription
                $this->sign();
                break;

            case 'sign_process': // processus d'inscription
                if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
                    $accUser->addUser($_POST["username"], $_POST["password"], $_POST["email"]);
                }
                $cible = $_SERVER["PHP_SELF"];
                $this->default();
                break;
    
            case 'login': // page de connexion
                $this->login();
                break;
            
            case 'login_process': // processus de connexion
                if (isset($_GET["username"]) && isset($_GET["password"])) {
                    $accUser->login($_GET["username"], $_GET["password"]);
                }
                $cible = $_SERVER["PHP_SELF"];
                $this->default();
                break;

            case 'logout': // processus de déconnexion
                if (isset($_SESSION["username"])) {
                    session_destroy();
                }
                $cible = $_SERVER["PHP_SELF"];

                $this->default();
                break;
            
            case 'all_challenges': // page de tous les challenges
                $this->allChallenges();
                break;

            case 'challenge': // page d'un challenge (avec id en paramètre)
                if (isset($_GET["id"])) {
                    if (isset($_SESSION["username"])) {
                        $this->challenge($_GET["id"]);
                    } else {
                        $cible = $_SERVER["PHP_SELF"];
                        $this->default();
                    }
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }
                break;

            case 'add_challenge': // page d'ajout de challenge (formulaire)
                if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") {
                    $this->addChallenge();
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }
                break;

            case 'add_challenge_process': // processus d'ajout de challenge
                if (isset($_POST["title"]) && isset($_POST["type"]) && isset($_POST["description"]) && isset($_POST["points"]) && isset($_POST["solution"]) && isset($_POST["SSH_link"])) {
                    $accChal->addChallenge($_POST["title"], $_POST["type"], $_POST["description"], $_POST["points"], $_POST["solution"], $_POST["SSH_link"]);
                }
                $cible = $_SERVER["PHP_SELF"];
                $this->default();
                break;
            case 'remove_challenge': // processus de suppression de challenge
                if (isset($_GET["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] == "admin" ) {
                    $accChal->removeChallenge($_GET["id"]);
                } 
                $cible = $_SERVER["PHP_SELF"];
                $this->default();
                break;
            
            case 'challenge_process' : // processus de validation du challenge
                if (isset($_GET["id"]) && isset($_GET["solution"])) {
                    $accChal->validateChallenge($_GET["id"], $_GET["solution"]);
                    $message = "Bravo ! Vous avez validé le challenge !";
                } else {
                    $message = "Dommage ! Vous n'avez pas validé le challenge !";
                }
                $cible = $_SERVER["PHP_SELF"] . "?route=challenge&id=" . $_GET["id"];
                $this->challenge($_GET["id"]);
                break;


            case 'add_user': // page d'ajout d'utilisateur (formulaire)
                if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") {
                    $this->addUser();
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }
                break;

            case 'add_user_process': // processus d'ajout d'utilisateur
                if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])) {
                    $accUser->addUser($_POST["username"], $_POST["password"], $_POST["email"]);
                } else {
                    $cible = $_SERVER["PHP_SELF"];
                    $this->default();
                }

                break;
            
            case 'profile': // page de profil ou de profil admin
                $this->profile($accUser);
                break;
            
            case 'delete_profile': // processus de suppression d'utilisateur
                if (isset($_SESSION['role']) && $_SESSION['role'] ) {
                    $accUser->logout();
                    $accUser->deleteUser($_SESSION['username']);
                    $message = '';
                } else {
                    $message = '';
                }
                break;
            
            case 'update_email': // processus de mise à jour de l'email
                $this->updateEmail($accUser);
                break;
            
            case 'update_password': // processus de mise à jour du mot de passe
                $this->updatePassword($accUser);
                break;
            
            
            
            

            default: // page d'accueil ou default a modifier pour session
                $this->default();
                break;
        }
    }


}

