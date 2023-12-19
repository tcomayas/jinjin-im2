<?php
  class Database{
        private $server = 'localhost';
        private $username = 'root';
        private $password = '31N$t31n';
        private $db_name = 'account_management_db';

        private $conn = null;
        private $state = false;    
        private $errmsg = '';

        public function __construct(){
            try{
                $this->conn = new PDO("mysql:host=$this->server;dbname=".$this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                
                $this->state = true;
        }catch(PDOException $exception){
            $this->errmsg =  "Connection error: " . $exception->getMessage();
            $this->state = false;
        }
        }

        public function getDb(){
            return $this->conn;
        }
        public function getState(){
            return $this->state;
        }
        public function getErrMsg(){
            return $this->errmsg;
        }

        public function closeConn() {
          $this->conn = null;
        }

  }
?>
