<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_edit_player extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Add an edit's stats.
  function addEdit($id, $row, $player = 0)
  {
    $data = array(
      'steps' => $row['steps'][$player],
      'jumps' => $row['jumps'][$player],
      'holds' => $row['holds'][$player],
      'mines' => $row['mines'][$player],
      'trips' => $row['trips'][$player],
      'rolls' => $row['rolls'][$player],
      'lifts' => $row['lifts'][$player],
      'fakes' => $row['fakes'][$player],
      'player' => $player,
    );
    $this->db->insert('ppe_edit_player', $data);
  }
  
  // Update an edit's stats.
  function updateEdit($id, $row, $player = 0)
  {
    $data = array(
      'steps' => $row['steps'][$player],
      'jumps' => $row['jumps'][$player],
      'holds' => $row['holds'][$player],
      'mines' => $row['mines'][$player],
      'trips' => $row['trips'][$player],
      'rolls' => $row['rolls'][$player],
      'lifts' => $row['lifts'][$player],
      'fakes' => $row['fakes'][$player],
    );
    $where = array('edit_id' => $id, 'player' => $player);
    $this->db->update('ppe_edit_player', $data, $where);
  }
  
  // Get the player involved with this player id.
  function getPlayerByID($id)
  {
  	  return $this->db->select('player')->from('ppe_edit_player')
  	  	->where('id', $id)->get()->row()->player;
  }
}
