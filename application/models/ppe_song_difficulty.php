<?php
class Ppe_song_difficulty extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Place a record that a chart exists...if it doesn't already.
  function addChart($songid, $diff)
  {
    // First, store the subquery.
    $sub = $this->db->select('id')->from('ppe_game_difficulty')
      ->where('diff', $diff)->_compile_select();
    $this->db->_reset_select();
    
    if (!$this->db->select('id')->where('song_id', $songid)
      ->where('diff_id', "($sub)")->get('ppe_song_difficulty')->num_rows())
    {
      // insert away.
      $data = array('song_id' => $songid, 'diff_id' => "($sub)");
      $this->db->insert('ppe_song_difficulty', $data);
      $this->db->cache_delete_all();
    }
  }
}