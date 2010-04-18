<?php
class Ppe_user_user extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // get the name of the user by its ID.
  public function getUserByID($id)
  {
    return $this->db->select('name')->where('id', $id)
      ->get('ppe_user_user')->row()->name;
  }
  
  // Check if the user is confirmed based on ID.
  function getConfirmedByID($id)
  {
    return $this->db->select('is_confirmed')->where('id', $id)
      ->get('ppe_user_user')->row()->is_confirmed;
  }
  
  // get the id of the user via email.
  function getIDByEmail($email)
  {
    return $this->db->select('id')->where('lc_email', strtolower($email))
      ->get('ppe_user_user')->row()->id;
  }
  
  // get the ID of the user via username.
  function getIDByEmail($name)
  {
    return $this->db->select('id')->where('lc_name', strtolower($name))
      ->get('ppe_user_user')->row()->id;
  }
  
  // get the list of users with edits.
  public function getUsersWithEdits()
  {
    return $this->db->select('id, name core, num_edits')
      ->from('ppe_user_user')
      ->where('num_edits >', 0)
      ->where_not_in('id', array(2, 95))
      ->order_by('lc_name')
      ->get();
  }
  
  // get the name of the author of the edit.
  public function getUserByEditID($eid)
  {
    return $this->db->select('u.name')
      ->join('ppe_edit_edit e', 'u.id = e.user_id')
      ->where('e.id', $eid)->get('ppe_user_user u')
      ->row()->name;
  }
  
  // get the intended name based off of the lowercase name.
  public function getCasedName($name)
  {
    return $this->db->select('name')
      ->where('lc_name', strtolower($name))
      ->get('ppe_user_user')->row()->name;
  }
}