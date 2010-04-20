<?php
class Ppe_song_game extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Any game after Pro 1 will have routine mode.
  function getRoutineCompatible($sid)
  {
    return $this->db->select('id')->where('game_id >=', 2)
      ->where('song_id', $sid)->get('ppe_song_game')->num_rows();
  }
}