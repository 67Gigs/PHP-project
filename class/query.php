<?php

require('../class/tbs_class.php');

class Query {
    protected $pdo;
    protected $tbs;
    protected $req;
    protected $gab;
    protected $eta;
    protected $data;
    
    public function __construct($pdo, $tbs, $req) {
        $this->pdo = $pdo;
        $this->tbs = $tbs;
        $this->req = $req;
    }

    public function execute() {
        $res = $this->pdo->prepare($this->req);
        $res->execute();
        $this->data = new ArrayObject();
        $this->data = $res->fetchAll();
    }
}

class RQSignup extends Query {
    public function __construct($pdo, $tbs) {
        parent::__construct($pdo, $tbs, "INSERT INTO users (username, password, email) VALUES ('".$_POST['username']."', '".$_POST['password']."', '".$_POST['email']."')");
    }

    public function execute() {
        parent::execute();

    }
}


?>