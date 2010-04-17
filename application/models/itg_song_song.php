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
      ->join('itg_song_game g', 'a.id = g.song_id AND g.game_id > 1', 'left')
      ->order_by('a.lc_name')
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
    return $this->db->select('id')->where('lc_name', strtolower($song))
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
    return $this->db->select('a.name, a.id, a.abbr, g.game_id tmp')
      ->join('itg_song_game g', 'a.id = g.song_id AND g.game_id > 1', 'left')
      ->order_by('a.lc_name')
      ->limit(APP_BASE_EDITS_PER_PAGE, $page)
      ->get('itg_song_song a');
  }
  
  // get the list of songs with edits.
  public function getSongsWithEdits()
  {
    return $this->db->select('id, name core, num_edits')
      ->from('itg_song_song')
      ->where('is_problem', false)
      ->where('num_edits >', 0)
      ->order_by('lc_name')
      ->get();
  }
  
  // Get all songs that have an assigned game and difficulty.
  public function getSongsWithGameAndDiff()
  {
    return $this->db->select('a.id, a.name, g.song_id sid, MIN(g.game_id) AS gid, COUNT(d.diff_id) AS did')
      ->join('itg_song_game g', 'a.id = g.song_id', 'left')
      ->join('itg_song_difficulty d', 'a.id = d.song_id', 'left')
      ->where('a.is_problem', 0)
      ->group_by(array('a.id, a.name', 'sid'))
      ->having('did >', 0)
      ->order_by('gid')
      ->order_by('name')
      ->get('itg_song_song a');
  }
  
  // Get each individual song difficulty. NULL = not available.
  public function getDifficulties($sid)
  {
    $cols = 'z.id, a.diff_id ez, b.diff_id nr, c.diff_id hr, d.diff_id cz, ';
    $cols .= 'e.diff_id hd, f.diff_id fs, g.diff_id nm, h.diff_id rt';
    return $this->db->select($cols)
      ->join('itg_song_difficulty a', 'z.id = a.song_id AND a.diff_id = 1', 'left')
      ->join('itg_song_difficulty b', 'z.id = b.song_id AND b.diff_id = 2', 'left')
      ->join('itg_song_difficulty c', 'z.id = c.song_id AND c.diff_id = 3', 'left')
      ->join('itg_song_difficulty d', 'z.id = d.song_id AND d.diff_id = 4', 'left')
      ->join('itg_song_difficulty e', 'z.id = e.song_id AND e.diff_id = 5', 'left')
      ->join('itg_song_difficulty f', 'z.id = f.song_id AND f.diff_id = 6', 'left')
      ->join('itg_song_difficulty g', 'z.id = g.song_id AND g.diff_id = 7', 'left')
      ->join('itg_song_difficulty h', 'z.id = h.song_id AND h.diff_id = 8', 'left')
      ->where('z.id', $sid)->get('itg_song_song z')->row_array();
  }
}