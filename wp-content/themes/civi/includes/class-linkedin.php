<?php
if (!defined("ABSPATH")) {
    exit();
}
if (!class_exists("Civi_LinkedIn")) {
	class Civi_LinkedIn {
		public static $app_id;
		public static $app_secret;
		public static $callback;
		public static $csrf;
		public static $scopes;
		public static $ssl;
		public static $accessToken;
		public function __construct(string $app_id, string $app_secret, string $callback, string $scopes, bool $ssl = true)
		{
			self::$app_id = $app_id;
			self::$app_secret = $app_secret;
			self::$scopes =  $scopes;
			self::$csrf = random_int(111111,99999999999);
			self::$callback = $callback;
			self::$ssl = $ssl;
		}
		public static function getAuthUrl()
		{
			$_SESSION['linkedincsrf'] = self::$csrf;
			$params = [
				'response_type' => 'code',
				'client_id' => self::$app_id,
				'redirect_uri' => self::$callback,
				'state' => self::$csrf,
				'scope' => self::$scopes,
			];
			return "https://www.linkedin.com/uas/oauth2/authorization?" . http_build_query($params);
		}
		public static function getAccessToken($code)
		{
			$url = "https://www.linkedin.com/uas/oauth2/accessToken";
			$params = [
				'grant_type' => 'authorization_code',
				'code' => $code,
				'redirect_uri' => self::$callback,
				'client_id' => self::$app_id,
				'client_secret' => self::$app_secret,
			];
			$response = self::curl($url,http_build_query($params), "application/x-www-form-urlencoded");
			$accessToken = json_decode($response)->access_token;
			self::$accessToken = $accessToken;
			return $accessToken;
		}
		public static function getPerson()
		{
			$url = "https://api.linkedin.com/v2/me?projection=(id,firstName,localizedFirstName,lastName,localizedLastName,maidenName,email,localizedMaidenName,headline,localizedHeadline,websites,vanityName,profilePicture(displayImage~:playableStreams))&oauth2_access_token=" . self::$accessToken;
			$params = [];
			$response = self::curl($url,http_build_query($params), "application/x-www-form-urlencoded", false);
			$person = json_decode($response, true);
			return $person;
		}
		public static function getEmail()
		{
			$url = "https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))&oauth2_access_token=" . self::$accessToken;
			$params = [];
			$response = self::curl($url,http_build_query($params), "application/x-www-form-urlencoded", false);
			$emailObject = json_decode($response, true);
			return $emailObject;
		}
		protected static function curl($url, $parameters, $content_type, $post = true)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, self::$ssl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXY, null);
			curl_setopt($ch, CURLOPT_POST, $post);
			if ($post) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
			}

			$headers = [];
			$headers[] = "Content-Type: {$content_type}";
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		}
	}
}
