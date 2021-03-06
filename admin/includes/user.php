<?php
/**
 * Created by PhpStorm.
 * User: Mahfuz
 * Date: 13-Feb-19
 * Time: 11:34 PM
 */




class User{

    protected static $db_table = "users";
    protected static $db_table_fields = array('username', 'password', 'first_name', "last_name");
	public $id;
	public $username;
	public $password;
	public $first_name;
	public $last_name;

	public static function find_all_users(){
        //global $database;
        //$result_set = $database->query("SELECT * FROM users");
        //return $result_set;

		return self::find_this_query("SELECT * FROM users"); 



	}



	public static function find_users_by_id(){
		global $database;
		$result_user_id = $database->query("SELECT * FROM users WHERE id=2");
		return $result_user_id;
	}

	public static function find_users_by_id_with_parameter($user_id){
		global $database;
		$result_array = self::find_this_query("SELECT * FROM users WHERE id=$user_id LIMIT 1");

return !empty($result_array) ? array_shift($result_array) : false;

/*if(!empty($result_array)){ // check whether array empte or not
$first_item = array_shift($result_array); // return first item of array
return $first_item;
}else{
	return false;
}*/

//return $find_user;
}

    public static function find_this_query($sent_sql){ // this method is used to sent sql query
    	global $database;
    	$result_set = $database->query($sent_sql);

    	$the_object_array = array();
    	while($row = mysqli_fetch_array($result_set)){
    		$the_object_array[] = self::instantiation($row);
    	}
    	return $the_object_array;
    }

    public static function verify_user($username,$password){

        global $database;
        $username = $database->escape_string($username);
        $password = $database->escape_string($password);

        $sql = "SELECT * FROM users WHERE ";
        $sql .= "username = '{$username}' ";
        $sql .= "AND password = '{$password}' ";
        $sql .= "LIMIT 1";
        $result_array = self::find_this_query($sql);

        return !empty($result_array) ? array_shift($result_array) : false;




    } // end of verify user


    public static function instantiation($the_record){ //this method is used to instantiate class
    	$the_object = new self;

             /*$the_object->id = $calling_by_id['id'];
             $the_object->username = $calling_by_id['username'];
             $the_object->password = $calling_by_id['password'];
             $the_object->first_name = $calling_by_id['first_name'];
             $the_object->last_name = $calling_by_id['last_name'];*/

             foreach ($the_record as $the_attribute => $value) {
	if($the_object->has_the_attribute($the_attribute)){ // check whether the object has value or not
		$the_object->$the_attribute = $value;
	}
} // end of foreach

return $the_object;
    } //end of instantiation

    private function has_the_attribute($the_attribute){
    	$object_properties = get_object_vars($this);
    	return array_key_exists($the_attribute, $object_properties);
    } // end of has_the_attribute


    protected function properties(){ //it will sent all the property of this class


        $properties = array();
        foreach (self::$db_table_fields as $db_field){
            if(property_exists($this,$db_field)){
                $properties[$db_field] = $this->$db_field;
            }
        }

        return $properties;

    } //end of property method

    protected function clean_properties(){ // for cleaning the value using scape string
        global $database;
        $clean_properties = array();

        foreach ($this->properties() as $key => $value){
            $clean_properties[] = $database->escape_string($value);
        }
        return $clean_properties;
    }


    public function save(){ // this method detects the user is already in the database or not. if it is there it will execute update() otherwise it will execute create();
        return isset($this->id) ? $this->update() : $this->create();
    } //end of save


    



    public function create(){
        global $database;
        $properties = $this->properties(); // for holding all the object property

        $sql = "INSERT INTO " .self::$db_table . "(" . implode(",", array_keys($properties)) . ")";
        $sql .= "VALUES ('". implode("','", array_values($properties)) . "')";


        if($database->query($sql)){
            $this->id = $database->the_insert_id();
            return true;
        }else{
            return false;
        }


    } //end of create

    public function update(){
        global $database;
        $properties = $this->properties(); // for holding all the object property
        $property_pairs = array(); // for holding the attribute of database which will be updated

        foreach ($properties as $key => $value){
            $property_pairs[] = "{$key}='{$value}'";
        }


        $sql = "UPDATE  " .self::$db_table . "  SET ";
        $sql .= implode(", ", $property_pairs);
        $sql .= " WHERE id= " . $database->escape_string($this->id) ;

        $database->query($sql);
        return (mysqli_affected_rows($database->connection) == 1) ? true : false; //mysqli_affected_rows returns the number of affected rows in the previous query



    } //end of update

    public function delete(){
        global $database;
        $sql = "DELETE from  " .self::$db_table . " ";
        $sql .= " WHERE id= " . $database->escape_string($this->id);
        $sql .= " LIMIT 1";

        $database->query($sql); // execution of query
        return (mysqli_affected_rows($database->connection) == 1) ? true : false; //mysqli_affected_rows returns the number of affected rows in the previous query



    } //end of delete


} //end of user class