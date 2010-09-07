<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_note_skin extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get the list of valid note skins
  function getNoteSkins($lower = false)
  {
  	  $r = $this->db->select(($lower ? 'LOWER(name)' : 'name') . ' AS name')
  	  	->order_by('id')->get('ppe_note_skin')->result();
  	  $ret = array();
  	  foreach ($r as $q)
  	  {
  	  	  $ret[] = $q->name;
  	  }
  	  return $ret;
  }
  
  // Format the list of noteskins used by the forms.
  function getSelectSkins()
  {
	  $choices = array();
	  foreach ($this->getNoteSkins() as $c)
	  {
		  $choices[] = array("value" => strtolower($c), "text" => $c,
		  	  "selected" => ($c == "Original" ? true : false));
	  }
	  $ret = array("for" => "noteskin", "label" => "Noteskin", 
	  	  "choices" => $choices);
	  return $ret;
  }
}
