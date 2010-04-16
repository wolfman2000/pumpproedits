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
}