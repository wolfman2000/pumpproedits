<?php
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