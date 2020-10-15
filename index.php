<?php
session_start();

$shortner = new Shortner();



class Shortner{
	public function __construct(){
		$this->slug = substr($_SERVER['REQUEST_URI'],1);
		if(file_exists('env.php')){
			include 'env.php';

			try{
				$creds = Env::$database;
				$this->db = new PDO(
					"mysql:host=".$creds['DB_HOST'].";dbname=".$creds['DB_NAME'], 
					$creds['DB_USERNAME'], $creds['DB_PASSWORD']
				);
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				if($this->slug != "")
					$this->check_slug(); // Check if the short url exists in db and redirect to it
				else
					$this->home_page(); // Open Admin Page

			}
			catch(Exception $e){
				$this->show_error("<b>Error :</b> Can't Connect to database, or PHP version does't support");
			}
		}
		else
			$this->show_error("<b>Error :</b> env.php not configured.");
	}

	// Admin Page
	private function home_page(){

		if(!isset($_SESSION['user'])){
			// If no user logged in, login
			if(isset($_POST['email'],$_POST['password']))
				$this->login($_POST['email'],$_POST['password']);
			else
				$this->show_login();
		}
		else{
			if(array_key_exists($_SESSION['user'], Env::$credentials) && !isset($_POST['logout'])){

				if(isset($_POST['slug'],$_POST['url'])) // Create New Slug
					$this->add_url($_POST['slug'],$_POST['url']);
				else if(isset($_POST['delete'])) // Delete slug by Id
					$this->delete_slug($_POST['delete']);
				else // Show dashboard
					$this->show_dashboard();
			}
			else // if $_POST contains logout , or admin removed the logged in email from directory
				$this->logout();

		}
	}
	// Check credentials and login
	private function login($email,$password){
		if( array_key_exists($email, Env::$credentials) &&
			password_verify($password, Env::$credentials[$email])
		){
			$_SESSION['user'] = $email;
			header('Location: /');
			die();
		}
		else
			$this->show_login("Invalid Credentials");
	}
	// Logout and destroy all sessions
	private function logout(){
		session_destroy();
		$this->show_login();
	}
	// Dashboard Template
	private function show_dashboard($success = null,$error = null){
		try{
		  $stmt = $this->db->prepare("SELECT * FROM urls ORDER BY id DESC");
		  $stmt->execute();

		  $urls = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	      include "dashboard.template.php";
		}
		catch(Exception $e){
			$this->show_error("<b>Error</b>: Can't find the Table, or PHP version does't support");
		}
	}
	// Error page template
	private function show_error($error = null){
		include "error.template.php";
	}
	// Login Page Template
	private function show_login($error = null){
		include "login.template.php";
	}


	// Add New url to the db
	private function add_url($slug,$link){
		try{
			$scheme = parse_url($link, PHP_URL_SCHEME);
			if (empty($scheme)) {
			    $link = 'http://' . ltrim($link, '/');
			}
			$data = array($slug, $link, $_SESSION['user']); 
			$stmt = $this->db->prepare("INSERT INTO urls (slug, url, creator) VALUES (?,?,?)");
			$stmt->execute($data);
			$this->show_dashboard("Succesfully Inserted",null);	

		}
		catch(Exception $e){
			$this->show_dashboard(null,"Insertion Faild, May be the slug already exists.");	
		}
	}
	// Delete url from db
	private function delete_slug($slug){
		try{
			$data = array($slug); 
			$stmt = $this->db->prepare("DELETE FROM urls WHERE id = ?");
			$stmt->execute($data);
			$this->show_dashboard("Succesfully Deleted",null);	

		}
		catch(Exception $e){
			$this->show_dashboard(null,"Deletion Faild");	
		}
	}
	// Check if url exists in db, if so redirect to it
	private function check_slug(){
		try{
			$q = $this->db->prepare("SELECT `url` from urls WHERE `slug` = ? LIMIT 1");
		    $q->execute([$this->slug]);
		    if($q->rowCount() > 0){
		    	$this->redirect_to($q->fetchColumn());
		    }
		    else
		    	$this->show_error("Error 404 : Cant' find the url");			
		}
		catch(Exception $e){
	    	$this->show_error("Some Error Occured");			
		}
	}
	// Redirect to a given url
	private function redirect_to($url = null){
		?>
		<html>
		   <head>
	     		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-180621412-2"></script>
				<script>
				  window.dataLayer = window.dataLayer || [];
				  function gtag(){dataLayer.push(arguments);}
				  gtag('js', new Date());

				  gtag('config', 'UA-180621412-2');
				</script>

		      <script type = "text/javascript">
		         <!--
		            function Redirect() {
		               window.location = "<?=$url?>";
		            }            
		            document.write("Redirecting to <?=$url?>");
		            setTimeout('Redirect()', 500);
		         //-->
		      </script>
		   </head>
		</html>
		<?php
	}
}


?>