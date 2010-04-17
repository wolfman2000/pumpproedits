<?php
class itg_edit_edit extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Confirm if the edit exists.
  public function checkExistance($eid)
  {
    return $this->db->select('id')->where('id', $eid)
      ->get('itg_edit_edit')->num_rows() > 0;
  }
  
  // Get all of the user edits for possible charting.
  public function getNonProblemEdits()
  {
    return $this->db->select('a.id, u.name uname, a.style, a.title, a.diff, s.name sname')
      ->from('itg_edit_edit a')
      ->join('itg_user_user u', 'a.user_id = u.id')
      ->join('itg_song_song s', 'a.song_id = s.id')
      ->where('a.is_problem', 0)
      ->where('a.deleted_at', null)
      ->order_by('u.lc_name')
      ->order_by('s.lc_name')
      ->order_by('a.title')
      ->order_by('a.style')
      ->get();
  }
  
  // Get all edits of the chosen song.
  public function getEditsBySong($sid)
  {
    $cols = 'a.id, a.diff, y.steps ysteps, y.jumps yjumps, y.holds yholds, y.mines ymines';
    $cols .= ', y.trips ytrips, y.rolls yrolls';
    $cols .= ', a.user_id, b.name uname, a.title, a.style';
    return $this->db->select($cols)
      ->from('itg_edit_edit a')
      ->join('itg_user_user b', 'a.user_id = b.id')
      ->join('itg_edit_player y', 'a.id = y.edit_id AND y.player = 1')
      ->where('song_id', $sid)
      ->where('a.is_problem', 0)
      ->order_by('b.lc_name')
      ->order_by('a.title')
      ->get();
  }
  
  // Get all edits of the chosen user.
  public function getEditsByUser($uid)
  {
    $cols = 'a.id, a.diff, y.steps ysteps, y.jumps yjumps, y.holds yholds, y.mines ymines';
    $cols .= ', y.trips ytrips, y.rolls yrolls';
    $cols .= ', a.song_id, b.name sname, a.title, a.style,';
    return $this->db->select($cols)
      ->from('itg_edit_edit a')
      ->join('itg_song_song b', 'a.song_id = b.id')
      ->join('itg_edit_player y', 'a.id = y.edit_id AND y.player = 1')
      ->where('user_id', $uid)
      ->where('a.is_problem', 0)
      ->order_by('b.lc_name')
      ->order_by('a.title')
      ->get();
  }
}