<?php
class Constants extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  function getTwitterName()
  {
    return $this->db->select('value')->where('name', 'twitter_user')
      ->get('constants')->row()->value;
  }
  
  function getTwitterPass()
  {
    return $this->db->select('value')->where('name', 'twitter_pass')
      ->get('constants')->row()->value;
  }
}
  