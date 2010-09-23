<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_song_bpm extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	// Get all of the BPM changes in this song.
	public function getBPMsBySongID($id)
	{
		return $this->db->where('song_id', $id)
			->order_by('beat')->get('ppe_song_bpm')->result();
	}
	
	// Get all of the BPM changes via the edit id.
	public function getBPMsByEditID($id)
	{
		return $this->db->from('ppe_song_bpm b')
			->join('ppe_edit_edit e', 'e.song_id = b.song_id')
			->where('e.id', $id)
			->order_by('beat')
			->get()
			->result();
	}
}
