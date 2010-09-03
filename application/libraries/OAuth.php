<?php

/**
* tmhOAuth
*
* An OAuth 1.0A library written in PHP.
* The library supports file uploading using multipart/form as well as general
* REST requests. OAuth authentication is sent using the an Authorization Header.
*
* @author themattharris
* @version 0.1
*
* 26 August 2010
*/

/*
 * This code is slightly modified to work exclusively with the Twitter
 * account for Pump Pro Edits. No maliciousness is intended.
 */

class OAuth {
  /**
* Creates a new tmhOAuth object
*
* @param string $config, the configuration to use for this request
*/
  function __construct($config = array()) {
    $this->params = array();
    $this->auto_fixed_time = false;
    
    $this->CI =& get_instance();
    $this->CI->load->model('constants');

    // default configuration options
    $this->config = array_merge(
      array(
        'consumer_key' => $CI->constants->getConsumerKey(),
        'consumer_secret' => $CI->constants->getConsumerSecret(),
        'user_token' => $CI->constants->getUserToken(),
        'user_secret' => $CI->constants->getUserSecret(),
        'host' => 'http://api.twitter.com',
        'v' => '1',
        'debug' => false,
        'force_nonce' => false,
        'nonce' => false, // used for checking signatures. leave as false for auto
        'force_timestamp' => false,
        'timestamp' => false, // used for checking signatures. leave as false for auto
        'oauth_version' => '1.0'
      ),
      $config
    );
  }

  /**
* Generates a random OAuth nonce.
* If 'force_nonce' is true a nonce is not generated and the value in the configuration will be retained.
*
* @param string $length how many characters the nonce should be before MD5 hashing. default 12
* @param string $include_time whether to include time at the beginning of the nonce. default true
* @return void
*/
  private function create_nonce($length=12, $include_time=true) {
    if ($this->config['force_nonce'] == false) {
      $sequence = array_merge(range(0,9), range('A','Z'), range('a','z'));
      $length = $length > count($sequence) ? count($sequence) : $length;
      shuffle($sequence);
      $this->config['nonce'] = md5(substr(microtime() . implode($sequence), 0, $length));
    }
  }

  /**
* Generates a timestamp.
* If 'force_timestamp' is true a nonce is not generated and the value in the configuration will be retained.
*
* @return void
*/
  private function create_timestamp() {
    $this->config['timestamp'] = ($this->config['force_timestamp'] == false ? time() : $this->config['timestamp']);
  }

  /**
* Encodes the string or array passed in a way compatible with OAuth.
* If an array is passed each array value will will be encoded.
*
* @param mixed $data the scalar or array to encode
* @return $data encoded in a way compatible with OAuth
*/
  private function safe_encode($data) {
    if (is_array($data)) {
      return array_map(array($this, 'safe_encode'), $data);
    } else if (is_scalar($data)) {
      return str_ireplace(
        array('+', '%7E'),
        array(' ', '~'),
        rawurlencode($data)
      );
    } else {
      return '';
    }
  }

  /**
* Decodes the string or array from it's URL encoded form
* If an array is passed each array value will will be decoded.
*
* @param mixed $data the scalar or array to decode
* @return $data decoded from the URL encoded form
*/
  private function safe_decode($data) {
    if (is_array($data)) {
      return array_map(array($this, 'safe_decode'), $data);
    } else if (is_scalar($data)) {
      return rawurldecode($data);
    } else {
      return '';
    }
  }

  /**
* Returns an array of the standard OAuth parameters.
*
* @return array all required OAuth parameters, safely encoded
*/
  private function get_defaults() {
    $defaults = array(
      'oauth_version' => $this->config['oauth_version'],
      'oauth_nonce' => $this->config['nonce'],
      'oauth_timestamp' => $this->config['timestamp'],
      'oauth_consumer_key' => $this->config['consumer_key'],
      'oauth_signature_method' => 'HMAC-SHA1',
    );

    // include the user token if it exists
    if ( $this->config['user_token'] )
      $defaults['oauth_token'] = $this->config['user_token'];

    // safely encode
    foreach ($defaults as $k => $v) {
      $_defaults[$this->safe_encode($k)] = $this->safe_encode($v);
    }

    return $_defaults;
  }

  /**
* Extracts and decodes OAuth parameters from the passed string
*
* @param string $body the response body from an OAuth flow method
* @return array the response body safely decoded to an array of key => values
*/
  function extract_params($body) {
    $kvs = explode('&', $body);
    $decoded = array();
    foreach ($kvs as $kv) {
      $kv = explode('=', $kv, 2);
      $kv[0] = $this->safe_decode($kv[0]);
      $kv[1] = $this->safe_decode($kv[1]);
      $decoded[$kv[0]] = $kv[1];
    }
    return $decoded;
  }

  /**
* Prepares the HTTP method for use in the base string by converting it to
* uppercase.
*
* @param string $method an HTTP method such as GET or POST
* @return void value is stored to a class variable
* @author Matt Harris
*/
  private function prepare_method($method) {
    $this->method = strtoupper($method);
  }

  /**
* Prepares the URL for use in the base string by ripping it apart and
* reconstructing it.
*
* @param string $url the request URL
* @return void value is stored to a class variable
* @author Matt Harris
*/
  private function prepare_url($url) {
    $parts = parse_url($url);

    $port = @$parts['port'];
    $scheme = $parts['scheme'];
    $host = $parts['host'];
    $path = @$parts['path'];

    $port or $port = ($scheme == 'https') ? '443' : '80';

    if (($scheme == 'https' && $port != '443')
        || ($scheme == 'http' && $port != '80')) {
      $host = "$host:$port";
    }
    $this->url = "$scheme://$host$path";
  }

  /**
* Prepares all parameters for the base string and request.
* Multipart parameters are ignored as they are not defined in the specification,
* all other types of parameter are encoded for compatibility with OAuth.
*
* @param array $params the parameters for the request
* @return void prepared values are stored in class variables
*/
  private function prepare_params($params) {
    // do not encode multipart parameters, leave them alone
    if ($this->config['multipart']) {
      $this->request_params = $params;
      $params = array();
    }

    // signing parameters are request parameters + OAuth default parameters
    $this->signing_params = array_merge($this->get_defaults(), (array)$params);

    // Remove oauth_signature if present
    // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
    if (isset($this->signing_params['oauth_signature'])) {
      unset($this->signing_params['oauth_signature']);
    }

    // Parameters are sorted by name, using lexicographical byte value ordering.
    // Ref: Spec: 9.1.1 (1)
    uksort($this->signing_params, 'strcmp');

    // encode. Also sort the signed parameters from the POST parameters
    foreach ($this->signing_params as $k => $v) {
      $k = $this->safe_encode($k);
      $v = $this->safe_encode($v);
      $_signing_params[$k] = $v;
      $kv[] = "{$k}={$v}";
    }

    // auth params = the default oauth params which are present in our collection of signing params
    $this->auth_params = array_intersect_key($this->get_defaults(), $_signing_params);
    if (isset($_signing_params['oauth_callback'])) {
      $this->auth_params['oauth_callback'] = $_signing_params['oauth_callback'];
      unset($_signing_params['oauth_callback']);
    }

    // request_params is already set if we're doing multipart, if not we need to set them now
    if ( ! $this->config['multipart'])
      $this->request_params = array_diff_key($_signing_params, $this->get_defaults());

    // create the parameter part of the base string
    $this->signing_params = implode('&', $kv);
  }

  /**
* Prepares the OAuth signing key
*
* @return void prepared signing key is stored in a class variables
*/
  private function prepare_signing_key() {
    $this->signing_key = $this->safe_encode($this->config['consumer_secret']) . '&' . $this->safe_encode($this->config['user_secret']);
  }

  /**
* Prepare the base string.
* Ref: Spec: 9.1.3 ("Concatenate Request Elements")
*
* @return void prepared base string is stored in a class variables
*/
  private function prepare_base_string() {
    $base = array(
      $this->method,
      $this->url,
      $this->signing_params
    );
    $this->base_string = implode('&', $this->safe_encode($base));
  }

  /**
* Prepares the Authorization header
*
* @return void prepared authorization header is stored in a class variables
*/
  private function prepare_auth_header() {
    $this->headers = array();
    uksort($this->auth_params, 'strcmp');
    foreach ($this->auth_params as $k => $v) {
      $kv[] = "{$k}=\"{$v}\"";
    }
    $this->auth_header = 'Authorization: OAuth ' . implode(', ', $kv);
    $this->headers[] = $this->auth_header;
  }

  /**
* Signs the request and adds the OAuth signature. This runs all the request
* parameter preparation methods.
*
* @param string $method the HTTP method being used. e.g. POST, GET, HEAD etc
* @param string $url the request URL without query string parameters
* @param array $params the request parameters as an array of key=value pairs
* @param string $useauth whether to use authentication when making the request.
*/
  private function sign($method, $url, $params, $useauth) {
    $this->prepare_method($method);
    $this->prepare_url($url);
    $this->prepare_params($params);

    // we don't sign anything is we're not using auth
    if ($useauth) {
      $this->prepare_base_string();
      $this->prepare_signing_key();

      $this->auth_params['oauth_signature'] = $this->safe_encode(
        base64_encode(
          hash_hmac(
            'sha1', $this->base_string, $this->signing_key, true
      )));

      $this->prepare_auth_header();
    }
  }

  /**
* Make an HTTP request using this library. This method doesn't return anything.
* Instead the response should be inspected directly.
*
* @param string $method the HTTP method being used. e.g. POST, GET, HEAD etc
* @param string $url the request URL without query string parameters
* @param array $params the request parameters as an array of key=value pairs
* @param string $useauth whether to use authentication when making the request. Default true.
* @param string $multipart whether this request contains multipart data. Default false
*/
  function request($method, $url, $params=array(), $useauth=true, $multipart=false) {
    $this->config['multipart'] = $multipart;

    $this->create_nonce();
    $this->create_timestamp();

    $this->sign($method, $url, $params, $useauth);
    $this->curlit($multipart);
  }

  /**
* Make an HTTP request using this library. This method is different to 'request'
* because on a 401 error it will retry the request.
*
* When a 401 error is returned it is possible the timestamp of the client is
* too different to that of the API server. In this situation it is recommended
* the request is retried with the OAuth timestamp set to the same as the API
* server. This method will automatically try that technique.
*
* This method doesn't return anything. Instead the response should be
* inspected directly.
*
* @param string $method the HTTP method being used. e.g. POST, GET, HEAD etc
* @param string $url the request URL without query string parameters
* @param array $params the request parameters as an array of key=value pairs
* @param string $useauth whether to use authentication when making the request. Default true.
* @param string $multipart whether this request contains multipart data. Default false
*/
  function auto_fix_time_request($method, $url, $params=array(), $useauth=true, $multipart=false) {
    $this->request($method, $url, $params, $useauth, $multipart);

    // if we're not doing auth the timestamp isn't important
    if ( ! $useauth)
      return;

    // some error that isn't a 401
    if ($this->response['code'] != 401)
      return;

    // some error that is a 401 but isn't because the OAuth token and signature are incorrect
    // TODO: this check is horrid but helps avoid requesting twice when the username and password are wrong
    if (stripos($this->response['response'], 'password') !== false)
     return;

    // force the timestamp to be the same as the Twitter servers, and re-request
    $this->auto_fixed_time = true;
    $this->config['force_timestamp'] = true;
    $this->config['timestamp'] = strtotime($this->response['headers']['date']);
    $this->request($method, $url, $params, $useauth, $multipart);
  }

  /**
* Utility function to create the request URL in the requested format
*
* @param string $request the API method without extension
* @param string $format the format of the response. Default json. Set to an empty string to exclude the format
* @return string the concatenation of the host, API version, API method and format
*/
  function url($request, $format='json') {
    $format = strlen($format) > 0 ? ".$format" : '';
    return implode('/', array(
      $this->config['host'],
      $this->config['v'],
      $request . $format
    ));
  }

  /**
* Utility function to parse the returned curl headers and store them in the
* class array variable.
*
* @param object $ch curl handle
* @param string $header the response headers
* @return the string length of the header
*/
  private function curlHeader($ch, $header) {
    $i = strpos($header, ':');
    if ( ! empty($i) ) {
      $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
      $value = trim(substr($header, $i + 2));
      $this->response['headers'][$key] = $value;
    }
    return strlen($header);
  }

  /**
* Makes a curl request. Takes no parameters as all should have been prepared
* by the request method
*
* @return void response data is stored in the class variable 'response'
*/
  private function curlit() {
    if (@$this->config['prevent_request'])
      return;

    // method handling
    switch ($this->method) {
      case 'GET':
        // GET request so convert the parameters to a querystring
        if ( ! empty($this->request_params)) {
          foreach ($this->request_params as $k => $v) {
            $params[] = $this->safe_encode($k) . '=' . $this->safe_encode($v);
          }
          $qs = implode('&', $params);
          $this->url = strlen($qs) > 0 ? $this->url . '?' . $qs : $this->url;
          $this->request_params = array();
        }
        break;
    }

    // configure curl
    $c = curl_init();
    curl_setopt($c, CURLOPT_USERAGENT, "themattharris' HTTP Client");
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($c, CURLOPT_TIMEOUT, 10);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
    // for security you may want to set this to TRUE. If you do you need to install
    // the servers certificate in your local certificate store.
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($c, CURLOPT_URL, $this->url);
    // process the headers
    curl_setopt($c, CURLOPT_HEADERFUNCTION, array($this, 'curlHeader'));
    curl_setopt($c, CURLOPT_HEADER, FALSE);
    curl_setopt($c, CURLINFO_HEADER_OUT, true);
    switch ($this->method) {
      case 'GET':
        break;
      case 'POST':
        curl_setopt($c, CURLOPT_POST, TRUE);
        break;
      default:
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, $this->method);
    }

    if ( ! empty($this->request_params) ) {
      // if not doing multipart we need to implode the parameters
      if ( ! $this->config['multipart'] ) {
        foreach ($this->request_params as $k => $v) {
          $ps[] = "{$k}={$v}";
        }
        $this->request_params = implode('&', $ps);
      }
      curl_setopt($c, CURLOPT_POSTFIELDS, $this->request_params);
    } else {
      // CURL will set length to -1 when there is no data, which breaks Twitter
      $this->headers[] = 'Content-Type:';
      $this->headers[] = 'Content-Length:';
    }

    // CURL defaults to setting this to Expect: 100-Continue which Twitter rejects
    $this->headers[] = 'Expect:';

    if ( ! empty($this->headers)) {
      curl_setopt($c, CURLOPT_HTTPHEADER, $this->headers);
    }

    // do it!
    $response = curl_exec($c);
    $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
    $info = curl_getinfo($c);
    curl_close($c);

    // store the response
    $this->response['code'] = $code;
    $this->response['response'] = $response;
    $this->response['info'] = $info;
  }

  /**
* Debug function for printing the content of an object
*
* @param mixes $obj
*/
  function pr($obj) {
    echo '<pre style="word-wrap: break-word">';
    if ( is_object($obj) )
      print_r($obj);
    elseif ( is_array($obj) )
      print_r($obj);
    else
      echo $obj;
    echo '</pre>';
  }

  /**
* Returns the current URL. This is instead of PHP_SELF which is unsafe
*
* @param bool $dropqs whether to drop the querystring or not. Default true
* @return string the current URL
*/
  function php_self($dropqs=true) {
    $url = sprintf('%s://%s%s',
      $_SERVER['SERVER_PORT'] == 80 ? 'http' : 'https',
      $_SERVER['SERVER_NAME'],
      $_SERVER['REQUEST_URI']
    );

    $parts = parse_url($url);

    $port = @$parts['port'];
    $scheme = $parts['scheme'];
    $host = $parts['host'];
    $path = @$parts['path'];
    $qs = @$parts['query'];

    $port or $port = ($scheme == 'https') ? '443' : '80';

    if (($scheme == 'https' && $port != '443')
        || ($scheme == 'http' && $port != '80')) {
      $host = "$host:$port";
    }
    $url = "$scheme://$host$path";
    if ( ! $dropqs)
      return "{$url}?{$qs}";
    else
      return $url;
  }
  
	function genTinyURL($url)
	{
		//gets the data from a URL  
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
	}
	
	function genEditMessage($userid, $user, $status, $style, $title, $song)
	{
		$style = substr($style, 5);
		if ($userid != 2)
		{
			$url = $this->genTinyURL("http://www.pumpproedits.com/user/$userid");
			$person = "$user's";
		}
		else
		{
			$url = $this->genTinyURL("http://www.pumpproedits.com/official");
			$person = "The Official";
		}
		$phrase = "$url $person $style edit for $song called $title has been $status.";
		return $phrase;
	}
	
	function postTwitter($twit)
	{
		if (strpos($_SERVER["SERVER_NAME"], "localhost") !== false) { return; }
		$this->request('POST', $this->url('statuses/update'), array(
			'status' => $twit
		));
		
		if ($this->response['code'] == 200)
		{
			$this->pr(json_decode($this->response['response']));
		}
		else
		{
			$this->pr(htmlentities($this->response['response']));
		}
	}
  
}


