<?php
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