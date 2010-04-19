<?php
public function postTwitter($status)
{
  if (strpos($_SERVER["SERVER_NAME"], "localhost") !== false) { return; }
  // Twitter login information
  $CI =& get_instance();
  $CI->load->model('constants');
  $username = $CI->constants->getTwitterName(); 
  $password = $CI->constants->getTwitterPass();
  // The url of the update function 
  $url = 'http://twitter.com/statuses/update.xml'; 
  // Arguments we are posting to Twitter 
  $postargs = 'status='.urlencode($status); 
  // Will store the response we get from Twitter 
  $responseInfo=array(); 
  // Initialize CURL 
  $ch = curl_init($url);
  // Tell CURL we are doing a POST 
  curl_setopt ($ch, CURLOPT_POST, true); 
  // Give CURL the arguments in the POST 
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
  // Set the username and password in the CURL call 
  curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password); 
  // Set some cur flags (not too important) 
  curl_setopt($ch, CURLOPT_VERBOSE, 1); 
  curl_setopt($ch, CURLOPT_NOBODY, 0); 
  curl_setopt($ch, CURLOPT_HEADER, 0); 
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  // execute the CURL call 
  $response = curl_exec($ch); 
  // Get information about the response 
  $responseInfo=curl_getinfo($ch); 
  // Close the CURL connection
  curl_close($ch);
}

public function genTinyURL($url)
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

public function genEditMessage($userid, $name, $status)
{
  if ($userid != 2)
  {
    return $status . " edit by " . $name . " now available! Check out his/her "
      . "work here: " . genTinyURL("http://www.pumpproedits.com/users/" . $userid);
  }
  else
  {
    return $status . " edit based on Andamiro's work now available! Check it here: "
      . genTinyURL("http://www.pumpproedits.com/official");
  }
}