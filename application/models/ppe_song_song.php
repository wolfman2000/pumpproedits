<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_song_song extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get the needed information for the edit creator.
  function getCreatorData($sid)
  {
    return $this->db->select('name, abbr, measures, duration')
      ->where('id', $sid)->get('ppe_song_song')->row();
  }
  
  // ensure the song exists.
  public function doesSongExist($sid)
  {
    return $this->db->select('name')->where('id', $sid)
      ->get('ppe_song_song')->num_rows();
  }
  
  // get the number of songs that have a game
  public function getSongCountWithGame()
  {
    return $this->db->select('COUNT(a.name) names')
      ->join('ppe_song_game g', 'a.id = g.song_id AND g.game_id > 1', 'left')
      ->order_by('a.lc_name')
      ->get('ppe_song_song a')
      ->row()->names;
  }
  
  // get EVERYTHING about the song by its ID.
  public function getSongRow($id)
  {
    return $this->db->where('id', $id)->get('ppe_song_song')->row();
  }
  
  // get the ID of the song by its lowercased name.
  public function getIDBySong($song)
  {
    $q = $this->db->select('id')->where('lc_name', strtolower($song))
      ->get('ppe_song_song');
    return $q->num_rows() ? $q->row()->id : null;
  }
  
  // get the name of the song by its ID.
  public function getSongByID($id)
  {
    $q = $this->db->select('name')->where('id', $id)
      ->get('ppe_song_song');
    return $q->num_rows() ? $q->row()->name : null;
  }
  
  // get all of the information needed for base edits.
  public function getBaseEdits($page = 0)
  {
    return $this->db->select('a.name, a.id, a.abbr, g.game_id tmp')
      ->join('ppe_song_game g', 'a.id = g.song_id AND g.game_id > 1', 'left')
      ->where('is_problem', 0)
      ->order_by('a.lc_name')
      ->limit(APP_BASE_EDITS_PER_PAGE, ($page - 1) * APP_BASE_EDITS_PER_PAGE)
      ->get('ppe_song_song a');
  }
  
  // get the list of songs with edits.
  public function getSongsWithEdits()
  {
    return $this->db->select('a.id, a.name core, COUNT(b.id) num_edits')
      ->from('ppe_song_song a')
      ->join('ppe_edit_edit b', 'a.id = b.song_id')
      ->where('b.is_problem', 0)
      ->where('b.deleted_at', null)
      ->where('b.is_public', 1)
      ->group_by(array('a.id', 'a.name'))
      ->order_by('a.lc_name')
      ->get();
  }
  
  // get the songs in order of their game appearance.
  function getSongsWithGame()
  {
  	  return $this->db->select('sid id, sid sid, name, first_game_id gid, first_game game')
  	  	->where('is_problem', 0)
  	  	->order_by('gid')
  	  	->order_by('name')
  	  	->get('song_first_last_games');
  }
  
	// Get all songs that have an assigned game and difficulty.
	public function getSongsWithGameAndDiff()
	{
		return $this->db
			->order_by('gid')
			->order_by('name')
			->get('song_game_chart_sort');
  }
}
