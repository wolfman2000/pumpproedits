<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_edit_measure extends Model
{
	function __construct()
	{
		parent::Model();
		$this->load->model('ppe_edit_player');
	}
	
	// Get all of the notes that this song uses.
	function getNotes($eid)
	{
		return $this->db->select('player, measure, beat, column, symbol')
			->where('id', $eid)
			->order_by('player')
			->order_by('measure')
			->order_by('beat')
			->order_by('column')
			->get('edit_chart_notes');
	}
	
	// Get all of the notes for the edit creator.
	function getCreatorNotes($eid)
	{
		return $this->db->select('player, measure, beat, column, note')
			->where('id', $eid)
			->order_by('measure')
			->order_by('beat')
			->order_by('column')
			->get('edit_chart_notes');
	}
	
	function placeNotes($pid, $notes)
	{
		// ignore what's already there.
		$this->db->where('player_id', $pid)->delete('ppe_edit_measure');
		
		// now insert in bulk using modified simple syntax.
		$columns = '`player_id`, `measure`, `beat`, `column`, `note_id`';
		
		$length = count($notes);
		$rows = array();
		$player = $this->ppe_edit_player->getPlayerByID($pid);
		for ($i = 0; $i < $length; $i++)
		{
			if ($notes[$i]['player'] != $player) { continue; } # Routine hack.
			
			switch($notes[$i]['note'])
			{
			case "1":
				{
					$nid = 1;
					break;
				}
			case "2":
				{
					$nid = 2;
					break;
				}
			case "3":
				{
					$nid = 3;
					break;
				}
			case "4":
				{
					$nid = 4;
					break;
				}
			case "M": 
				{
					$nid = 5;
					break;
				}
			case "L":
				{
					$nid = 6;
					break;
				}
			case "F":
				{
					$nid = 7;
					break;
				}
			default:
				{
					echo "Invalid note. You got: ";
					print_r($notes[$i]['note']); exit;
				}
			}
			$rows[$i] = sprintf("%d, %d, %d, %d, %d", $pid, 
				$notes[$i]['measure'], $notes[$i]['beat'],
				$notes[$i]['column'] - 1, $nid);
		}
		
		$values = "(" . implode( '),(', $rows ) . ")";
		$sql = "INSERT INTO ppe_edit_measure ( $columns ) VALUES $values ;";
		return $this->db->simple_query($sql);
	}
}
