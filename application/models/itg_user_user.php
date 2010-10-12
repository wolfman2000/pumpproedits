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
    return $this->db->select('id, name AS core, min_edits AS num_edits')
      ->from('users_with_edits')
      ->order_by('LOWER(name)')
      ->get();
  }
  
  // get the name of the author of the edit (old edit ID style)
  public function getUserByOldEditID($oid)
  {
    return $this->db->select('a.name aname')
      ->join('itg_edit_edit b', 'a.id = b.user_id')
      ->where('b.old_edit_id', $oid)
      ->get('itg_user_user a')->row()->aname;
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