<?php

require('function.php');
require('query.php');
require('user.php');


class Challenge {
    private $id;
    private $title;
    private $type = "default";
    private $description;
    private $points;
    private $solution;
    private $SSH_link;
    private $users = [];
    private $difficulty = 0;

    public function __construct($title, $type,$description, $points, $solution, $SSH_link, $difficulty) {
        $this->id = uniqid(); 
        $this->title = $title;
        $this->type = $type;
        $this->description = $description;
        $this->points = $points;
        $this->solution = $solution;
        $this->SSH_link = $SSH_link;
        $this->difficulty = $difficulty;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getType() {
        return $this->type;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getPoints() {
        return $this->points;
    }

    public function getSSH_link() {
        return $this->SSH_link;
    }

    public compareSolution($solution) {
        return ($this->solution == $solution) 
    }

    public function getDifficulty() {
        return $this->difficulty;
    }

    public function addUser($user) {
        array_push($this->users, $user);
    }

    public function removeUser($user) {
        $key = array_search($user, $this->users);
        if ($key !== false) {
            unset($this->users[$key]);
        }
    }

    public function getUsers() {
        return $this->users;
    }

}

class AccessChallenge {
    private $pdo;

    public function __construct($param_pdo, $tbs) {
        $this->pdo = $param_pdo;
    }

    public function addChallenge($title, $type, $description, $points, $solution, $SSH_link) {
        $challenge = new Challenge($title, $type, $description, $points, $solution, $SSH_link);
        $req = "INSERT INTO challenge (id, title, type, description, points, solution, SSH_link) VALUES ('".$challenge->getId()."', '".$challenge->getTitle()."', '".$challenge->getType()."', '".$challenge->getDescription()."', '".$challenge->getPoints()."', '".$challenge->getSolution()."', '".$challenge->getSSH_link()."')";
        $res = $this->pdo->prepare($req);
        $res->execute();
    }

    public function removeChallenge($id) {
        $req = "DELETE FROM challenge WHERE id = '".$id."'";
        $res = $this->pdo->prepare($req);
        $res->execute();
    }

    public function updateChallenge($id, $title, $type, $description, $points, $solution, $SSH_link) {
        $req = "UPDATE challenge SET title = '".$title."', type = '".$type."', description = '".$description."', points = '".$points."', solution = '".$solution."', SSH_link = '".$SSH_link."' WHERE id = '".$id."'";
        $res = $this->pdo->prepare($req);
        $res->execute();
    }

    public function getChallenge($id) {
        $req = "SELECT * FROM challenge WHERE id = '".$id."'";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetch();
        $challenge = new Challenge($data['title'], $data['type'], $data['description'], $data['points'], $data['solution'], $data['SSH_link']);
        return $challenge;
    }

    public function getAllChallenges() {
        $req = "SELECT * FROM challenge";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetchAll();
        $challenges = [];
        foreach($data as $row) {
            $challenge = new Challenge($row['title'], $row['type'], $row['description'], $row['points'], $row['solution'], $row['SSH_link']);
            array_push($challenges, $challenge);
        }
        return $challenges;
    }

    // A voir si on garde ou pas

    // public function getChallengesByType($type) {
    //     $req = "SELECT * FROM challenge WHERE type = '".$type."'";
    //     $res = $this->pdo->prepare($req);
    //     $res->execute();
    //     $data = $res->fetchAll();
    //     $challenges = [];
    //     foreach($data as $row) {
    //         $challenge = new Challenge($row['title'], $row['type'], $row['description'], $row['points'], $row['solution'], $row['SSH_link']);
    //         array_push($challenges, $challenge);
    //     }
    //     return $challenges;
    // }

    // public function getChallengesByDifficulty($difficulty) {
    //     $req = "SELECT * FROM challenge WHERE difficulty = '".$difficulty."'";
    //     $res = $this->pdo->prepare($req);
    //     $res->execute();
    //     $data = $res->fetchAll();
    //     $challenges = [];
    //     foreach($data as $row) {
    //         $challenge = new Challenge($row['title'], $row['type'], $row['description'], $row['points'], $row['solution'], $row['SSH_link']);
    //         array_push($challenges, $challenge);
    //     }
    //     return $challenges;
    // }

}


?>