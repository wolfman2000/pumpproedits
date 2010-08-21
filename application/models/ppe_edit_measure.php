<?php
class Ppe_edit_measure extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function placeNotes($pid, $notes)
	{
		// ignore what's already there.
		$this->db->where('player_id', $pid)->delete('ppe_edit_measure');
		
		// now insert in bulk using modified simple syntax.
		$columns = '`player_id`, `measure`, `beat`, `column`, `note_id`';
		
		$length = count($notes);
		$rows = array();
		for ($i = 0; $i < $length; $i++)
		{
			switch($notes[$i])
			{
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
					$nid = $notes[$i]['note'];
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
