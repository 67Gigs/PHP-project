<?php

require('../class/tbs_class.php');

class Query {
    protected $pdo;
    protected $tbs;
    protected $req;
    protected $gab;
    protected $eta;
    protected $data;
    
    public function __construct($pdo, $tbs, $etat, $req, $gab) {
        $this->pdo = $pdo;
        $this->tbs = $tbs;
        $this->req = $req;
        $this->gab = $gab;
        $this->eta = $etat;
    }

    public function execute() {
        $res = $this->pdo->prepare($this->req);
        $res->execute();
        $this->data = new ArrayObject();
        $this->data = $res->fetchAll();
    }
}


?>