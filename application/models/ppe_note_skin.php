<?php
class Ppe_note_skin extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Get the list of valid note styles
  function getNoteSkins()
  {
  	  $r = $this->db->select('name')
  	  	->get('ppe_note_skin')->result();
  	  $ret = array();
  	  foreach ($r as $q)
  	  {
  	  	  $ret[] = $q->name;
  	  }
  	  return $ret;
  }
}