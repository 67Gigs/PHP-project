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
        
        if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") { // charger different templates, en fonction de si il est administrateur ou non
            $this->tbs->LoadTemplate("../template/challengesAdmin.tpl.html"); // template administrateur
        } else {
            $this->tbs->LoadTemplate("../template/challenges.tpl.html"); // template utilisateurs
        }
        $data = new AccessChallenge($this->PDO); // creer une instance de la classe AccesChallenge pour pouvoir acceder au differente fonctions
        
        // initialisation des tableaux pour extraire les donnees
        $titles = [];
        $types = [];
        $descriptions = [];
        $points = [];
        $difficulty = [];
        $id = [];
        $validated = [];

        // parcourir chaque challenge et extraire les donnees
        foreach ($data->getAllChallenges() as $challenge) { 
            $titles[] = $challenge->getTitle();
            $types[] = $challenge->getType();
            $descriptions[] = $challenge->getDescription();
            $points[] = $challenge->getPoints();
            $difficulty[] = $challenge->getDifficulty();
            $id[] = $challenge->getId();
        }

        // verifier les challenges qui etaient validé par l'utilisateur :
        if (isset($_SESSION["username"])) { // verifier si l'utilisateur est connecté
            foreach ($data->getAllChallenges() as $challenge) {
                // verifier si le challenge est validé par l'utilisateur
                $req = "SELECT * FROM user_challenge WHERE username = '".$_SESSION['username']."' AND id_challenge = '".$challenge->getId()."'";
                $res = $this->PDO->prepare($req);
                $res->execute();
                $data = $res->fetch();
                if ($data) { // message de validation
                    $validated[] = "Validé";
                } else {
                    $validated[] = "Non validé";
                }
            }
            // merge les donnees
            $this->tbs->MergeBlock('valide', $validated);
        } else {
            $this->tbs->MergeBlock('valide', [""]);
        }

        // merge les donnees
        $this->tbs->MergeBlock('title', $titles);
        $this->tbs->MergeBlock('type', $types);
        $this->tbs->MergeBlock('description', $descriptions);
        $this->tbs->MergeBlock('points', $points);
        $this->tbs->MergeBlock('difficulty', $difficulty);
        $this->tbs->MergeBlock('id', $id);

        // afficher le template
        $this->tbs->Show();
    }

    // page d'un challenge (avec id en paramètre)
    private function challenge($id) { 
        if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") { // charger different templates, en fonction de si il est administrateur ou non
            $this->tbs->LoadTemplate("../template/challengeidAdmin.tpl.html"); // template administrateur
        } else {
            $this->tbs->LoadTemplate("../template/challengeid.tpl.html"); // template utilisateurs
        }
        $data = new AccessChallenge($this->PDO); // creer une instance de la classe AccesChallenge pour pouvoir acceder au differente fonctions
        $challenge = $data->getChallenge($id);
        if ($challenge) { // verifier si le challenge existe
            // verifier si l'utilisateur est connecté
            if (isset($_SESSION["username"])) { 
                // verifier si le challenge est validé par l'utilisateur
                $req = "SELECT * FROM user_challenge WHERE username = '".$_SESSION['username']."' AND id_challenge = '".$challenge->getId()."'";
                $res = $this->PDO->prepare($req);
                $res->execute();
                $data = $res->fetch();
                if ($data) { // message de validation
                    $this->tbs->MergeBlock('valide', ["Validé"]);
                } else {
                    $this->tbs->MergeBlock('valide', ["Non validé"]);
                }
            } else {
                $this->tbs->MergeBlock('valide', [""]);
            }

            // merge les donnees
            $this->tbs->MergeBlock('title', [$challenge->getTitle()]);
            $this->tbs->MergeBlock('type', [$challenge->getType()]);
            $this->tbs->MergeBlock('description', [$challenge->getDescription()]);
            $this->tbs->MergeBlock('points', [$challenge->getPoints()]);
            $this->tbs->MergeBlock('difficulty', [$challenge->getDifficulty()]);
            $this->tbs->MergeBlock('id', [$challenge->getId()]);
            $this->tbs->MergeBlock('solution', [$challenge->getSolution()]);
            $this->tbs->MergeBlock('SSH', [$challenge->getSSH_link()]);
            $this->tbs->Show();
        } else { // si le challenge n'existe pas, il le retourne à la page de tous les challenges
            $cible = $_SERVER["PHP_SELF"] . "?route=all_challenges";
            $this->allChallenges();
        }

    }

    private function addChallenge() { // page d'ajout de challenge (formulaire)
        $this->tbs->LoadTemplate("../template/addChallenge.tpl.html");
        $this->tbs->Show();
    }

    private function profile($accUser) { // page de profil ou de profil admin
        if (isset($_SESSION["role"])) { // verifier si l'utilisateur est connecté
            $user = $accUser->getUser($_SESSION["username"]); // recuperer les informations de l'utilisateur
            // extraire les informations de l'utilisateur
            $username = $user->getUsername();
            $email = $user->getEmail();
            $role = $user->getRole();
            $score = $accUser->getUserScore($_SESSION["username"]);

            // initialisation des tableaux pour afficher les donnees
            $username = [$username];
            $email = [$email];
            $role = [$role];
            $score = [$score];


            
            
            // charger different templates, en fonction de si il est administrateur ou non
            if ($_SESSION["role"] == "admin") {
                $this->tbs->LoadTemplate("../template/profileAdmin.tpl.html"); // template administrateur
                $users = $accUser->getUsers(); // recuperer les informations de tous les utilisateurs
                $usernames = [];

                foreach ($users as $usere) { // verifier si l'utilisateur est administrateur
                    if ($usere->getRole() != 'admin') {
                        $usernames[] = $usere->getUsername();
                    }
                }
                $this->tbs->MergeBlock('usernames', $usernames);
                $this->tbs->MergeBlock('usernames', $usernames);                
                
            } else {
                $this->tbs->LoadTemplate("../template/profile.tpl.html"); // template utilisateur
                $this->tbs->MergeBlock('score', $score); // merge les données
            }
            
            //merge les données
            $this->tbs->MergeBlock('username', $username);
            $this->tbs->MergeBlock('email', $email);
            $this->tbs->MergeBlock('role', $role);
            // afficher le template
            $this->tbs->Show();

        } else { // si l'utilisateur n'est pas connecté, il le retourne à la page d'accueil
            $cible = $_SERVER["PHP_SELF"];
            $this->default();
        }
    }

    // processus de mise à jour de l'email
    private function updateEmail($accUser) { 
        if (isset($_SESSION["username"]) && isset($_GET["email"])) { // verifier si l'utilisateur est connecté et si l'email est renseigné
            $accUser->updateEmail($_SESSION["username"], $_GET["email"]); // mettre à jour l'email
            $this->profile($accUser);
        } else {
            $cible = $_SERVER["PHP_SELF"];
            $this->default();
        }
    }

    // processus de mise à jour du mot de passe
    private function updatePassword($accUser) { 
        if (isset($_SESSION["username"]) && isset($_GET["password"])) { // verifier si l'utilisateur est connecté et si le mot de passe est renseigné
            if ($accUser->getUser($_SESSION["username"])->getPassword() == $_GET["ancient_password"]) { // verifier si l'ancien mot de passe est correct
                $accUser->updatePassword($_SESSION["username"], $_GET["password"]); // mettre à jour le mot de passe
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

    // leaderboard
    private function leaderboard($accUser) {
        $this->tbs->LoadTemplate("../template/leaderboard.tpl.html"); // charger le template
        $data = $accUser->getLeaderboard(); // recuperer les informations des utilisateurs
        // initialisation des tableaux pour afficher les donnees
        $usernames = [];
        $scores = [];

        // parcourir chaque utilisateur et extraire les informations
        foreach ($data as $user) {
            $usernames[] = $user->getUsername();
            $scores[] = $user->getScore();
        }

        // merge les donnees
        $this->tbs->MergeBlock('username', $usernames);
        $this->tbs->MergeBlock('score', $scores);
        // afficher le template
        $this->tbs->Show();
    }

    // page de contact
    private function contacte() {
        $this->tbs->LoadTemplate("../template/contacte.tpl.html");
        $this->tbs->Show();
    }

    // page d'accueil
    private function default() { // page d'accueil
        if (isset($_SESSION["username"])) { // verifier si l'utilisateur est connecté
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
                if (isset($_GET["username"]) && isset($_GET["password"]) && isset($_GET["email"])) {
                    if ($accUser->addUser($_GET["username"], $_GET["password"], $_GET["email"])) {
                        $message = "Inscription réussie";
                    } else {
                        $message = "Erreur lors de l'inscription";
                        echo "Erreur lors de l'inscription";
                    }
                } else {
                    echo "Erreur lors de l'inscription";
                }
                header('Location: ' . $_SERVER['PHP_SELF']);
                $this->default();
                break;
    
            case 'login': // page de connexion
                $this->login();
                break;
            
            case 'login_process': // processus de connexion
                if (isset($_GET["username"]) && isset($_GET["password"])) {
                    $accUser->login($_GET["username"], $_GET["password"]);
                }
                header('Location: ' . $_SERVER['PHP_SELF']);
                $this->default();
                break;

            case 'logout': // processus de déconnexion
                if (isset($_SESSION["username"])) {
                    session_destroy();
                    $_COOKIE = [];
                }
                header('Location: ' . $_SERVER['PHP_SELF']);
                $this->default();
                break;
            
            case 'all_challenges': // page de tous les challenges
                $this->allChallenges();
                break;

            case 'challenge': // page d'un challenge (avec id en paramètre)
                if (isset($_GET["id"]) && $_GET["id"] != "") {

                    if (isset($_SESSION["username"])) {
                        $this->challenge($_GET["id"]);
                    } else {
                        $cible = $_SERVER["PHP_SELF"];
                        $this->default();
                    }
                } else {
                    // si l'id n'est pas renseigné, il le retourne à la page de tous les challenges
                    header('Location: ' . $_SERVER['PHP_SELF'] . "?route=all_challenges");
                    $this->allChallenges();
                }
                break;

            case 'challenge_process' : // processus de validation du challenge
                if (isset($_GET["id"]) && isset($_GET["solution"]) && isset($_SESSION["username"])) {
                    if ($accChal->validateChallenge($_GET["id"], $_GET["solution"])) {
                        $points = $accChal->getChallenge($_GET["id"])->getPoints();
                        $accUser->increaseScore($_SESSION["username"], $points);    
                    }
                }
                header('Location: ' . $_SERVER['PHP_SELF'] . "?route=challenge&id=" . $_GET["id"]);
                $this->challenge($_GET["id"]);
                break;

            case 'add_challenge': // page d'ajout de challenge (formulaire)
                if (isset($_SESSION["role"]) && $_SESSION["role"] == "admin") {
                    $this->addChallenge();
                } else {
                    header('Location: ' . $_SERVER['PHP_SELF'] . "?route=all_challenges");

                    $this->allChallenges();
                }
                break;

            case 'add_challenge_process': // processus d'ajout de challenge
                if (isset($_GET["title"]) && isset($_GET["type"]) && isset($_GET["description"]) && isset($_GET["points"]) && isset($_GET["solution"]) && isset($_GET["SSH_link"])) {
                    $accChal->addChallenge($_GET["title"], $_GET["type"], $_GET["description"], $_GET["points"], $_GET["solution"], $_GET["SSH_link"], $_GET["difficulty"]);
                }
                header('Location: ' . $_SERVER['PHP_SELF'] . "?route=all_challenges");
                $this->allChallenges();
                break;

            case 'remove_challenge': // processus de suppression de challenge
                if (isset($_GET["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] == "admin" ) {
                    $accChal->removeChallenge($_GET["id"]);
                }
                header('Location: ' . $_SERVER['PHP_SELF'] . "?route=all_challenges");
                $this->allChallenges();
                break;

            case 'update_challenge':
                if (isset($_GET["id"]) && isset($_SESSION["role"]) && $_SESSION["role"] == "admin") {
                    $accChal->updateChallenge($_GET["id"], $_GET["title"], $_GET["type"], $_GET["description"], $_GET["points"], $_GET["solution"], $_GET["command"], $_GET["difficulty"]);
                }
                header('Location: ' . $_SERVER['PHP_SELF'] . "?route=all_challenges");
                $this->allChallenges();
                break;

            case 'leaderboard': // page du classement
                $this->leaderboard($accUser);
                break;

            case 'add_user_process': // processus d'ajout d'utilisateur
                if (isset($_GET["username"]) && isset($_GET["password"]) && isset($_GET["email"])) {
                    $accUser->addUser($_GET["username"], $_GET["password"], $_GET["email"], $_GET["role"]);
                }
                header('Location: ' . $_SERVER['PHP_SELF']);
                $this->default();

                break;

            case 'remove_user': // processus de suppression d'utilisateur
                if (isset($_GET["username"]) && isset($_SESSION["role"]) && $_SESSION["role"] == "admin") {
                    $accUser->removeUser($_GET["username"]);
                    
                }
                header('Location: ' . $_SERVER['PHP_SELF']);
                $this->default();
                break;
            
            case 'profile': // page de profil ou de profil admin
                if (isset($_SESSION["role"])) { // verifier si l'utilisateur est connecté
                    $this->profile($accUser);

                } else {
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    $this->default();
                }
                break;
            
            case 'delete_profile': // processus de suppression d'utilisateur
                if (isset($_SESSION['role'])) { // verifier si l'utilisateur est connecté
                    // destruction de la session et suppression de l'utilisateur
                    session_destroy();
                    $accUser->removeUser($_SESSION['username']); 
                }
                header('Location: ' . $_SERVER['PHP_SELF']);
                $this->default();
                break;
            
            case 'update_email': // processus de mise à jour de l'email
                $this->updateEmail($accUser);
                break;
            
            case 'update_password': // processus de mise à jour du mot de passe
                $this->updatePassword($accUser);
                break;
            case 'contacte':
                $this->contacte();
                break;
            

            default: // page d'accueil ou default a modifier pour session
                $this->default();
                break;
        }
    }

}