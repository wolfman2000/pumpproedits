<?php
class itg_song_stop extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get all of the stops in this song.
  public function getStopsBySongID($id)
  {
    return $this->db->where('song_id', $id)
      ->order_by('beat')->get('itg_song_stop')->result();
  }
}