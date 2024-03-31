<?php


class User {
    private $username;
    private $password;
    private $email;
    private $role = 'user';
    private $challenges = [];
    private $score = 0;

    public function __construct($username, $password, $email) {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
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

class userNoPassword extends User {
    public function __construct($username, $email) {
        $this->username = $username;
        $this->email = $email;
    }
}

class AccessUser {
    private $pdo;

    public function __construct($param_pdo) {
        $this->pdo = $param_pdo;
    }

    public function addUser($username, $password, $email) {
        $user = new User($username, $password, $email);
        try {
            $req = "INSERT INTO users (username, password, email, role, score) VALUES ('$username', '$password', '$email', 'user', 0)";
            $res = $this->pdo->prepare($req);
            $res->execute();
            if ($res) {
                return $user;
            } else {
                return null;
            }
        } catch (Exception $e) {
            echo 'Error : ' . $e->getMessage();
        }
    }

    public function removeUser($username) {
        try {
            $req = "DELETE FROM users WHERE username = '$username'";
            $res = $this->pdo->prepare($req);
            $res->execute();
        } catch (Exception $e) {
            echo 'Error : ' . $e->getMessage();
        }
    }

    public function updateEmail ($username, $email) {
        try {
            $req = "UPDATE users SET email = '$email' WHERE username = '$username'";
            $res = $this->pdo->prepare($req);
            $res->execute();
        } catch (Exception $e) {
            echo 'Error : ' . $e->getMessage();
        }
    }

    public function updatePassword ($username, $password) {
        try {
            $req = "UPDATE users SET password = '$password' WHERE username = '$username'";
            $res = $this->pdo->prepare($req);
            $res->execute();
        } catch (Exception $e) {
            echo 'Error : ' . $e->getMessage();
        }
    }

    public function getUser($username) {
        try {
            $req = "SELECT * FROM users WHERE username = '$username'";
            $res = $this->pdo->prepare($req);
            $res->execute();
            $data = $res->fetch();
            $user = new User($data['username'], $data['password'], $data['email']);
            return $user;
        } catch (Exception $e) {
            echo 'Error : ' . $e->getMessage();
        }
    }

    public function getUsers() {
        try {
            $req = "SELECT * FROM users";
            $res = $this->pdo->prepare($req);
            $res->execute();
            $data = $res->fetchAll();
            $users = [];
            foreach ($data as $user) {
                array_push($users, new User($user['username'], $user['password'], $user['email']));
            }
            return $users;
        } catch (Exception $e) {
            echo 'Error : ' . $e->getMessage();
        }
    }

    public function getUserScore($username) {
        try {
            $req = "SELECT score FROM users WHERE username = '$username'";
            $res = $this->pdo->prepare($req);
            $res->execute();
            $data = $res->fetch();
            return $data['score'];
        } catch (Exception $e) {
            echo 'Error : ' . $e->getMessage();
        }
    }

    public function getLeaderboard() {
        try {
            $req = "SELECT * FROM users ORDER BY score DESC";
            $res = $this->pdo->prepare($req);
            $res->execute();
            $data = $res->fetchAll();
            $users = [];
            foreach ($data as $user) {
                array_push($users, new userNoPassword($user['username'], $user['email']));
            }
            return $users;
        } catch (Exception $e) {
            echo 'Error : ' . $e->getMessage();
        }
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
            header('Location: controleur.php');
        } else {
            header('Location: controleur.php?route=login');
        }
    }

    public function increaseScore($username, $points) {
        $req = "UPDATE users SET score = score + $points WHERE username = '$username'";
        $res = $this->pdo->prepare($req);
        $res->execute();
    }

}

