<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
require "EditCharter.php";
class SongCharter extends EditCharter
{
	function __construct($params)
	{
		parent::__construct($params);
		$this->CI->load->model('ppe_song_measure');
		$this->sid = $params['sid'];
		$this->diff = $params['diff'];
		$this->abbr = $params['abbr'];
	}
	
	protected function genXMLHeader($measures, $notedata)
	{
		// Take advantage of the header already in play.
		parent::genXMLHeader($measures, $notedata);
		
		$str = "Arcade ${notedata['style']} chart — Pump Pro Edits";
		$txt = $this->xml->createTextNode($str);
		$node = $this->xml->getElementById("headTitle");
		$node->replaceChild($txt, $node->firstChild);
	}
	
	protected function genEditHeader($nd)
	{
		parent::genEditHeader($nd);
		
		$str = sprintf("%s %s - %d", $nd['sname'], ucfirst($nd['difficulty']), $nd['diff']);
		$txt = $this->xml->createTextNode($str);		
		$node = $this->xml->getElementById("editHead");
		$node->replaceChild($txt, $node->firstChild);
	}
	
	protected function getBPMData($id)
	{
		return $this->CI->ppe_song_bpm->getBPMsBySongID($id);
	}
	
	protected function getStopData($id)
	{
		return $this->CI->ppe_song_stop->getStopsBySongID($id);
	}
	
	protected function getAllNotes()
	{
		return $this->CI->ppe_song_measure->getNotes($this->sid, $this->abbr)->result_array();
	}
	
	protected function getMeasureCount()
	{
		return $this->CI->ppe_song_song->getMeasuresBySongID($this->sid);
	}
}
