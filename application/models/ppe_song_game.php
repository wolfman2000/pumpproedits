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
  	  return $this->db->where('song_id', $sid)
  	  	->get('song_routine_compatible')->num_rows();
  }
  
  // Get the list of difficulties that this song can play under.
  function getValidDifficulties($sid)
  {
  	  return $this->db->select('style')->where('id', $sid)
  	  	->order_by('pid')->get('valid_song_styles');
  }
}