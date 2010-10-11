<?php
class itg_song_bpm extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get all of the BPM changes in this song.
  public function getBPMsBySongID($id)
  {
    return $this->db->where('song_id', $id)
      ->order_by('beat')->get('itg_song_bpm')->result();
  }
  
  // Get all of the BPM changes via the edit id.
	public function getBPMsByEditID($id)
	{
		return $this->db->from('itg_song_bpm b')
			->join('itg_edit_edit e', 'e.song_id = b.song_id')
			->where('e.old_edit_id', $id)
			->order_by('beat')
			->get()
			->result();
	}
}