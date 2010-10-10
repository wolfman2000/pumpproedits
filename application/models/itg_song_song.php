<?php
class itg_song_song extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // ensure the song exists.
  public function doesSongExist($sid)
  {
    return $this->db->select('name')->where('id', $sid)
      ->get('itg_song_song')->num_rows();
  }
  
  // get the number of songs that have a game
  public function getSongCountWithGame()
  {
    return $this->db->select('COUNT(a.name) names')
      ->order_by('LOWER(a.name)')
      ->get('itg_song_song a')
      ->row()->names;
  }
  
  // get EVERYTHING about the song by its ID.
  public function getSongRow($id)
  {
    return $this->db->where('id', $id)->get('itg_song_song')->row();
  }
  
  // get the ID of the song by its lowercased name.
  public function getIDBySong($song)
  {
    return $this->db->select('id')->where('LOWER(name)', strtolower($song))
      ->get('itg_song_song')->row()->id;
  }
  
  // get the name of the song by its ID.
  public function getSongByID($id)
  {
    return $this->db->select('name')->where('id', $id)
      ->get('itg_song_song')->row()->name;
  }
  
  // get all of the information needed for base edits.
  public function getBaseEdits($page = 0)
  {
    return $this->db->select('a.name, a.id, a.abbr')
      ->order_by('LOWER(a.name)')
      ->limit(APP_BASE_EDITS_PER_PAGE, $page)
      ->get('itg_song_song a');
  }
  
  // get the list of songs with edits.
  public function getSongsWithEdits()
  {
    return $this->db->select('a.id, a.name core, COUNT(b.id) AS num_edits')
      ->from('itg_song_song a')
      ->join('itg_edit_edit b', 'a.id = b.song_id')
      ->order_by('LOWER(name)')
      ->group_by(array('a.id', 'a.name'))
      ->get();
  }
  
  // Get all songs that have an assigned game and difficulty.
  public function getSongsWithGameAndDiff()
  {
    return $this->db->select('a.id, a.name, g.song_id sid, MIN(g.game_id) AS gid')
      ->join('itg_song_game g', 'a.id = g.song_id', 'left')
      ->where('a.is_problem', 0)
      ->group_by(array('a.id, a.name', 'sid'))
      ->order_by('gid')
      ->order_by('name')
      ->get('itg_song_song a');
  }
  
  
}