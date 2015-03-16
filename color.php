<?php
	/**********
	 * Login Page for Super Example. Takes person back to
	 * page if redirect is set.
	 * Jaime Ruiz	initial		
	 */
	 
	require_once "inc/page_setup.php";

	//Set some variables required by header
	$title = "Authentication Super Example";
	$page_name ="extended";
	
	require_once "lib/user.php";
	
	//Define some variables used throughout the page
	$errors = array();
	if(isset($_POST['color'])){
		//Check username password combo
		//Only return simple error. Do you know
		//why not say "username not found" or
		//"password not valid"?

		$usercolor = User::getUserWithUsername($_SESSION['username']);

		if(is_null($usercolor)){
			$errors[] = "Invalid User.";
		}else{
			//$usercolor = strip_tags($_POST["color"]);
			
			// How to change final userlogin to color.php page instead of login.php
			//Other users are broken
			//How to securely get used information on secondary page aka color.php
			if($usercolor == User::salt($_POST["color"])){
				//$_SESSION['first_name'] = $user->first_name;
				//$_SESSION['username'] = $user->username;
				$_SESSION['start'] = time();
				//Let's redirect or go to home page
				if(isset($_SESSION['redirect'])){
					$loc = $_SESSION['redirect'];
					unset($_SESSION['redirect']);
					header("Location: " . $loc);
					return;
				}
				else{
					header("Location: " . $config->base_url);
					return;
				}
				
			}else{
				//$errors[] = "User color invalid.";
				//$_SESSION['redirect'] = "logout.php";
				//$loc = $_SESSION['redirect'];
				header("Location: " . "logout.php");
					return;

			}
		}	
	}
	//Include example header
	include 'inc/header.php';
?>
<main>
	<div class="container">
		<div class="page-header">
			<h1>Enter your favorite color:</h1>
		</div>
		<?php 
			if(count($errors) > 0){ ?>
			<div class="alert alert-danger" role="alert">
			<h4 >Please fix the following errors.</h3>
				<ul>
					<?php 	
						//This for each will load the key of the
						//array into the $field variable and the value
						//into the $error variable
						foreach($errors as $field => $error){
							echo "<li>$error</li>";
						}
					?> 
				</ul>
			</div>
		<?php		
			} //End if count($errors);
		?>
		<form method="post" action="color.php">
			<div class="form-group">
				<label for="color">Favorite Color</label>
				<input type="text" id="color" name="color" class="form-control" required  />
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-default" />
			</div>	
			
		</form>
	</div>
</main>