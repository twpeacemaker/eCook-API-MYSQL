<?php
include_once '../errors.php';
include_once '../constants.php';
include_once "BaseConnection.php";
/*
  CI: this class holds and fetches information about user,
*/
class User extends BaseConnection {
  // object properties
  public $id;
  public $username;
  public $password;
  public $fkey_theme_id;
  public $theme;

  /*
     constucts the object and its parent
  */
  public function __construct() {
    parent::__construct(USER_TABLE);
  }

  /*
     Takes the data stored in memeber data of User and stores that data
     to te database, $this->username,password, and theme_id much be init
  */
  public function store(){

    if( !empty($this->username) && !empty($this->password) && !empty($this->fkey_theme_id) ){
      //ASSERT: check if the user has already been created;
      try {
        $username = $this->get_user($this->username)["username"];
        //already created
        throw new Exception(ALREADY_CREATED);
      } catch(Exception $e) {
        //valid new username
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->fkey_theme_id=htmlspecialchars(strip_tags(
                                                $this->fkey_theme_id
                                              ));
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                  SET username='{$this->username}',
                  password='{$this->password}',
                  fkey_theme_id={$this->fkey_theme_id}";
        // prepare query
        $st = $this->conn->prepare($query);
        // execute query
        if(!$st->execute()) {
          //assert will error if any lengths or fkeys are invalid
          throw new Exception(WHEN_ADDING_TO_DATABASE);
        }
      }
    } else {
      throw new Exception(MEMBER_DATA_NOT_SET);
    }
  }

  // shows one user from the database, $this->user must be unit, sets all
  // the member dat aobjects to match the database
  public function show() {
    if( !empty($this->username) ) {
      try {
        // attempt to get the user based on the username
        $user = $this->get_user($this->username);
        // got a user successfully
        $this->setMemberData($user);
      } catch(Exception $e) {
        throw new Exception($e->getMessage());
      }
    } else {
      throw new Exception(MEMBER_DATA_NOT_SET);
    }
  }

  // validates a user in the database based on the username and password
  // $this->userame, password must be init, once validated sets all the
  // member data object to the values fetched in the database
  public function validateLogin() {
    if( !empty($this->username) && !empty($this->password) ) {
      try {
        // attempt to get the user based on the username
        $user = $this->get_user($this->username);
        // got a user successfully
        if ($this->password == $user["password"]) {
          // password posted matches the user fetched
          $this->setMemberData($user);
        } else {
          // password posted does not the user fetched
          throw new Exception(INVALID_PASSWORD);
        }
      } catch(Exception $e) {
        throw new Exception($e->getMessage());
      }
    } else {
      throw new Exception(MEMBER_DATA_NOT_SET);
    }
  }

  // gets a user from the database from its username
  // @param $username str, the username fetched
  // @return, array of the user with the id,username,password,fkey_theme_id,
  //          theme_name
  public function get_user($username){
      // query to read single record
      if(is_string($username)) {
        $query = "SELECT u.id,u.username,u.password,u.fkey_theme_id,t.theme_name
                  FROM ".$this->table_name." u
                  LEFT JOIN Theme t ON u.fkey_theme_id = t.id
                  WHERE u.username = '{$username}';";
        // prepare query statement
        $st = $this->conn->prepare( $query );
        if(!$st->execute()) {
          throw new Exception(QUERY_ERROR);
        } else {
          if($st->rowCount() > 0) {
            // get retrieved row
            //ASSERT: must be 0 or 1
            $row = $st->fetch(PDO::FETCH_ASSOC);
          } else {
            // user was not found
            throw new Exception(INVALID_USERNAME);
          }
        }
      } else {
        throw new Exception(INVALID_USERNAME);
      }

      // set values to object properties
      return $row;
  }

  // gets a user array from and sets the local member data object to that
  // data
  // @param user[] must have keys of id, username, password, fkey_theme_id
  //               and theme_name
  private function setMemberData($user) {
    $this->id = $user["id"];
    $this->username = $user["username"];
    $this->password = $user["password"];
    $this->fkey_theme_id = $user["fkey_theme_id"];
    $this->theme = $user["theme_name"];
  }
}
