<?php
class itg_song_stop extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get all of the stops in this song.
  public function getStopsBySongID($id)
  {
    return $this->db->where('song_id', $id)
      ->order_by('beat')->get('itg_song_stop')->result();
  }
  
  // Get all of the BPM stops via the edit id.
	public function getStopsByEditID($id)
	{
		return $this->db->from('itg_song_stop s')
			->join('itg_edit_edit e', 'e.song_id = s.song_id')
			->where('e.old_edit_id', $id)
			->order_by('beat')
			->get()
			->result();
	}
}