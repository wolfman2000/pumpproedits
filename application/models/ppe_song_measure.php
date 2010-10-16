<?php

class Ppe_song_measure extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	// Get all of the notes that this song uses.
	function getNotes($sid, $diff)
	{
		return $this->db->select('measure, beat, column, symbol')
			->where('id', $sid)
			->where('diff', $diff)
			->order_by('measure')
			->order_by('beat')
			->order_by('column')
			->get('song_chart_notes');
	}
	
	function getLongDifficulty($diff)
	{
		return $this->db->select('diff')->where('abbr', $diff)
			->get('ppe_game_difficulty')->row()->diff;
	}
	
	// Put in the basic radar stuff. Return the modified player ID.
	function placeStats($sdid, $p, $s, $j, $h, $m, $t, $r, $l, $f)
	{
		$data = array
		(
			'song_diff_id' => $sdid,
			'player' => $p,
			'steps' => $s,
			'jumps' => $j,
			'holds' => $h,
			'mines' => $m,
			'trips' => $t,
			'rolls' => $r,
			'lifts' => $l,
			'fakes' => $f,
		);
		$this->db->insert('ppe_song_stat', $data);
		return $this->db->select('id')->where('song_diff_id', $sdid)
			->where('player', $p)->get('ppe_song_stat')->row()->id;
	}
	
	function placeNotes($pid, $player, $notes, $beats)
	{
		// ignore what's already there.
		$this->db->where('player_id', $pid)->delete('ppe_song_measure');
		
		// now insert in bulk using modified simple syntax.
		$columns = 'player_id, measure, beat, `column`, note_id';
		
		$length = count($notes);
		$rows = array();
		for ($i = 0; $i < $length; $i++)
		{
			if ($notes[$i]['player'] != $player) continue;
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
			$measure = $notes[$i]['measure'];
			$rows[$i] = sprintf("%d, %d, %d, %d, %d", $pid,
				$measure, $notes[$i]['row'] * 192 / $beats[$player][$measure],
				$notes[$i]['column'], $nid);
		}
		
		$values = "(" . implode( '),(', $rows ) . ")";
		$sql = "INSERT INTO ppe_song_measure ( $columns ) VALUES $values ;";
		
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
