<?php
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
}