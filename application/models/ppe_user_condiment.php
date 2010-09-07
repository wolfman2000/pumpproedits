<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_user_condiment extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // check the password matches.
  function checkPassword($salt, $pass)
  {
    $pepper = hash("sha256", $pass . $salt);
    $q = $this->db->select('user_id')->where('pepper', $pepper)
      ->get('ppe_user_condiment')->row();
    return $q ? $q->user_id : false;
  }
  
  // check the user exists.
  function checkUser($name, $pass)
  {
    $q = $this->db->select('a.salt')
      ->join('ppe_user_user b', 'b.id = a.user_id')
      ->where('b.lc_name', strtolower($name))
      ->get('ppe_user_condiment a')->row();
    return $q ? $this->checkPassword($q->salt, $pass) : false;
  }
  
  // Confirm the user through password and special string.
  function confirmUser($oregano, $pass)
  {
    $q = $this->db->select('salt')->where('oregano', $oregano)
      ->get('ppe_user_condiment')->row();
    
    return $q ? $this->checkPassword($q->salt, $pass) : false;
  }
  
  // get the user's recent oregano.
  function getOreganoByID($id)
  {
    return $this->db->select('oregano')->where('user_id = ?', $id)
      ->get('ppe_user_condiment')->row()->oregano;
  }

  function getIDByOregano($oregano)
  {
    return $this->db->select('user_id')->where('oregano', $oregano)
      ->get('ppe_user_condiment')->row()->user_id;
  }
  
  // Update the oregano value and return it.
  function updateOregano($id)
  {
    $this->load->helper('salter');
    $salt = genSalt();
    $md5 = hash("md5", date("YmdHis") . $salt); // date here, not pw: won't matter.
    $this->db->update('ppe_user_condiment', array('oregano' => $md5), "user_id = $id");
    return $md5;
  }
  
  // Update the pepper/password value and return it.
  function setPassword($id, $pass)
  {
    $this->load->helper('salter');
    $salt = genSalt();
    $pepper = hash("sha256", $pass . $salt);
    $this->db->update('ppe_user_condiment', array('pepper' => $pepper, 'salt' => $salt), "user_id = $id");
  }
}
