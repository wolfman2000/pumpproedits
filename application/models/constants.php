<?php
class Constants extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	private function getValue($name)
	{
		return $this->db->select('value')->where('name', $name)
			->get('constants')->row()->value;
	}
	
	function getTwitterName()
	{
		return $this->getValue('twitter_user');
	}
	
	function getTwitterPass()
	{
		return $this->getValue('twitter_pass');
	}
	
	function getConsumerKey()
	{
		return $this->getValue('consumer_key');
	}
	
	function getConsumerSecret()
	{
		return $this->getValue('consumer_secret');
	}
	function getUserToken()
	{
		return $this->getValue('oauth_token');
	}
	function getUserSecret()
	{
		return $this->getValue('oauth_token_secret');
	}
}
  