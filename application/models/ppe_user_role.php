<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_user_role extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  function getRolesById($id)
  {
    $q = $this->db->select('a.role')
      ->join('ppe_user_power b', 'a.id = b.role_id')
      ->where('b.user_id', $id)->get('ppe_user_role a');
    
    $a = array();
    foreach ($q->result() as $r)
    {
      array_push($a, $r->role);
    }
    return $a;
  }
  
  // confirm if the user is banned.
  function getIsUserBanned($id)
  {
    return $this->db->select('a.role')
      ->join('ppe_user_power b', 'a.id = b.role_id')
      ->where('b.user_id', $id)
      ->where_in('a.role', array('banned', 'forbidden'))
      ->get('ppe_user_role a')->num_rows();
  }
}
  
