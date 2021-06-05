<?php

class Database {
    // localhost
    private $hostName = "localhost";
    private $dbname = "ocgs";
    private $username = "root";
    private $password = "";
  

    
    private $pdo;
  
    // Start Connection
    public function __construct() {
      $this->pdo = null;
      try {
        $this->pdo = new PDO("mysql:host=$this->hostName;dbname=$this->dbname;", $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }catch(PDOException $e) {
        echo "Error : ". $e->getMessage();
      }
    }
  
    public function fetchAllByCriteria($query,$value) {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([$value]);
      $rowCount = $stmt->rowCount();
      if ($rowCount <= 0) {
        return 0;
      }
      else {
        return $stmt->fetchAll();
      }
    }
  
    public function fetchAll($query) {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute();
      $rowCount = $stmt->rowCount();
      if ($rowCount <= 0) {
        return 0;
      }
      else {
        return $stmt->fetchAll();
      }
    }
  
    public function fetchOne($query, $parameter) {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([$parameter]);
      $rowCount = $stmt->rowCount();
      if ($rowCount <= 0) {
        return 0;
      }else {
        return $stmt->fetch();
      }
    }


    public function insertUserType($query, $name) {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([$name]);
    }
    
    public function updateUserType($query, $name, $id) {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([$name,$id]);
    }




}

?>