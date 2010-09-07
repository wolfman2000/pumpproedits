<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_user_power extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  private function powerCheck($userid, $roleid)
  {
    return $this->db->select('id')->where('role_id >=', $roleid)
      ->where('user_id', $userid)->get('ppe_user_power')->num_rows();
  }
  
  // Only allow mods and higher to mess with official charts/edits.
  function canEditOfficial($userid)
  {
    return $this->powerCheck($userid, 4);
  }
  
  // Only allow admins with the power to fix up other user's charts.
  function canEditOthers($userid)
  {
    return $this->powerCheck($userid, 5);
  }
}
