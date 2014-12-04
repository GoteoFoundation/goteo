<?php
/*
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
//*/

//Includes all necessary files for oAuth
include_once(GOTEO_PATH. 'vendor/lusitanian/oauth/src/OAuth/bootstrap.php');

use \OAuth\OAuth2\Service\Facebook;
use \OAuth\Common\Storage\Memory as Storage;
use \OAuth\Common\Consumer\Credentials;
use Goteo\Library\Text;
use \OAuth\ServiceFactory;

/**
 * Suportat:
 * 				OAuth o similar: twitter, facebook, linkedin
 * 				OpenId: google
 *
 * identities:
	 *    Google : https://www.google.com/accounts/o8/id
	 *    Google profile : http://www.google.com/profiles/~YOURUSERNAME
	 *    Yahoo : https://me.yahoo.com
	 *    AOL : https://www.aol.com
	 *    WordPress : http://YOURBLOG.wordpress.com
	 *    LiveJournal : http://www.livejournal.com/openid/server.bml
 * */
class SocialAuth {
	public $host;
	public $callback_url;
	public $provider;
	public $original_provider;
	public $last_error = '';
	public $error_type = '';
	//datos que se recopilan
	public $user_data = array('username' => null, 'name' => null, 'email' => null, 'profile_image_url' => null, 'website' => null, 'about' => null, 'location'=>null,'twitter'=>null,'facebook'=>null,'google'=>null,'identica'=>null,'linkedin'=>null);
	//datos que se importaran (si se puede) a la tabla 'user'
	public $import_user_data = array('name', 'about', 'location', 'twitter', 'facebook', 'google', 'identica', 'linkedin');
	public $tokens = array('twitter'=>array('token'=>'','secret'=>''), 'facebook'=>array('token'=>'','secret'=>''), 'linkedin'=>array('token'=>'','secret'=>''), 'openid'=>array('token'=>'','secret'=>'')); //secretos generados en el oauth

	private $credentials = array(
		'twitter' => array('key' => OAUTH_TWITTER_ID, 'secret' => OAUTH_TWITTER_SECRET),
		'facebook' => array('key' => OAUTH_FACEBOOK_ID, 'secret' => OAUTH_FACEBOOK_SECRET),
		'linkedin' => array('key' => OAUTH_LINKEDIN_ID, 'secret' => OAUTH_LINKEDIN_SECRET),
		'openid' => array('key' => OAUTH_OPENID_SECRET)
	);

	protected $openid_server;
	public $openid_public_servers = array(
		"Google" => "https://www.google.com/accounts/o8/id",
		"Yahoo" => "https://me.yahoo.com",
		"myOpenid" => "http://myopenid.com/",
		"AOL" => "https://www.aol.com",
		"Ubuntu" => "https://login.ubuntu.com",
		"LiveJournal" => "http://www.livejournal.com/openid/server.bml",
	 );

	/**
	 * @param $provider : 'twitter', 'facebook', 'linkedin', 'any_openid_server'
	 * */
	function __construct($provider='') {
		$this->provider = $provider;
		$this->original_provider = $provider;
        $URL = \SITE_URL;
        if(substr($URL,0, 2) === '//') $URL = HTTPS_ON ? "https:$URL" : "http:$URL";
        $this->host = $URL;
	}

	/**
	 * conecta con el servicio de oauth, redirecciona a la pagina para la autentificacion
	 * */
	public function authenticate() {
		switch ($this->provider) {
			case 'twitter':
				return $this->authenticateTwitter();
				break;
			case 'facebook':
				return $this->authenticateFacebook();
				break;
			case 'linkedin':
				return $this->authenticateLinkedin();
				break;
			case 'openid':
				return $this->authenticateOpenid();
				break;
			default:
				$this->last_error = Text::get('oauth-unknown-provider');
				$this->error_type = 'unknown-provider';
				return false;
		}
		return true;
	}

	/**
	 * Autentica con twitter, redirige a Twitter para que el usuario acepte
	 * */
	public function authenticateOpenid() {
		try {
			$openid = new \LightOpenID($this->host);
			$openid->identity = $this->openid_server;
			//standard data provided
			$openid->required = array(
				'namePerson/friendly',
				'namePerson',
				'namePerson/first',
				'namePerson/last',
				'contact/email',
				'contact/country/home',
				//'pref/language'
			);
			$openid->returnUrl = $this->callback_url;
			$url = $openid->authUrl();
			header("Location: $url");
			exit;
		}
		catch(Exception $e){
			$this->last_error = $e->getMessage().' 1/ '.get_class($e);
			$this->error_type = 'provider-exception';
			return false;
		}
		return true;
	}

	/**
	 * Autentica con twitter, redirige a Twitter para que el usuario acepte
	 * */
	public function authenticateTwitter() {
		try {
			$twitterObj = new \EpiTwitter($this->twitter_id, $this->twitter_secret);
			$url = $twitterObj->getAuthenticateUrl(null,array('oauth_callback' => $this->callback_url));
			header("Location: $url");
			exit;
		}
		catch(Exception $e){
			$this->last_error = $e->getMessage().' 1/ '.get_class($e);
			$this->error_type = 'provider-exception';
			return false;
		}
		return true;
	}

	/**
	 * Autentica con Facebook, redirige a Facebook para que el usuario acepte
	 * */
	public function authenticateFacebook() {
		try {
			// Session storage
			$storage = new Storage();
			// Setup the credentials for the requests
			$credentials = new Credentials(
				$this->credentials['facebook']['key'],
				$this->credentials['facebook']['secret'],
				$this->host . '/user/oauth/?provider=facebook'
			);
			// Instantiate the Facebook service using the credentials, http client and storage mechanism for the token
			/** @var $facebookService Facebook */
			$serviceFactory = new ServiceFactory();
			$facebookService = $serviceFactory->createService('facebook', $credentials, $storage, array('email'));

			if(!empty($_GET['code'])) {
				try {
					// This was a callback request from facebook, get the token
					$token = $facebookService->requestAccessToken($_GET['code']);

					// $token = $t->getAccessToken();
					// print_r($t);print_r($token);die;
					if(!$token) {
						$this->last_error = Text::get('oauth-facebook-access-denied');
						$this->error_type = 'access-denied';
						return false;
					}

					// print_R($token);

					//guardar los tokens en la base datos si se quieren usar mas adelante!
					//con los tokens podems acceder a la info del user, hay que recrear el objecto con los tokens privados
					// Send a request with it
					$res = json_decode($facebookService->request('/me'));
					if($res->error) {
						$this->last_error = $res->error->message;
						$this->error_type = 'access-denied';
						return false;
					}

					// $this->tokens['facebook']['token'] = $token->getAccessToken(;
					$this->tokens['facebook']['token'] = $res->id ? $res->id : $res->email;
					//ver todos los datos disponibles:
					//print_r($res);die;

					$this->user_data['name'] = $res->name;
					if($res->username) $this->user_data['username'] = $res->username;
					if($res->email) $this->user_data['email'] = $res->email;
					if($res->website) $this->user_data['website'] = $res->website; //ojo, pueden ser varias lineas con varias webs
					if($res->about) $this->user_data['about'] = $res->about;
					if($res->location->name) $this->user_data['location'] = $res->location->name;
					if($res->id) $this->user_data['profile_image_url'] = 'http://graph.facebook.com/' . $res->id . '/picture?type=large';
					//facebook link
					if($res->link) $this->user_data['facebook'] = $res->link;

					// print_r($res); print_r($this->user_data);die;
				}
				catch(Exception $e){
					$this->last_error =  $e->getMessage().' 1/ '.get_class($e);
					$this->error_type = 'provider-exception';
					return false;
				}
			}
			else {
				$url = $facebookService->getAuthorizationUri();
				// die($url);
				header('Location: ' . $url);
				exit;
			}
			return true;
		}
		catch(Exception $e){
			$this->last_error = $e->getMessage().' 1/ '.get_class($e);
			$this->error_type = 'provider-exception';
			return false;
		}
	}

	/**
	 * Autentica con LinkedIn, redirige a LinkedIn para que el usuario acepte
	 * */
	public function authenticateLinkedin() {
		try {
			//do the authentication:
			//get public tokens
			$to = new \LinkedInOAuth($this->linkedin_id, $this->linkedin_secret);
			// This call can be unreliable for some providers if their servers are under a heavy load, so
			// retry it with an increasing amount of back-off if there's a problem.
			$maxretrycount = 1;
			$retrycount = 0;
			while ($retrycount<$maxretrycount) {
				$tok = $to->getRequestToken($this->callback_url);
				if (isset($tok['oauth_token']) && isset($tok['oauth_token_secret']))
					break;
				$retrycount += 1;
				sleep($retrycount*5);
			}

			if(empty($tok['oauth_token']) || empty($tok['oauth_token_secret'])) {
				$this->last_error = Text::get('oauth-token-request-error');
				$this->error_type = 'access-denied';
				return false;
			}

			//en linkedin hay que guardar los token de autentificacion para usarlos
			//despues para obtener los tokens de acceso,
			$_SESSION['linkedin_token'] = $tok;

			//set URL
			$url = $to->getAuthorizeURL($tok['oauth_token']);

			header("Location: $url");
			exit;
		}
		catch(Exception $e){
			$this->last_error = $e->getMessage().' 1/ '.get_class($e);
			$this->error_type = 'provider-exception';
			return false;
		}
		return true;
	}


	/**
	 * Login con linkedin
	 * */
	public function loginLinkedin() {
		try {

			//recuperar tokens de autentificacion
			$tok = $_SESSION['linkedin_token'];
			$to = new \LinkedInOAuth($this->linkedin_id, $this->linkedin_secret,$tok['oauth_token'],$tok['oauth_token_secret']);
			//obtenemos los tokens de acceso
			$tok = $to->getAccessToken($_GET['oauth_verifier']);
			//borramos los tokens de autentificacion de la session, ya no nos sirven
			//unset($_SESSION['linkedin_token']);

			if(empty($tok['oauth_token']) || empty($tok['oauth_token_secret'])) {
				$this->last_error = Text::get('oauth-linkedin-access-denied');
				$this->error_type = 'access-denied';
				return false;
			}

			//guardar los tokens en la base datos si se quieren usar mas adelante!
			//con los tokens podems acceder a la info del user, hay que recrear el objecto con los tokens privados
			$this->tokens['linkedin']['token'] = $tok['oauth_token'];
			$this->tokens['linkedin']['secret'] = $tok['oauth_token_secret'];


			$profile_result = $to->oAuthRequest('http://api.linkedin.com/v1/people/~:(id,first-name,last-name,summary,public-profile-url,picture-url,headline,interests,twitter-accounts,member-url-resources:(url),positions:(company),location:(name))');
			$profile_data = simplexml_load_string($profile_result);

			$this->user_data['name'] = trim($profile_data->{'first-name'} . ' ' . $profile_data->{'last-name'});
			if($profile_data->{'public-profile-url'}) {
				//linkedin link
				$this->user_data['linkedin'] = current($profile_data->{"public-profile-url"});
				//username from url
				$this->user_data['username'] = basename($this->user_data['linkedin']);
			}


			if($profile_data->{"member-url-resources"}->{"member-url"}) {
				$urls = array();
				foreach($profile_data->{"member-url-resources"}->{"member-url"} as $url) {
					$urls[] = current($url->url);
				}
				$this->user_data['website'] .= implode("\n",$urls);
			}
			if($profile_data->headline) $this->user_data['about'] = current($profile_data->headline);
			if($profile_data->location->name) $this->user_data['location'] = current($profile_data->location->name);
			if($profile_data->{"picture-url"}) $this->user_data['profile_image_url'] = current($profile_data->{"picture-url"});
			//si el usuario tiene especificada su cuenta twitter
			if($profile_data->{"twitter-accounts"}->{"twitter-account"}) $this->user_data['twitter'] = 'http://twitter.com/' . current($profile_data->{"twitter-accounts"}->{"twitter-account"}->{"provider-account-name"});

			//ver todos los datos disponibles:
			//print_r($profile_data);print_r($this->user_data);die;


			return true;
		}
		catch(Exception $e){
			$this->last_error =  $e->getMessage().' 1/ '.get_class($e);
			$this->error_type = 'provider-exception';
			return false;
		}
		return true;
	}

	/**
	 * Login con twitter
	 * */
	public function loginTwitter() {

		if($_GET['denied']) {
			//comprovar si el retorno contiene la variable de denegación
			$this->last_error = Text::get('auth-twitter-access-denied');
			$this->error_type = 'access-denied';
			return false;
		}
		try {
			$twitterObj = new \EpiTwitter($this->twitter_id, $this->twitter_secret);
			$twitterObj->setToken($_GET['oauth_token']);
			$token = $twitterObj->getAccessToken();

			//print_R($token);
			//echo 'twitter_oauth_token: ' . $token->oauth_token . ' / twitter_oauth_token_secret: ' . $token->oauth_token_secret;

			//guardar los tokens en la base datos si se quieren usar mas adelante!
			//con los tokens podems acceder a la info del user, hay que recrear el objecto con los tokens privados
			$twitterObj = new \EpiTwitter($this->twitter_id, $this->twitter_secret,$token->oauth_token,$token->oauth_token_secret);
			$this->tokens['twitter']['token'] = $token->oauth_token;
			$this->tokens['twitter']['secret'] = $token->oauth_token_secret;

			$userInfo = $twitterObj->get_accountVerify_credentials();

			//Twitter NO RETORNA el email!!!
			$this->user_data['username'] = $userInfo->screen_name;
			$this->user_data['name'] = $userInfo->name;
			$this->user_data['profile_image_url'] = str_replace('_normal','',$userInfo->profile_image_url);
			//twitter link
			$this->user_data['twitter'] = 'http://twitter.com/'.$userInfo->screen_name;
			if($userInfo->url) $this->user_data['website'] = $userInfo->url;
			if($userInfo->location) $this->user_data['location'] = $userInfo->location;
			if($userInfo->description) $this->user_data['about'] = $userInfo->description;

			return true;
		}
		catch(Exception $e){
			$this->last_error =  $e->getMessage().' 1/ '.get_class($e);
			$this->error_type = 'provider-exception';
			return false;
		}
		return true;
	}

	/**
	 * Login con openid
	 * */
	public function loginOpenid() {

		$openid = new \LightOpenID($this->host);

		if($openid->mode) {

			if ($openid->mode == 'cancel') {
				$this->last_error = Text::get('oauth-openid-access-denied');
				$this->error_type = 'access-denied';
				return false;

			} elseif($openid->validate()) {

				$data = $openid->getAttributes();
				//print_r($data);print_r($openid);print_r($openid->identity);die;
				/*
				//por seguridad no aceptaremos conexions de OpenID que no nos devuelvan el email
				if(!Goteo\Library\Check::mail($data['contact/email'])) {
					$this->last_error = Text::get('oauth-openid-email-required');
					$this->error_type = 'access-denied';
					return false;
				}*/

				$this->user_data['email'] = $data['contact/email'];
				$this->user_data['username'] = $data['namePerson/friendly'];
				$this->user_data['name']  = $data['namePerson'];
				if(empty($this->user_data['name'])) $this->user_data['name']  = trim($data['namePerson/first'] . ' ' . $data['namePerson/last']);
				if($data['contact/country/home']) $this->user_data['location'] = $data['contact/country/home'];

				//no se usan tokens para openid, guardamos el servidor como token
				$this->tokens['openid']['token'] = $this->openid_server;
				//como secreto usaremos un hash basado an algo que sea unico para cada usuario (la identidad openid es una URL única)
				//$this->tokens['openid']['secret'] = sha1($this->openid_server.$this->openid_secret.$data['contact/email']);
				$this->tokens['openid']['secret'] = $openid->identity;

				return true;
			}
			else {
				$this->last_error = Text::get('oauth-openid-not-logged');
				$this->error_type = 'access-denied';
				return false;
			}
		}

		$this->last_error = Text::get('oauth-openid-not-logged');
		return false;
	}

	/**
	 * Hace el login en goteo si es posible (existen tokens o el email es el mismo)
	 * Guarda los tokens si se encuentra el usuario
	 *
	 * @param $force_login	logea en goteo sin comprovar que la contraseña esté vacía o que el usuario este activo
	 * */
	public function goteoLogin($force_login = false) {
		/*****
		 * POSIBLE PROBLEMA:
		 * en caso de que ya se haya dado permiso a la aplicación goteo,
		 * el token da acceso al login del usuario aunque este haya cambiado el email en goteo.org
		 * es un problema? o da igual...
		*****/
		//Comprovar si existe el mail en la base de datos

		$username = '';
		//comprovar si existen tokens
		$query = Goteo\Core\Model::query('SELECT user.id FROM user
										  INNER JOIN user_login ON user.id = user_login.user
										  AND user_login.provider = :provider
										  AND user_login.oauth_token = :token
										  AND user_login.oauth_token_secret = :secret',
									array(':provider' => $this->provider,
										  ':token' => $this->tokens[$this->provider]['token'],
										  ':secret' => $this->tokens[$this->provider]['secret']));

		$username = $query->fetchColumn();
		// print_r($this->tokens);die;
		if(empty($username)) {
			//no existen tokens, comprovamos si existe el email
			/**
			 * Problema de seguridad, si el proveedor openid nos indica un mail que no pertenece al usuario
			 * da un método para acceder a los contenidos de cualquier usuario
			 * por tanto, en caso de que no existan tokens, se deberá preguntar la contraseña al usuario
			 * si el usuario no tiene contraseña, podemos permitir el acceso directo o denegarlo (mas seguro)
			 * */
			$query = Goteo\Core\Model::query('SELECT user.id,user.password,user_login.provider,user_login.oauth_token,user_login.oauth_token_secret FROM user
											  LEFT JOIN user_login ON user_login.user = user.id
											  WHERE user.email = :user',
									   array(':user' => $this->user_data['email']
									   	));
			$user = null;
			foreach($query->fetchAll(\PDO::FETCH_CLASS) as $user) {
                if($user->provider == $this->provider) {
                	break;
                }
            }
			if($user) {
				// print_r($user);die;
				$username = $user->id;
				// si no existe contraseña permitimos acceso siempre y cuando
				// exista una entrada previa en la tabla user_login para ese proveedor
				$login = false;
				if(!$force_login) {
					// con contraseña no permitimos login
					// lanzamos un error de usuario existente, se usará para mostrar un formulario donde preguntar el password
					if($user->password) {
						$this->last_error = Text::get('oauth-goteo-user-password-exists');
						$this->error_type = 'user-password-exists';
						$this->user_data['username'] = $username;
						return false;
					}
					else {
						// no existe entrada para este proveedor, no permitimos login por seguridad
						if($user->provider != $this->provider && !empty($user->provider)) {
							$this->last_error = sprintf(Text::get('oauth-goteo-user-provider-error'), ucfirst($user->provider));
							$this->error_type = 'user-provider-error';
							return false;
						}
					}
				}
			}
			else {
				//El usuario no existe
				//redirigir a user/confirm para mostrar un formulario para que el usuario compruebe/rellene los datos que faltan
				$this->last_error = Text::get('oauth-goteo-user-not-exists');
				$this->error_type = 'user-not-exists';
				return false;
			}

		}

		//si el usuario existe, actualizar o crear los tokens
		$this->saveTokensToUser($username);

		//actualizar la imagen de avatar si no tiene!
		if($this->user_data['profile_image_url']) {
			$query = Goteo\Core\Model::query('SELECT avatar FROM user WHERE id = ?', array($username));
			if(!($query->fetchColumn())) {

				$img = new Goteo\Model\Image($this->user_data['profile_image_url']);
				$img->save();

				if($img->id) {
					Goteo\Core\Model::query('UPDATE user SET avatar = :avatar WHERE id = :user', array(':user'=>$username,':avatar'=>$img->id));
				}
			}
		}

		//el usuario existe, creamos el objeto
		$user = Goteo\Model\User::get($username);

		//actualizar datos de usuario si no existen:
		$update = array();
		$data = array(':user' => $username);
		foreach($this->import_user_data as $key) {
			if(empty($user->$key) && $this->user_data[$key]) {
				$update[] = "$key = :$key";
				$data[":$key"] = $this->user_data[$key];
			}
		}
		if($update) {
			Goteo\Core\Model::query("UPDATE user SET ".implode(", ",$update)." WHERE id = :user", $data);
			//rebuild user object
			$user = Goteo\Model\User::get($username);
		}

		//actualizar las webs
		if($this->user_data['website']) {
			$current_webs = array();
			if(is_array($user->webs)) {
				foreach($user->webs as $k => $v)
				$current_webs[] = strtolower($v->url);
			}
			$webs = array();
			preg_match_all("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", $this->user_data['website'], $webs);
			if($webs[0] && is_array($webs[0])) {
				$updated = false;
				foreach($webs[0] as $web) {
					$web = strtolower($web);
					if(!in_array($web,$current_webs)) {
						Goteo\Core\Model::query("INSERT user_web (user, url) VALUES (:user, :url)", array(':user' => $username, ':url' => $web));
						$updated = true;
					}
				}
				//rebuild user object
				if($updated) $user = Goteo\Model\User::get($username);
			}
		}

		//Si no tiene imagen, importar de gravatar.com?
		if(!$user->avatar || $user->avatar->id == 1) {
			$query = Goteo\Core\Model::query('SELECT avatar FROM user WHERE id = ?', array($username));
			if(!($query->fetchColumn())) {
				$url = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($user->email)));
				$url .= "?d=404";

				$img = new Goteo\Model\Image( $url );
				$img->save();

				if($img->id) {
					Goteo\Core\Model::query("UPDATE user SET avatar = :avatar WHERE id = :user", array(':user'=>$username,':avatar'=>$img->id));
					$user = Goteo\Model\User::get($username);
				}
			}
		}

		//siempre login, aunque no esté activo el usuario
		//Iniciar sessión i redirigir
		$_SESSION['user'] = $user;

		//Guardar en una cookie la preferencia de "login with"
		//no servira para mostrar al usuario primeramente su opcion preferida
		setcookie("goteo_oauth_provider", $this->original_provider, time() + 3600*24*365);

		if (!empty($_POST['return'])) {
			throw new \Goteo\Core\Redirection($_POST['return']);
		} elseif (!empty($_SESSION['jumpto'])) {
			$jumpto = $_SESSION['jumpto'];
			unset($_SESSION['jumpto']);
			throw new \Goteo\Core\Redirection($jumpto);
		} else {
			// print_r($user);die;
			throw new \Goteo\Core\Redirection('/dashboard/activity');
		}
	}

	/**
	 * Guarda los tokens generados en el usuario
	 * */
	public function saveTokensToUser($goteouser) {
		$query = Goteo\Core\Model::query('SELECT id FROM user WHERE id = ?', array($goteouser));
		if($id = $query->fetchColumn()) {
			foreach($this->tokens as $provider => $token) {
				if($token['token']) {
					$query = Goteo\Core\Model::query("REPLACE user_login (user,provider,oauth_token,oauth_token_secret) VALUES (:user,:provider,:token,:secret)",array(':user'=>$goteouser,':provider'=>$provider,':token'=>$token['token'],':secret'=>$token['secret']));
				}
			}
		}
		else {
			$this->last_error = Text::get('oauth-goteo-user-not-exists');
			$this->error_type = 'provider-exception';
			return false;
		}
	}
}

?>
