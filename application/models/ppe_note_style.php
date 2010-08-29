<?php
class Ppe_note_style extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get the list of valid note styles
  function getNoteStyles()
  {
  	  $r = $this->db->select('LOWER(name) name')
  	  	->get('ppe_note_style')->result();
  	  $ret = array();
  	  foreach ($r as $q)
  	  {
  	  	  $ret[] = $q->name;
  	  }
  	  return $ret;
  }
}