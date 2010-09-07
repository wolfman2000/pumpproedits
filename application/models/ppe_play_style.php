<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_play_style extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get the play style ID
  function getPlayStyleID($style)
  {
  	  return $this->db->select('id')->where('style', $style)
  	  	->get('ppe_play_style')->row()->id;
  }
}
