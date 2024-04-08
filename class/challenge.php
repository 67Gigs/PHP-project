<?php

class Challenge {
    private $id;
    private $title;
    private $type = "default";
    private $description;
    private $points;
    private $solution;
    private $SSH_link;
    private $users = [];
    private $difficulty = "easy";
    private $validation = '';

    public function __construct($title, $type = "default", $description, $points, $solution, $SSH_link, $difficulty, $id) {
        $this->title = $title;
        $this->type = $type;
        $this->description = $description;
        $this->points = $points;
        $this->solution = $solution;
        $this->SSH_link = $SSH_link;
        $this->difficulty = $difficulty;
        $this->id = $id;
    }

    public function setId($id) {
        $this->id = $id;
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

    public function compareSolution($solution) {
        return ($this->solution == $solution);
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

    public function getValidation() {
        return $this->validation;
    }

    public function setValidation($validation) {
        $this->validation = $validation;
    }

    public function getSolution() {
        return $this->solution;
    }

}

class AccessChallenge {
    private $pdo;

    public function __construct($param_pdo) {
        $this->pdo = $param_pdo;
    }

    public function addChallenge($title, $type, $description, $points, $solution, $SSH_link, $difficulty) {
        $req = "INSERT INTO challenge (title, type, description, points, solution, SSH_link, difficulty) VALUES ('".$title."', '".$type."', '".$description."', '".$points."', '".$solution."', '".$SSH_link."', '".$difficulty."')";
        $res = $this->pdo->prepare($req);
        $res->execute();
        return $res->rowCount();
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
        $challenge = new Challenge($data['title'], $data['type'], $data['description'], $data['points'], $data['solution'], $data['SSH_link'], $data['difficulty'], $data['id']);
        return $challenge;
    }

    public function getAllChallenges() {
        $req = "SELECT * FROM challenge";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetchAll();
        
        $challenges = [];
        foreach($data as $row) {
            $challenge = new Challenge($row['title'], $row['type'], $row['description'], $row['points'], $row['solution'], $row['SSH_link'], $row['difficulty'], $row['id']);
            array_push($challenges, $challenge);
        }

        return $challenges;
    }

    public function validateChallenge ($id, $solution) {
        $challenge = $this->getChallenge($id);
        $req = "SELECT * FROM user_challenge WHERE username = '".$_SESSION['username']."' AND id_challenge = '".$id."'";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetch();
        if ($data) {
            return false;
        }
        if ($challenge->compareSolution($solution)) {
            $req = "INSERT INTO user_challenge (username, id_challenge) VALUES ('".$_SESSION['username']."', '".$id."')";
            $res = $this->pdo->prepare($req);
            $res->execute();
            return true;
        }
        return false;
    }

    public function getChallengesByUser($id) {
        $req = "SELECT * FROM user_challenge WHERE id_user = '".$id."'";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetchAll();
        $challenges = [];
        foreach($data as $row) {
            $challenge = $this->getChallenge($row['id_challenge']);
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
