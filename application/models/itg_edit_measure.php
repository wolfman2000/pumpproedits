<?php

class Itg_edit_measure extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	// Get all of the notes that this song uses.
	function getNotes($eid)
	{
		return $this->db->select('measure, beat, column, symbol')
			->where('id', $eid)
			->order_by('measure')
			->order_by('beat')
			->order_by('column')
			->get('edit_chart_notes');
	}
	
	function placeNotes($eid, $notes)
	{
		// ignore what's already there.
		$this->db->where('old_edit_id', $eid)->delete('itg_edit_measure');
		
		// now insert in bulk using modified simple syntax.
		$columns = '`old_edit_id`, `measure`, `beat`, `column`, `note_id`';
		
		$length = count($notes);
		$rows = array();
		for ($i = 0; $i < $length; $i++)
		{
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
			default:
				{
					echo "Invalid note. You got: ";
					print_r($notes[$i]['note']); exit;
				}
			}
			$rows[$i] = sprintf("%d, %d, %d, %d, %d", $eid, 
				$notes[$i]['measure'], $notes[$i]['beat'],
				$notes[$i]['column'], $nid);
		}
		
		$values = "(" . implode( '),(', $rows ) . ")";
		$sql = "INSERT INTO itg_edit_measure ( $columns ) VALUES $values ;";
		$this->db->simple_query($sql);
		$ret = $this->db->_error_message();
		if (strlen($ret))
		{
			return $ret . "<br>";
		}
		else
		{
			return "";
		}
	}
}
