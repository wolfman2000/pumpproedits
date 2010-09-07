<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_song_section extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	// Get all of the BPM changes in this song.
	public function getSectionsBySongID($id)
	{
		return $this->db->select('song_id, beat, section')
			->where('song_id', $id)
			->order_by('beat')->get('song_sections')->result();
	}
}
