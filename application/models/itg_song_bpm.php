<?php
class itg_song_bpm extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get all of the BPM changes in this song.
  public function getBPMsBySongID($id)
  {
    return $this->db->where('song_id', $id)
      ->order_by('beat')->get('itg_song_bpm')->result();
  }
}