<?php
session_start();

$shortner = new Shortner();
$shortner->init();



class Shortner{
	public function init(){
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

				if($this->slug != ""){
					$q = $this->db->prepare("SELECT `url` from urls WHERE `slug` = ? LIMIT 1");
				    $q->execute([$this->slug]);
				    if($q->rowCount() > 0){
				    	$this->redirect_to($q->fetchColumn());
				    }
				    else
				    	$this->show_error("Error 404 : Cant' find the url");
				}
				else $this->home_page();

			}
			catch(Exception $e){
				$this->show_error("<b>Error :</b> Can't Connect to database, or PHP version does't support");
			}
		}
		else
			$this->show_error("<b>Error :</b> env.php not configured.");
	}
	private function home_page(){
		if(!isset($_SESSION['user'])){
			if(isset($_POST['email'],$_POST['password'])){
				if( array_key_exists($_POST['email'], Env::$credentials) &&
					password_verify($_POST['password'], Env::$credentials[$_POST['email']])
				){
					$_SESSION['user'] = $_POST['email'];
					header('Location: /');
					die();
				}
				else
					$this->show_login("Invalid Credentials");
			}
			else
				$this->show_login();
		}
		else{
			if( array_key_exists($_SESSION['user'], Env::$credentials)){
				// User Logged In
				if(isset($_POST['slug'],$_POST['url'])){


					try{
						$link = $_POST['url'];
						$scheme = parse_url($link, PHP_URL_SCHEME);
						if (empty($scheme)) {
						    $link = 'http://' . ltrim($link, '/');
						}
						$data = array($_POST['slug'], $link, $_SESSION['user']); 
						$stmt = $this->db->prepare("INSERT INTO urls (slug, url, creator)
						  VALUES (?,?,?)");
						$stmt->execute($data);
						$this->show_dashboard("Succesfully Inserted",null);	

					}
					catch(Exception $e){
						$this->show_dashboard(null,"Insertion Faild, May be the slug already exists.");	
					}


				}
				else if(isset($_POST['delete'])){
					try{
						$data = array($_POST['delete']); 
						$stmt = $this->db->prepare("DELETE FROM urls WHERE id = ?");
						$stmt->execute($data);
						$this->show_dashboard("Succesfully Deleted",null);	

					}
					catch(Exception $e){
						$this->show_dashboard(null,"Deletion Faild");	
					}


				}
				else
					$this->show_dashboard();
			}
			else{
				session_destroy();
				$this->show_login();
			}

		}
	}
	private function show_login($error = null){
		include "login.template.php";
	}
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
	private function show_error($error = null){
		include "error.template.php";
	}
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
		            setTimeout('Redirect()', 1000);
		         //-->
		      </script>
		   </head>
		</html>
		<?php
	}
}


?>