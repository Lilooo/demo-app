<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	session_start();
	if($_SERVER['HTTP_HOST'] == 'localhost:8888' )
	{
		$app_id = "924762040878218";
		$app_secret = "47b59577b04c1c8189948b6dad9cac90";
		$url_redirect = "http://localhost:8888/demo-app";
	}else
	{
		$app_id = "887201411300948";
		$app_secret = "3bde5d30b6258994654a0b4169ead22f";
		$url_redirect = "https://alice-app42.herokuapp.com/";
	}
	
	require_once "facebook-php-sdk-v4-4.0-dev/autoload.php";
	use Facebook\FacebookRedirectLoginHelper;
	use Facebook\FacebookSession;
	use Facebook\FacebookRequest;
	use Facebook\GraphUser;
	use Facebook\FacebookRequestException;
	FacebookSession::setDefaultApplication($app_id, $app_secret);
	$helper = new FacebookRedirectLoginHelper($url_redirect);
	try {
		if(isset($_SESSION) && isset($_SESSION['fb_token']))
		{
			$session = new FacebookSession($_SESSION['fb_token']);
		}else{
			$session = $helper->getSessionFromRedirect();		
		}
	  
	} catch(FacebookRequestException $e) {
	  echo "error facebook ";
	} catch(\Exception $e) {
	  echo "error ";
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Application Facebook</title>
		<script>
		  window.fbAsyncInit = function() {
		    FB.init({
		      appId      : '<?php echo $app_id;?>',
		      xfbml      : true,
		      version    : 'v2.3'
		    });
                    FB.ui({
                      method: 'pagetab',
                      redirect_uri: '<?php echo $url_redirect;?>'
                    }, function(response){});
		  };
		  (function(d, s, id){
		     var js, fjs = d.getElementsByTagName(s)[0];
		     if (d.getElementById(id)) {return;}
		     js = d.createElement(s); js.id = id;
		     js.src = "//connect.facebook.net/fr_FR/sdk.js";
		     fjs.parentNode.insertBefore(js, fjs);
		   }(document, 'script', 'facebook-jssdk'));
		</script>
	</head>
	<body>
		
		<h1>Mon application FB</h1>

		<div
		  class="fb-like"
		  data-share="true"
		  data-width="450"
		  data-show-faces="true">
		</div>


		<h2>Se connecter</h2>
		<p>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
		</p>

		<?php
             
			if($session)
			{
				try{
					$_SESSION['fb_token'] =(string) $session->getAccessToken();
					$request = new FacebookRequest($session, 'GET', '/me');
					$response = $request->execute();
					$user_profile = $response->getGraphObject("Facebook\GraphUser");
                                        
//					echo "<pre>";
//					print_r($user_profile);
//					echo "</pre>";
					
					$request = new FacebookRequest(
					  $session,
					  'GET',
					  '/me'
					);
					$response = $request->execute();
					$graphObject = $response->getGraphObject()->asArray();
					
//					echo "<pre>";
//					print_r($graphObject);
//					echo "</pre>";
                                        
                                        $uid = $user_profile->getId();
                                        $fname = $user_profile->getfirstName();
                                        $lname = $user_profile->getlastName();
                                        $email = $user_profile->getProperty('email');
                                        $gender = $user_profile->getProperty('gender'); 

                                        echo $uid.'<br/>';
                                        echo $fname.'<br/>';
                                        echo $lname.'<br/>';
                                        echo $email.'<br/>';
                                        echo $gender.'<br/>';
                                        
                    
                                        $request_photos = new FacebookRequest(
					  $session,
					  'GET',
					  '/me/photos/uploaded'
					);
					$response_photos = $request_photos->execute();
					$graphObject_photos = $response_photos->getGraphObject()->asArray();
					
//					echo "<pre>";
//					print_r($graphObject_photos);
//					echo "</pre>";
                                        
					foreach ($graphObject_photos["data"] as $image) {
						echo "<img width='300px' src='".$image->images[0]->source."'>";
					}
					
				}catch(FacebookRequestException $e)
				{
					echo "Erreur ". $e->getMessage();
					//On supprime la variable de session au cas ou
					session_destroy();
				}
			}else
			{
				$url = $helper->getLoginUrl(['email','user_photos']);
				echo "Veuillez vous connecter en <a href='".$url."'>cliquant ici</a>";
			}
		?>
		
	</body>
</html>