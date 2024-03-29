<?php

require('function.php');
require('challenge.php');
require('query.php');

class User {
    private $username;
    private $password;
    private $email;
    private $role = 'user';
    private $challenges = [];
    private $score = 0;

    public function __construct($username, $password, $email) {
        $this->username = $username;
        if (validatePassword($password)) {
            $this->password = $password;
        }
        if (validateEmail($email)) {
            $this->email = $email;
        }
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }

    public function getChallenges() {
        return $this->challenges;
    }

    public function getScore() {
        return $this->score;
    }

    public function addChallenge($challenge) {
        array_push($this->challenges, $challenge);
    }

    public function removeChallenge($challenge) {
        $key = array_search($challenge, $this->challenges);
        if ($key !== false) {
            unset($this->challenges[$key]);
        }
    }

    public function updateScore($points) {
        $this->score += $points;
    }

    public function comparePassword($password) {
        return ($this->password == $password);
    }

}

class AccessUser {
    private $pdo;

    public function __construct($param_pdo) {
        $this->pdo = $param_pdo;
    }

    public function addUser($username, $password, $email) {
        $user = new User($username, $password, $email);
        $req = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
        $res = $this->pdo->prepare($req);
        $res->execute();
    }

    public function removeUser($username) {
        $req = "DELETE FROM users WHERE username = '$username'";
        $res = $this->pdo->prepare($req);
        $res->execute();
    }

    public function updateEmail ($username, $email) {
        $req = "UPDATE users SET email = '$email' WHERE username = '$username'";
        $res = $this->pdo->prepare($req);
        $res->execute();
    }

    public function updatePassword ($username, $password) {
        $req = "UPDATE users SET password = '$password' WHERE username = '$username'";
        $res = $this->pdo->prepare($req);
        $res->execute();
    }

    public function getUser($username) {
        $req = "SELECT * FROM users WHERE username = '$username'";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetch();
        $user = new User($data['username'], $data['password'], $data['email']);
        return $user;
    }

    public function getUsers() {
        $req = "SELECT * FROM users";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetchAll();
        $users = [];
        foreach ($data as $user) {
            array_push($users, new User($user['username'], $user['password'], $user['email']));
        }
        return $users;
    }

    public function getUserChallenges($username) {
        $req = "SELECT * FROM challenges WHERE username = '$username'";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetchAll();
        $challenges = [];
        foreach ($data as $challenge) {
            array_push($challenges, new Challenge($challenge['title'], $challenge['type'], $challenge['description'], $challenge['points'], $challenge['solution'], $challenge['SSH_link']));
        }
        return $challenges;
    }

    public function getUserScore($username) {
        $req = "SELECT score FROM users WHERE username = '$username'";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetch();
        return $data['score'];
    }

    public function login($username, $password) {
        $req = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $res = $this->pdo->prepare($req);
        $res->execute();
        $data = $res->fetch();
        if ($data) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $data['role'];
            $_SESSION['score'] = $data['score'];
            header('Location: index.php');
        } else {
            header('Location: index.php?page=login');
        }
    }

}

class Admin extends User {
    public function __construct($username, $password, $email) {
        parent::__construct($username, $password, $email);
        $this->role = 'admin';
    }
}


?>