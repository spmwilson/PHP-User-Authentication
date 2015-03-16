<?php
/**
 * An adaptation of the User used in last lecture.
 * @author jgruiz
 *
 */
require_once "config.php";

class User {
	public $first_name            = '';          /* Users first name */
	public $last_name             = '';           /* Users last name */
	public $username             = '';              /* User Name */
	public $hash                  = '';              /* Hash of password */
	public $status				  = ''; //Expects New, Active, Banned
	public $color				  = ''; //User's favorite color

	public function __construct($first = "", $last = "",
			 $username = "", $passwd = "", $status="New", $color =""){
		$this->first_name = $first;
		$this->last_name  = $last;
		$this->username  = $username;
		$this->hash = User::salt($passwd);
		$this->status = $status;
		$this->color = User::salt($color);
	}
	/* This function provides a complete tab delimeted dump of the contents/values of an object */
	public function contents() {
		$vals = array_values(get_object_vars($this));
		return( array_reduce($vals, create_function('$a,$b',
				'return is_null($a) ? "$b" : "$a"."\t"."$b";')));
	}
	/* Companion to contents, dumps heading/member names in tab delimeted format */
	public function headings() {
		$vals = array_keys(get_object_vars($this));
		return( array_reduce($vals,
				create_function('$a,$b','return is_null($a) ? "$b" : "$a"."\t"."$b";')));
	}
	
	public static function setupDefaultUsers() {
		$users = array();
		$i = 0;
		//Note: I set all the passwords to test but notice that
		//		since we are salting hashes are different.
		$users[$i++] = new User('Simons', 'Cat', 'scat', 'password', 'Active','blue');
		$users[$i++] = new User('George',  'Tirebiter', 'gtierbiter', 'password2', 'Active','red');
		$users[$i++] = new User('Chris', 'Isaak', 'chris', 'test', 'Banned','blue');
		User::writeUsers($users);
	}
	
	public static function writeUsers($users) {
		$fh = fopen(dirname(__FILE__) . '/users.tsv', 'w+') or die("Can't open file");
		//$fh = fopen('users.tsv', 'w+') or die("Can't open file");
		fwrite($fh, $users[0]->headings()."\n");
		for ($i = 0; $i < count($users); $i++) {
			fwrite($fh, $users[$i]->contents()."\n");
		}
		fclose($fh);
	}
	
	public static function readUsers() {
		if (! file_exists(dirname(__FILE__).'/users.tsv')) { User::setupDefaultUsers(); }
		$contents = file_get_contents(dirname(__FILE__).'/users.tsv');
		$lines    = preg_split("/\r|\n/", $contents, -1, PREG_SPLIT_NO_EMPTY);
		$keys     = preg_split("/\t/", $lines[0]);
		$i        = 0;
		for ($j = 1; $j < count($lines); $j++) {
			$vals = preg_split("/\t/", $lines[$j]);
			if (count($vals) > 1) {
				$u = new User();
				for ($k = 0; $k < count($vals); $k++) {
					$u->$keys[$k] = $vals[$k];
				}
				$users[$i] = $u;
				$i++;
			}
		}

		return $users;
	}
	
	public static function loginRequired(){
		global $_SESSION;
		global $config;
		
		if(isset($_SESSION["username"])){
			$users = User::readUsers();
			foreach ($users as $user){
				if($user->username == $_SESSION["username"]){
					if($user->status != "Banned"){
						return;
					}else{
						header("Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ");
						exit();
					}
				}
			}	
		}
		$_SESSION['redirect'] = $_SERVER["REQUEST_URI"];
		//If we got here then we need to log in
		header("Location: " . $config->base_url . "/login.php");
		exit();
	}
	
	public static function getUser($username, $password){
		$users = User::readUsers();
		foreach($users as $user){
			if($user->username == $username){
				if(User::salt($password) == $user->hash){
					return $user;
				}else{
					//We could just keep going but might as well
					//return once we know that the passwd is wrong
					return null;
				}
			}
		}
		return null;
	}
	public static function getUserWithUsername($username){
		$users = User::readUsers();

		foreach($users as $user){
			if($user->username == $username){
				return $user->color;
				}
		}
		return null;
	}
	
	public static function salt($password){
		$salt = "aB1cD2eF3G";
		$password = md5($salt.$password);
		return $password;
	}
}
?>