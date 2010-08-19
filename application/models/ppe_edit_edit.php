<?php
class Ppe_edit_edit extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Add an edit that is new to the database.
  function addEdit($row)
  {
    $date = date('Y-m-d H:i:s');
    
    $id = $this->db->select('MAX(id) AS lid')->get('ppe_edit_edit')
      ->row()->lid + 1;

    $data = array(
      'id' => $id,
      'style' => substr($row['style'], 5),
      'song_id' => $row['id'],
      'user_id' => $row['uid'],
      'title' => $row['title'],
      'diff' => $row['diff'],
      'created_at' => $date,
      'updated_at' => $date,
      'is_public' => (isset($row['public']) ? $row['public'] : 1),
    );
    $this->db->insert('ppe_edit_edit', $data);
    #$id = $this->db->insert_id();
    
    $players = array(0);
    if ($row['style'] === "pump-routine")
    {
      $players = array(0, 1);
    }
    foreach ($players as $player)
    {
       $data = array(
        'steps' => $row['steps'][$player],
        'jumps' => $row['jumps'][$player],
        'holds' => $row['holds'][$player],
        'mines' => $row['mines'][$player],
        'trips' => $row['trips'][$player],
        'rolls' => $row['rolls'][$player],
        'lifts' => $row['lifts'][$player],
        'fakes' => $row['fakes'][$player],
        'player' => $player + 1,
        'edit_id' => $id,
      );
      $this->db->insert('ppe_edit_player', $data);
    }
    return $id; // Return the edit ID.
  }
  
  // Update an edit that is already in the database.
  function updateEdit($id, $row)
  {
    $data = array(
      'title' => $row['title'],
      'diff' => $row['diff'],
      'updated_at' => date('Y-m-d H:i:s'),
      'is_public' => $row['public'],
    );
    $this->db->update('ppe_edit_edit', $data, "id = $id");
    $players = array(0);
    if ($row['style'] === "pump-routine")
    {
      $players = array(0, 1);
    }
    foreach ($players as $player)
    {
      $data = array(
        'steps' => $row['steps'][$player],
        'jumps' => $row['jumps'][$player],
        'holds' => $row['holds'][$player],
        'mines' => $row['mines'][$player],
        'trips' => $row['trips'][$player],
        'rolls' => $row['rolls'][$player],
        'lifts' => $row['lifts'][$player],
        'fakes' => $row['fakes'][$player],
      );
      $where = array('edit_id' => $id, 'player' => $player + 1);
      $this->db->update('ppe_edit_player', $data, $where);
    }
  }
  
  // Get a list of edits with same song/style/title, excluding itself.
  function checkDuplicates($sid, $uid, $style, $title, $eid = null)
  {
    $q = $this->db->select('id')
      ->where('song_id', $sid)->where('user_id', $uid)
      ->where('style', $style)->where('title', $title);
    if ($eid)
    {
      $q->where('id !=', $eid);
    }
    return $q->get('ppe_edit_edit')->num_rows();
  }
  
  // Get the list of edits by the user. Should problem ones be included?
  function getSVGEdits($uid)
  {
    return $this->db->select('a.id, a.style, a.title, a.diff, s.abbr, s.name')
      ->join('ppe_song_song s', 'a.song_id = s.id')
      ->where('a.user_id', $uid)
      ->order_by('s.lc_name')
      ->order_by('a.title')
      ->order_by('a.style')
      ->get('ppe_edit_edit a')->result_array();
  }
  
  // Confirm if the edit exists.
  public function checkExistance($eid)
  {
    return $this->db->select('id')->where('id', $eid)
      ->get('ppe_edit_edit')->num_rows() > 0;
  }
  
  // Confirm if the edit exists and is not deleted.
  function checkExistsAndActive($eid)
  {
    return $this->db->select('id')->where('id', $eid)
      ->where('deleted_at', null)
      ->get('ppe_edit_edit')->num_rows() > 0;
  }
  
  // Get all of the user edits for possible charting.
  public function getNonProblemEdits()
  {
    return $this->db->select('a.id, u.name uname, a.style, a.title, a.diff, s.name sname')
      ->from('ppe_edit_edit a')
      ->join('ppe_user_user u', 'a.user_id = u.id')
      ->join('ppe_song_song s', 'a.song_id = s.id')
      ->where('a.is_problem', 0)
      ->where('a.deleted_at', null)
      ->order_by('u.lc_name')
      ->order_by('s.lc_name')
      ->order_by('a.title')
      ->order_by('a.style')
      ->get();
  }
  
  // Get all of the user edits that could be deleted.
  public function getEditsToDelete($uid)
  {
    return $this->db->select('a.id, a.style, a.title, a.diff, s.name sname')
      ->from('ppe_edit_edit a')
      ->join('ppe_song_song s', 'a.song_id = s.id')
      ->where('a.is_problem', 0)
      ->where('a.deleted_at', null)
      ->where('a.user_id', $uid)
      ->order_by('s.lc_name')
      ->order_by('a.title')
      ->order_by('a.style')
      ->get();
  }
  
  // "Remove" the selected edits. Or maybe reactivate.
  function removeEdits($ids, $time = 1)
  {
    if ($time) { $time = date('Y-m-d H:i:s'); }
    else { $time = null; }
    $data['deleted_at'] = $time;
    if ($time) { $data['title'] = null; }
    $this->db->where_in('id', $ids)->update('ppe_edit_edit', $data);
  }
  
  // Determine if the edit being uploaded is new or old.
  function getIDByUpload($row)
  {
    $q = $this->db->select('id')->where('title', $row['title'])
      ->where('style', substr($row['style'], 5))
      ->where('user_id', $row['uid'])
      ->get('ppe_edit_edit');
    return $q->num_rows() ? $q->row()->id : null;
  }
  
  
  // Have a separate place to get common columns used.
	private function _getCommonCols()
	{
		$cols = 'a.id, a.diff, y.steps ysteps, y.jumps yjumps, y.holds yholds, y.mines ymines';
    $cols .= ', y.trips ytrips, y.rolls yrolls, y.fakes yfakes, y.lifts ylifts';
    $cols .= ', m.steps msteps, m.jumps mjumps, m.holds mholds, m.mines mmines';
    $cols .= ', m.trips mtrips, m.rolls mrolls, m.fakes mfakes, m.lifts mlifts';
    return $cols . ', a.title, a.style';
	}
	
	// Common function to get (10) of the edits of a chosen parameter.
  private function _getEdits($params)
  {
    $cols = 'a.id, a.diff, y.steps ysteps, y.jumps yjumps, y.holds yholds, y.mines ymines';
    $cols .= ', y.trips ytrips, y.rolls yrolls, y.fakes yfakes, y.lifts ylifts';
    $cols .= ', m.steps msteps, m.jumps mjumps, m.holds mholds, m.mines mmines';
    $cols .= ', m.trips mtrips, m.rolls mrolls, m.fakes mfakes, m.lifts mlifts';
    $cols .= ', a.title, a.style, ' . $params['cols'];
    return $this->db->select($cols)
      ->from('ppe_edit_edit a')
      ->join($params['join'] . ' b', 'a.' . $params['opposite'] . '_id = b.id')
      ->join('ppe_edit_player y', 'a.id = y.edit_id AND y.player = 1')
      ->join('ppe_edit_player m', 'a.id = m.edit_id AND m.player = 2', 'left')
      
      ->where($params['main'], $params['main_id'])
      ->where('a.is_problem', 0)
      ->where('a.is_public', 1)
      ->where('a.deleted_at', null)
      ->order_by('b.lc_name')
      ->order_by('a.title')
      ->limit(APP_MAX_EDITS_PER_PAGE, ($params['page'] - 1) * APP_MAX_EDITS_PER_PAGE)
      ->get();
  }
  
	// Common function that uses the full view.
	private function _getGoodEdits($order, $limit = 10000)
	{
		$this->db->from('full_edit_stats')
			->where('a.is_problem', 0)
			->where('a.is_public', 1)
			->where('a.deleted_at', null);
		foreach ($order as $o):
			$this->db->order_by($o['column'], $o['direction']);
		endforeach;
		$this->db->limit($limit);
		return $this->db->get();
	}
	
	// Get 5 edits for the entry page.
	public function getEditsEntry()
	{
		return $this->_getGoodEdits(array('column' => 'random', 'direction' => 'asc'));
	}
	
  // Get all edits of the chosen song.
  public function getEditsBySong($sid, $page = 1)
  {
    return $this->_getEdits(array('cols' => 'a.user_id, b.name uname',
      'join' => 'ppe_user_user', 'opposite' => 'user',
      'main' => 'song_id', 'main_id' => $sid, 'page' => $page));
  }
  
  // Get all edits of the chosen user.
  public function getEditsByUser($uid, $page = 1)
  {
    return $this->_getEdits(array('cols' => 'a.song_id, b.name sname',
      'join' => 'ppe_song_song', 'opposite' => 'song',
      'main' => 'user_id', 'main_id' => $uid, 'page' => $page));
  }
  
  // Common function to get the max number of edits possible.
  private function _getEditCount($params)
  {
    return $this->db->select('id')
      ->where($params['where'], $params['cond'])
      ->where('a.is_problem', 0)
      ->where('a.is_public', 1)
      ->where('a.deleted_at', null)
      ->get('ppe_edit_edit a')->num_rows();
  }
  
  public function getUserEditCount($uid)
  {
    return $this->_getEditCount(array('where' => 'user_id', 'cond' => $uid));
  }
  
  public function getSongEditCount($sid)
  {
    return $this->_getEditCount(array('where' => 'song_id', 'cond' => $sid));
  }
}
