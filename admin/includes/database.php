<?php
/**
 * Created by PhpStorm.
 * User: Mahfuz
 * Date: 12-Feb-19
 * Time: 9:30 PM
 */


require_once("new_config.php");

class Database{
    public  $connection; //for making database connection global for all class

     function __construct(){
         $this->open_db_connection(); // by referring database object calling open_db_connection() method

     }



    public function open_db_connection(){

        $this-> connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

        if($this->connection->connect_errno){
            die("Database connection failed".$this->connection->connect_error);
        }
    }

    public function query($sql){
        $result = $this->connection->query($sql);
        $this->confirm_query($result);
        return $result;
    }

    private function confirm_query($result){
        if(!$result){
            die("Query Failed".$this->connection->error);
        }
    }


    public function escape_string($string){ // for cleaning data like what space etc
        $escaped_string = $this->connection->real_escape_string($string);
        return $escaped_string;
    }

    public function the_insert_id(){ // for pulling the id of last insert query
        return $this->connection->insert_id;
    }


} // end of database class


$database = new Database; // instantiating Database class by creating database ogject

//$database->open_db_connection();  by referring database object calling open_db_connection() method




