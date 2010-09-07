<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_song_stop extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get all of the stops in this song.
  public function getStopsBySongID($id)
  {
    return $this->db->where('song_id', $id)
      ->order_by('beat')->get('ppe_song_stop')->result();
  }
}
