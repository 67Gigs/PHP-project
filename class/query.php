<?php


class Query {
    protected $pdo;
    protected $tbs;
    protected $req;
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


?>