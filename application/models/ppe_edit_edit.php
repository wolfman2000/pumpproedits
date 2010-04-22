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
    $data = array(
      'style' => substr($row['style'], 5),
      'song_id' => $row['id'],
      'user_id' => $row['uid'],
      'title' => $row['title'],
      'diff' => $row['diff'],
      'created_at' => $date,
      'updated_at' => $date,
    );
    $this->db->insert('ppe_edit_edit', $data);
    $id = $this->db->insert_id();
    
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
  }
  
  // Update an edit that is already in the database.
  function updateEdit($id, $row)
  {
    $data = array(
      'title' => $row['title'],
      'diff' => $row['diff'],
      'updated_at' => date('Y-m-d H:i:s')
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
  
  // Get all edits of the chosen song.
  public function getEditsBySong($sid)
  {
    $cols = 'a.id, a.diff, y.steps ysteps, y.jumps yjumps, y.holds yholds, y.mines ymines';
    $cols .= ', y.trips ytrips, y.rolls yrolls, y.fakes yfakes, y.lifts ylifts';
    $cols .= ', m.steps msteps, m.jumps mjumps, m.holds mholds, m.mines mmines';
    $cols .= ', m.trips mtrips, m.rolls mrolls, m.fakes mfakes, m.lifts mlifts';
    $cols .= ', a.user_id, b.name uname, a.title, a.style';
    return $this->db->select($cols)
      ->from('ppe_edit_edit a')
      ->join('ppe_user_user b', 'a.user_id = b.id')
      ->join('ppe_edit_player y', 'a.id = y.edit_id AND y.player = 1')
      ->join('ppe_edit_player m', 'a.id = m.edit_id AND m.player = 2', 'left')
      ->where('song_id', $sid)
      ->where('a.is_problem', 0)
      ->where('a.deleted_at', null)
      ->order_by('b.lc_name')
      ->order_by('a.title')
      ->get();
  }
  
  // Get all edits of the chosen user.
  public function getEditsByUser($uid)
  {
    $cols = 'a.id, a.diff, y.steps ysteps, y.jumps yjumps, y.holds yholds, y.mines ymines';
    $cols .= ', y.trips ytrips, y.rolls yrolls, y.fakes yfakes, y.lifts ylifts';
    $cols .= ', m.steps msteps, m.jumps mjumps, m.holds mholds, m.mines mmines';
    $cols .= ', m.trips mtrips, m.rolls mrolls, m.fakes mfakes, m.lifts mlifts';
    $cols .= ', a.song_id, b.name sname, a.title, a.style';
    return $this->db->select($cols)
      ->from('ppe_edit_edit a')
      ->join('ppe_song_song b', 'a.song_id = b.id')
      ->join('ppe_edit_player y', 'a.id = y.edit_id AND y.player = 1')
      ->join('ppe_edit_player m', 'a.id = m.edit_id AND m.player = 2', 'left')
      ->where('user_id', $uid)
      ->where('a.is_problem', 0)
      ->where('a.deleted_at', null)
      ->order_by('b.lc_name')
      ->order_by('a.title')
      ->get();
  }
}