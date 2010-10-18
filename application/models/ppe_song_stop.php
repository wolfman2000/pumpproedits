<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_song_stop extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	// Get all of the stops in this song.
	public function getStopsBySongID($id, $override = 0)
	{
		$this->db->from('ppe_song_stop')
			->where('song_id', $id)
			->order_by('beat')
			->order_by('is_public', 'desc');
		if (!$override) { $this->db->where('is_public', '1'); }
		return $this->db->get()->result();
	}
	
	// Get all of the BPM stops via the edit id.
	public function getStopsByEditID($id, $override = 0)
	{
		$this->db->from('ppe_song_stop s')
			->join('ppe_edit_edit e', 'e.song_id = s.song_id')
			->where('e.id', $id)
			->order_by('beat')
			->order_by('is_public', 'desc');
		if (!$override) { $this->db->where('is_public', '1'); }
		return $this->db->get()->result();
	}
}
