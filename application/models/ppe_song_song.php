<?php
class Ppe_song_song extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  public function getSongsWithEdits()
  {
    return $this->db->select('id, name core, num_edits')
      ->from('ppe_song_song')
      ->where('is_problem', false)
      ->where('num_edits >', 0)
      ->order_by('lc_name')
      ->get();
  }
}