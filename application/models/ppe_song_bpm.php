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
	public function getBPMsBySongID($id, $override = 0)
	{
		$this->db->from('ppe_song_bpm')
			->where('song_id', $id)
			->order_by('beat')
			->order_by('is_public', 'desc');
		if (!$override) { $this->db->where('is_public', '1'); }
		return $this->db->get()->result();
	}
	
	// Get all of the BPM changes via the edit id.
	public function getBPMsByEditID($id, $override = 0)
	{
		$this->db->from('ppe_song_bpm b')
			->join('ppe_edit_edit e', 'e.song_id = b.song_id')
			->where('e.id', $id)
			->order_by('beat')
			->order_by('b.is_public', 'desc');
		if (!$override) { $this->db->where('b.is_public', '1'); }
		return $this->db->get()->result();
	}
}
