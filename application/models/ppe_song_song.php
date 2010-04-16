<?php
class Ppe_song_song extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // get EVERYTHING about the song by its ID.
  public function getSongRow($id)
  {
    return $this->db->where('id', $id)->get('ppe_song_song')->row();
  }
  
  // get the ID of the song by its lowercased name.
  public function getIDBySong($song)
  {
    return $this->db->select('id')->where('lc_name', strtolower($song))
      ->get('ppe_song_song')->row()->id;
  }
  
  // get the name of the song by its ID.
  public function getSongByID($id)
  {
    return $this->db->select('name')->where('id', $id)
      ->get('ppe_song_song')->row()->name;
  }
  
  // get the list of songs with edits.
  public function getSongsWithEdits()
  {
    return $this->db->select('id, name core, num_edits')
      ->from('ppe_song_song')
      ->where('is_problem', false)
      ->where('num_edits >', 0)
      ->order_by('lc_name')
      ->get();
  }
  
  // Get all songs that have an assigned game and difficulty.
  public function getSongsWithGameAndDiff()
  {
    return $this->db->select('a.id, a.name, g.song_id sid, MIN(g.game_id) AS gid, COUNT(d.diff_id) AS did')
      ->join('ppe_song_game g', 'a.id = g.song_id', 'left')
      ->join('ppe_song_difficulty d', 'a.id = d.song_id', 'left')
      ->where('a.is_problem', 0)
      ->group_by(array('a.id, a.name', 'sid'))
      ->having('did >', 0)
      ->order_by('gid')
      ->order_by('name')
      ->get('ppe_song_song a');
  }
}