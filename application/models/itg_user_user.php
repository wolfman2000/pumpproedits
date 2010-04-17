<?php
class itg_user_user extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // get the name of the user by its ID.
  public function getUserByID($id)
  {
    return $this->db->select('name')->where('id', $id)
      ->get('itg_user_user')->row()->name;
  }
  
  // get the list of users with edits.
  public function getUsersWithEdits()
  {
    return $this->db->select('id, name core, num_edits')
      ->from('itg_user_user')
      ->where('num_edits >', 0)
      ->where_not_in('id', array(2, 95))
      ->order_by('lc_name')
      ->get();
  }
  
  // get the name of the author of the edit.
  public function getUserByEditID($eid)
  {
    return $this->db->select('u.name')
      ->join('itg_edit_edit e', 'u.id = e.user_id')
      ->where('e.id', $eid)->get('itg_user_user u')
      ->row()->name;
  }
}