<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\OAuth;

use Goteo\Application\Config;
use Goteo\Model\User;
use Goteo\Model\Image;
use Goteo\Application\Cookie;
use Goteo\Core\Model;
use OAuth\OAuth2\Service\Facebook;
use OAuth\Common\Storage\Session as Storage;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;
use Goteo\Library\Text;

/**
 * Suportat:
 * 				OAuth o similar: twitter, facebook, linkedin, google
 * 				OpenId: generic
 *
 * identities:
	 *    Google profile : http://www.google.com/profiles/~YOURUSERNAME
	 *    Yahoo : https://me.yahoo.com
	 *    AOL : https://www.aol.com
	 *    WordPress : http://YOURBLOG.wordpress.com
	 *    LiveJournal : http://www.livejournal.com/openid/server.bml
 * */
class SocialAuth {
	public $host;
	public $provider;
	public $original_provider;
	public $last_error = '';
	public $error_type = '';

	//datos que se recopilan
	public $user_data = array('username' => null, 'name' => null, 'email' => null, 'avatar' => null, 'website' => null, 'about' => null, 'location' => null,'twitter' => null,'facebook' => null,'google' => null,'identica' => null,'linkedin' => null);

	//datos que se importaran (si se puede) a la tabla 'user'
	public $import_user_data = array('name', 'about', 'location', 'avatar', 'twitter', 'facebook', 'google', 'identica', 'linkedin');

	//secretos generados en el oauth
	public $tokens = array('twitter' => array('token' => '','secret' => ''), 'facebook' => array('token' => '','secret' => ''), 'google' => array('token' => '','secret' => ''), 'linkedin' => array('token' => '','secret' => ''), 'openid' => array('token' => '','secret' => ''));

	private $credentials = array(
		'twitter' => array('key' => OAUTH_TWITTER_ID, 'secret' => OAUTH_TWITTER_SECRET),
		'facebook' => array('key' => OAUTH_FACEBOOK_ID, 'secret' => OAUTH_FACEBOOK_SECRET),
        'linkedin' => array('key' => OAUTH_LINKEDIN_ID, 'secret' => OAUTH_LINKEDIN_SECRET),
		'google' => array('key' => OAUTH_GOOGLE_ID, 'secret' => OAUTH_GOOGLE_SECRET)
	);
	//variable para los servicios
	private $storage;
	private $serviceFactory;

	public $openid_public_servers = array(
		"Yahoo" => "https://me.yahoo.com",
		"myOpenid" => "http://myopenid.com/",
		"AOL" => "https://www.aol.com",
		"Ubuntu" => "https://login.ubuntu.com",
		"LiveJournal" => "http://www.livejournal.com/openid/server.bml",
	 );

	/**
	 * @param $provider : 'twitter', 'facebook', 'linkedin', 'google', 'any_openid_server'
	 * */
	function __construct($provider='') {
        $URL = Config::get('url.url_lang') ? Config::get('url.url_lang') : Config::get('url.main');
        $is_ssl = Config::get('ssl');
        if(substr($URL, 0, 2) === '//') $URL = $is_ssl ? "https:$URL" : "http:$URL";
        if(substr(strtolower($URL), 0, 4) !== 'http') $URL = $is_ssl ? "https://$URL" : "http://$URL";
        $this->host = $URL;

		$this->original_provider = $provider;
		if(in_array($provider,array('twitter', 'facebook', 'linkedin', 'google'))) {
			$this->provider = $provider;
		}
		else {
			$this->provider = 'openid';
		}
	}

	/**
	 * conecta con el servicio de oauth, redirecciona a la pagina para la autentificacion
	 * */
	public function authenticate() {
		// Storage for the class
		$this->storage = new Storage();
		$this->serviceFactory = new ServiceFactory();

		switch ($this->provider) {
			case 'twitter':
				return $this->authenticateTwitter();
				break;
            case 'facebook':
                return $this->authenticateFacebook();
                break;
			case 'google':
				return $this->authenticateGoogle();
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

			//OpenId providers to url from the known list
			$openid->identity = $this->openid_public_servers[$this->original_provider] ? $this->openid_public_servers[$this->original_provider] : $this->original_provider;

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
			if($openid->mode) {
				if ($openid->mode == 'cancel') {
					$this->last_error = Text::get('oauth-openid-access-denied');
					$this->error_type = 'access-denied';
					return false;

				} elseif($openid->validate()) {

					$data = $openid->getAttributes();
					// print_r($data);print_r($openid);print_r($openid->identity);print_r("[[".$openid->claimed_id."]]");print_r($openid->data['openid_identity']);die;
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
					$this->tokens['openid']['token'] = $this->original_provider;
					//como secreto usaremos un hash basado an algo que sea unico para cada usuario (la identidad openid es una URL única)
					$this->tokens['openid']['secret'] = $openid->identity;
					// print_r($this);die;
					// print_r($openid);die;
					return true;
				}
				else {
					$this->last_error = Text::get('oauth-openid-not-logged');
					$this->error_type = 'access-denied';
					return false;
				}
			}
			else {
				$openid->returnUrl = $this->host . '/login/openid?u=' .  urlencode($this->original_provider);
				$url = $openid->authUrl();
				header("Location: $url");
				exit;
			}
		}
		catch(\Exception $e){
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
            // Setup the credentials for the requests
            $credentials = new Credentials(
                $this->credentials['twitter']['key'],
                $this->credentials['twitter']['secret'],
                $this->host . '/login/twitter'
            );
            // Instantiate the twitter service using the credentials, http client and storage mechanism for the token
            /** @var $twitterService Twitter */
            $twitterService = $this->serviceFactory->createService('twitter', $credentials, $this->storage);

            if (!empty($_GET['oauth_token'])) {
                $token = $this->storage->retrieveAccessToken('Twitter');
                // This was a callback request from twitter, get the token
                $twitterService->requestAccessToken(
                    $_GET['oauth_token'],
                    $_GET['oauth_verifier'],
                    $token->getRequestTokenSecret()
                );
                // Send a request now that we have access token
                $res = json_decode($twitterService->request('account/verify_credentials.json'));

                $this->tokens['twitter']['token'] = $res->id ? $res->id : $res->screen_name;

                if($res->name) $this->user_data['name'] = $res->name;
                if($res->screen_name) $this->user_data['username'] = $res->screen_name;
                //this is never provided by twitter...
                if($res->email) $this->user_data['email'] = $res->email;

                //ojo, pueden ser varias lineas con varias webs
                if($res->entities) {
                    foreach($res->entities as $k => $entity) {
                        if($entity->urls && is_array($entity->urls)) {
                            foreach($entity->urls as $url) {
                                if($url->expanded_url) $this->user_data['website'] .= $url->expanded_url . "\n";
                            }
                        }
                    }
                }
                if($res->description) $this->user_data['about'] = $res->description;
                if($res->location) $this->user_data['location'] = $res->location;
                if($res->profile_image_url) {
                    $this->user_data['avatar'] = str_replace('_normal','',$res->profile_image_url);
                    $this->user_data['avatar_name'] = basename(parse_url($this->user_data['avatar'], PHP_URL_PATH));
                }

                //twitter link
                $this->user_data['twitter'] = 'http://twitter.com/'.$userInfo->screen_name;

                // echo 'result: <pre>' . print_r($this->user_data, 1) . print_r($res, true) . '</pre>';die;

                return true;

            }
            else {
                 // extra request needed for oauth1 to request a request token :-)
                $token = $twitterService->requestRequestToken();

                $url = $twitterService->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
                header('Location: ' . $url);
                exit;
            }

        }
        catch(\Exception $e){
            $this->last_error = $e->getMessage().' 1/ '.get_class($e);
            $this->error_type = 'provider-exception';
            return false;
        }
        return true;
    }

	/**
	 * Autentica con google, redirige a Google para que el usuario acepte
	 * */
	public function authenticateGoogle() {
		try {
			// Setup the credentials for the requests
			$credentials = new Credentials(
				$this->credentials['google']['key'],
				$this->credentials['google']['secret'],
				$this->host . '/login/google'
			);

			// Instantiate the twitter service using the credentials, http client and storage mechanism for the token
            $googleService = $this->serviceFactory->createService('google', $credentials, $this->storage, array('userinfo_email', 'userinfo_profile'));


			if (!empty($_GET['code'])) {
                // This was a callback request from google, get the token
                $googleService->requestAccessToken($_GET['code']);

                // Send a request with it
                $res = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'));

				$this->tokens['google']['token'] = $res->id ? $res->id : $res->screen_name;

                if($res->name) $this->user_data['name'] = $res->name;
				if($res->name) $this->user_data['username'] = strtolower(Model::idealiza($res->name));
				//this is never provided by google...
				if($res->email) $this->user_data['email'] = $res->email;

				if($res->picture) {
					$this->user_data['avatar'] = str_replace('_normal','',$res->picture);
					$this->user_data['avatar_name'] = $this->user_data['username'] . '.jpg';
				}

				//google link
				if($res->link) $this->user_data['google'] = $res->link ;
                 // $this->user_data['google'] = 'https://plus.google.com/'.$userInfo->id ;

				// echo 'result: <pre>' . print_r($this->user_data, 1) . print_r($res, true) . '</pre>';die;

				return true;

			}
            else {
                $url = $googleService->getAuthorizationUri();
                // die($url);
                header('Location: ' . $url);
                exit;
            }
            return true;

		}
		catch(\Exception $e){
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
			// Setup the credentials for the requests
			$credentials = new Credentials(
				$this->credentials['facebook']['key'],
				$this->credentials['facebook']['secret'],
				$this->host . '/login/facebook'
			);
			// Instantiate the Facebook service using the credentials, http client and storage mechanism for the token
			/** @var $facebookService Facebook */
			$facebookService = $this->serviceFactory->createService('facebook', $credentials, $this->storage, array('email'));

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

					//ver todos los datos disponibles:
					// print_r($res);die;
					$this->tokens['facebook']['token'] = $res->id ? $res->id : $res->email;

					if($res->name) $this->user_data['name'] = $res->name;
					if($res->username) $this->user_data['username'] = $res->username;
					if($res->email) $this->user_data['email'] = $res->email;
					if($res->website) $this->user_data['website'] = $res->website; //ojo, pueden ser varias lineas con varias webs
					if($res->about) $this->user_data['about'] = $res->about;
					if($res->location->name) $this->user_data['location'] = $res->location->name;
					if($res->id) {
						if($json = @json_decode(@file_get_contents('http://graph.facebook.com/' . $res->id . '/picture?type=large&redirect=false'))) {
							if($json->data && $json->data->url) {
								$this->user_data['avatar'] = $json->data->url;
								$this->user_data['avatar_name'] = basename(parse_url($json->data->url, PHP_URL_PATH));
								// print_r($json);print_r($this->user_data);die;
							}
						}
					}
					//facebook link
					if($res->link) $this->user_data['facebook'] = $res->link;

					// print_r($res); print_r($this->user_data);die;

					return true;

				}
				catch(\Exception $e){
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
		catch(\Exception $e){
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
			// Setup the credentials for the requests
			$credentials = new Credentials(
				$this->credentials['linkedin']['key'],
				$this->credentials['linkedin']['secret'],
				$this->host . '/login/linkedin'
			);
			// Instantiate the Linkedin service using the credentials, http client and storage mechanism for the token
			/** @var $linkedinService Linkedin */
			$linkedinService = $this->serviceFactory->createService('linkedin', $credentials, $this->storage, array('r_basicprofile', 'r_emailaddress'));

			if (!empty($_GET['code'])) {
			    // retrieve the CSRF state parameter
			    $state = isset($_GET['state']) ? $_GET['state'] : null;

			    // This was a callback request from linkedin, get the token
			    $token = $linkedinService->requestAccessToken($_GET['code'], $state);

			    // Send a request with it. Please note that XML is the default format.
			    $result = json_decode($linkedinService->request('/people/~:(id,first-name,last-name,email-address,summary,public-profile-url,picture-url,headline,interests,location:(name))?format=json'));

				$this->tokens['linkedin']['token'] = $result->id ? $result->id : $result->emailAddress;

				$this->user_data['name'] = trim($result->firstName . ' ' . $result->lastName);
				if($result->emailAddress) $this->user_data['email'] = $result->emailAddress;

				if($result->publicProfileUrl) {
					//linkedin link
					$this->user_data['linkedin'] = $result->publicProfileUrl;
					//username from url
					$this->user_data['username'] = basename($this->user_data['linkedin']);
				}

				if($result->headline) $this->user_data['about'] = $result->headline;
				if($result->location->name) $this->user_data['location'] = $result->location->name;
				if($result->pictureUrl) {
					$this->user_data['avatar'] = $result->pictureUrl;
					$this->user_data['avatar_name'] = $this->user_data['username'] . '.jpg';
				}
				if($result->summary) $this->user_data['website'] = $result->summary;
				// if($result->memberUrlResources->memberUrl) {
				// 	foreach($result->memberUrlResources->memberUrl as $url) {
				// 		$this->user_data['website'] .= $url->url . "\n";
				// 	}
				// }
				//si el usuario tiene especificada su cuenta twitter
				// if($result->twitterAccounts->twitterAccount) $this->user_data['twitter'] = 'http://twitter.com/' . current($result->twitterAccounts->twitterAccount->providerAccountName);

			    // Show some of the resultant data
			    // echo '<pre>' . print_r($this->user_data, 1) . print_r($result, 1) . '</pre>';die;

			} else {
			    $url = $linkedinService->getAuthorizationUri();
			    header('Location: ' . $url);
			    exit;
			}

		}
		catch(\Exception $e){
			$this->last_error = $e->getMessage().' 1/ '.get_class($e);
			$this->error_type = 'provider-exception';
			return false;
		}
		return true;
	}

	/**
	 * Hace el login en goteo si es posible (existen tokens o el email es el mismo)
	 * Guarda los tokens si se encuentra el usuario
	 * Actualiza datos de usuario si desde la red social
	 *
	 * @param $force_login	logea en goteo sin comprovar que la contraseña esté vacía o que el usuario este confirmado
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
		$query = Model::query('SELECT user.id FROM user
										  INNER JOIN user_login ON user.id = user_login.user
										  AND user_login.provider = :provider
										  AND user_login.oauth_token = :token
										  AND user_login.oauth_token_secret = :secret
										  ',
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
			$query = Model::query('SELECT user.id,user.password,user.active,user_login.provider,user_login.oauth_token,user_login.oauth_token_secret FROM user
											  LEFT JOIN user_login ON user_login.user = user.id
											  WHERE user.email = :user
											  ORDER BY user_login.datetime ASC',
									   array(':user' => $this->user_data['email']
									   	));
			//buscar si el usuario tiene una entrada previa con el proveedor
			$user = null;
			$user_provider = null;
			foreach($query->fetchAll(\PDO::FETCH_CLASS) as $user) {
				$user_provider = $user->provider;
				$compare = $this->provider;
				if($user->provider === 'openid') {
					$user_provider = $user->oauth_token;
					$compare = $this->tokens['openid']['token'];
				}

                if($user_provider === $compare) {
                	break;
                }
            }

			if($user) {

			    if(!$user->active) {
			        $this->last_error = Text::get('user-account-inactive') . $user->id;
			        $this->error_type = 'user-inactive';
			        return false;
			    }

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
						if($user_provider && $user_provider !== $this->original_provider) {
						// die($this->original_provider."|".$user_provider);
							$this->last_error = sprintf(Text::get('oauth-goteo-user-provider-error'), ucfirst($user_provider));
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

		//el usuario existe, creamos el objeto
		$query = Model::query('SELECT * FROM user WHERE id = ?', $username);
		$user = $query->fetchObject();
	    if(!$user->active) {
	        $this->last_error = Text::get('user-account-inactive');
	        $this->error_type = 'user-inactive';
	        return false;
	    }
	    // print_r($user);die;
		//actualizar datos de usuario si no existen:
		$update = array();
		$data = array(':user' => $username);
		foreach($this->import_user_data as $key) {
			$value = $this->user_data[$key];
			if(empty($user->$key)) {
				//actualizar la imagen de avatar si no tiene!
				if($key == 'avatar') {
					$value = '';
					$img = new Image($this->user_data['avatar'], $this->user_data['avatar_name']);
					$img->save($errors, false);
					if($img->id) {
						$value = $img->id;
					}
					//mirar en gravatar si no tiene ninguna de social
					else {
						$url = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email))) . '.jpg?s=400&d=404';
						$img = new Image( $url , "$username.jpg");
						$img->save($errors, false);
						if($img->id) {
							$value = $img->id;
						}
					}
					// print_r($img);
				}
				if($value) {
					$update[] = "$key = :$key";
					$data[":$key"] = $value;
				}
			}
		}

		if($update) {
			// print_r($user);
			// print_r($this->user_data);
			// print_r($update);
			// print_r($data);
			// die;
			Model::query("UPDATE user SET ".implode(", ",$update)." WHERE id = :user", $data);
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
				foreach($webs[0] as $web) {
					$web = strtolower($web);
					if(!in_array($web,$current_webs)) {
						Model::query('INSERT user_web (user, url) VALUES (:user, :url)', array(':user' => $username, ':url' => $web));
					}
				}
			}
		}

		//rebuild user object
		$user = User::get($username);

	    //Guardar en una cookie la preferencia de "login with"
        //servira para mostrar al usuario primeramente su opcion preferida
        Cookie::store('goteo_oauth_provider', $this->original_provider);

		//return user
		return $user;
	}

	/**
	 * Guarda los tokens generados en el usuario
	 * */
	public function saveTokensToUser($goteouser) {
		$query = Model::query('SELECT id FROM user WHERE id = ?', array($goteouser));
		if($id = $query->fetchColumn()) {
			foreach($this->tokens as $provider => $token) {
				if($token['token']) {
					$query = Model::query("REPLACE user_login (user,provider,oauth_token,oauth_token_secret) VALUES (:user,:provider,:token,:secret)",array(':user'=>$goteouser,':provider'=>$provider,':token'=>$token['token'],':secret'=>$token['secret']));
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
