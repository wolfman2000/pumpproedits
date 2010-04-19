<?php
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
    return $this->db->select('id')->where('pepper', $pepper)
      ->get('ppe_user_condiment')->row()->id;
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
  
  // Update the oregano value and return it.
  function updateOregano($id)
  {
    $this->load->helper('salter');
    $salt = genSalt();
    $md5 = hash("md5", date("YmdHis") . $salt); // date here, not pw: won't matter.
    $this->db->update('ppe_user_condiment', array('oregano' => $md5), "user_id = $id");
    return $md5;
    
  }
}