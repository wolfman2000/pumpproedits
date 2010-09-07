<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_note_style extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get the list of valid note styles
  function getNoteStyles($lower = false)
  {
  	  $r = $this->db->select(($lower ? 'LOWER(name)' : 'name') . ' AS name')
  	  	->order_by('id')->get('ppe_note_style')->result();
  	  $ret = array();
  	  foreach ($r as $q)
  	  {
  	  	  $ret[] = $q->name;
  	  }
  	  return $ret;
  }
  
  // Format the list of note styles used by the forms.
  function getSelectStyles()
  {
	  $choices = array();
	  foreach ($this->getNoteStyles() as $c)
	  {
		  $choices[] = array("value" => strtolower($c), "text" => $c,
		  	  "selected" => ($c == "Classic" ? true : false));
	  }
	  $ret = array("for" => "kind", "label" => "Note Style", 
	  	  "choices" => $choices);
	  return $ret;
  }
}
