<?php
class Ppe_edit_edit extends Model
{
	function __construct()
	{
	parent::Model();
	$this->load->model('ppe_edit_measure');
	}

	// Add an edit that is new to the database.
	function addEdit($row)
	{
		$date = date('Y-m-d H:i:s');
		$id = $this->db->select('MAX(id) AS lid')->get('ppe_edit_edit')
		->row()->lid + 1;

		$data = array(
			'id' => $id,
			'style_id' => $row['style_id'],
			'song_id' => $row['id'],
			'user_id' => $row['uid'],
			'title' => $row['title'],
			'diff' => $row['diff'],
			'created_at' => $date,
			'updated_at' => $date,
			'is_public' => (isset($row['public']) ? $row['public'] : 1),
		);

		$this->db->insert('ppe_edit_edit', $data);
		#$id = $this->db->insert_id();

		$players = array(0);
		if ($row['style'] === "pump-routine")
		{
			$players = array(0, 1);
		}
		foreach ($players as $player)
		{
			$pid = $this->db->select('MAX(id) AS pid')->get('ppe_edit_player')
				->row()->pid + 1;
			$data = array(
				'id' => $pid,
				'air' => $row['air'][$player],
				'stream' => $row['stream'][$player],
				'voltage' => $row['voltage'][$player],
				'freeze' => $row['freeze'][$player],
				'chaos' => $row['chaos'][$player],
				'steps' => $row['steps'][$player],
				'jumps' => $row['jumps'][$player],
				'holds' => $row['holds'][$player],
				'mines' => $row['mines'][$player],
				'trips' => $row['trips'][$player],
				'rolls' => $row['rolls'][$player],
				'lifts' => $row['lifts'][$player],
				'fakes' => $row['fakes'][$player],
				'player' => $player + 1,
				'edit_id' => $id,
			);
			$this->db->insert('ppe_edit_player', $data);
			$this->ppe_edit_measure->placeNotes($pid, $row['notes']);
		}
		return $id; // Return the edit ID.
	}

	// Update an edit that is already in the database.
	function updateEdit($id, $row)
	{
		$data = array(
			'title' => $row['title'],
			'diff' => $row['diff'],
			'updated_at' => date('Y-m-d H:i:s'),
			'is_public' => $row['public'],
		);
		$this->db->update('ppe_edit_edit', $data, "id = $id");
		$players = array(0);
		if ($row['style'] === "pump-routine")
		{
			$players = array(0, 1);
		}
		foreach ($players as $player)
		{
			$data = array(
				'air' => $row['air'][$player],
				'stream' => $row['stream'][$player],
				'voltage' => $row['voltage'][$player],
				'freeze' => $row['freeze'][$player],
				'chaos' => $row['chaos'][$player],
				'steps' => $row['steps'][$player],
				'jumps' => $row['jumps'][$player],
				'holds' => $row['holds'][$player],
				'mines' => $row['mines'][$player],
				'trips' => $row['trips'][$player],
				'rolls' => $row['rolls'][$player],
				'lifts' => $row['lifts'][$player],
				'fakes' => $row['fakes'][$player],
			);
			$where = array('edit_id' => $id, 'player' => $player + 1);

			$this->db->update('ppe_edit_player', $data, $where);
			$pid = $this->db->select('id')->where($where)
				->get('ppe_edit_player')->row()->id;

			// remove what's there, and reupload.
			$this->ppe_edit_measure->placeNotes($pid, $row['notes']);
		}
	}

	// Get a list of edits with same song/style/title, excluding itself.
	function checkDuplicates($sid, $uid, $style, $title, $eid = null)
	{
		$q = $this->db->select('id')
		->where('song_id', $sid)->where('user_id', $uid)
		->where('style', $style)->where('title', $title);
		if ($eid)
		{
			$q->where('id !=', $eid);
		}
		return $q->get('full_edit_stats')->num_rows();
	}

	// Get the list of edits by the user. Should problem ones be included?
	function getSVGEdits($uid)
	{
		return $this->db->select('id, style, title, diff, abbr, name')
			->where('user_id', $uid)
			->order_by('LOWER(name)')
			->order_by('title')
			->order_by('style')
			->get('edits_svg')->result_array();
	}

	// Confirm if the edit exists.
	public function checkExistance($eid)
	{
		return $this->db->select('id')->where('id', $eid)
		->get('ppe_edit_edit')->num_rows() > 0;
	}

	// Confirm if the edit exists and is not deleted.
	function checkExistsAndActive($eid)
	{
		return $this->db->select('id')->where('id', $eid)
		->where('deleted_at', null)
		->get('ppe_edit_edit')->num_rows() > 0;
	}

	// Get all of the user edits for possible charting.
	public function getNonProblemEdits()
	{
		return $this->db->select('id, uname, style, title, diff, sname')
		->where('is_problem', 0)
		->where('deleted_at', null)
		->order_by('LOWER(uname)')
		->order_by('LOWER(sname)')
		->order_by('title')
		->order_by('style')
		->get('edits_non_problem');
	}

	// Get all of the user edits that could be deleted.
	public function getEditsToDelete($uid)
	{
		return $this->db->select('id, style, title, diff, sname')
		->where('is_problem', 0)
		->where('deleted_at', null)
		->where('user_id', $uid)
		->order_by('LOWER(sname')
		->order_by('title')
		->order_by('style')
		->get('edits_to_delete');
	}

	// "Remove" the selected edits. Or maybe reactivate.
	function removeEdits($ids, $time = 1)
	{
		if ($time) { $time = date('Y-m-d H:i:s'); }
		else { $time = null; }
		$data['deleted_at'] = $time;
		if ($time) { $data['title'] = null; }
		$this->db->where_in('id', $ids)->update('ppe_edit_edit', $data);
	}

	// Determine via view if the edit being uploaded is new or old.
	function getIDByUpload($row)
	{
		$q = $this->db->select('id')->where('title', $row['title'])
		->where('style', substr($row['style'], 5))
		->where('user_id', $row['uid'])
		->get('edits_uploaded');
		return $q->num_rows() ? $q->row()->id : null;
	}

  // Common function that uses the full view.
	private function _getGoodEdits($order, $where = null, $limit = 10000)
	{
		$this->db->from('full_edit_stats')
			->where('is_problem', 0)
			->where('is_public', 1)
			->where('deleted_at', null);
		if (isset($where)):
			$this->db->where($where['main'], $where['main_id']);
		endif;
		foreach ($order as $o):
			$this->db->order_by($o['column'], $o['direction']);
		endforeach;
		$this->db->limit($limit);
		return $this->db->get();
	}

	// Get 5 edits for the entry page.
	public function getEditsEntry()
	{
		return $this->_getGoodEdits(array(array('column' => 'title', 'direction' => 'random')), null, 5);
	}

	// Get all edits of the chosen song.
	public function getEditsBySong($sid, $page = 1)
	{
		$order = array(array('column' => 'LOWER(uname)', 'direction' => 'asc'),
			array('column' => 'title', 'direction' => 'asc'));
		return $this->_getGoodEdits($order, array('main' => 'song_id', 'main_id' => $sid));
	}

	// Get all edits of the chosen user.
	public function getEditsByUser($uid, $page = 1)
	{
		$order = array(array('column' => 'LOWER(sname)', 'direction' => 'asc'),
			array('column' => 'title', 'direction' => 'asc'));
		return $this->_getGoodEdits($order, array('main' => 'user_id', 'main_id' => $uid));
	}

	// Common function to get the max number of edits possible.
	private function _getEditCount($params)
	{
		return $this->db->select('id')
		->where($params['where'], $params['cond'])
		->where('a.is_problem', 0)
		->where('a.is_public', 1)
		->where('a.deleted_at', null)
		->get('ppe_edit_edit a')->num_rows();
	}

	public function getUserEditCount($uid)
	{
		return $this->_getEditCount(array('where' => 'user_id', 'cond' => $uid));
	}

	public function getSongEditCount($sid)
	{
		return $this->_getEditCount(array('where' => 'song_id', 'cond' => $sid));
	}

	// Download the chosen edit using the database.
	public function downloadEdit($id, $pro1 = 0)
	{
		$str = "CALL buildEdit(%d, @file, %d);";
		$this->db->query(sprintf($str, $id, $pro1));
		return $this->db->query("SELECT @file AS wanted")->row()->wanted;
	}
	
	// Get the name of the author for this edit.
	public function getEditAuthor($eid)
	{
		return $this->db->select('user_id')
			->where('id', $eid)
			->get('edit_authors')->row()->user_id;
	}
	
	// Get the number of measures the song has.
	public function getMeasureCount($eid)
	{
		return $this->db->select('measures')
			->where('id', $eid)
			->get('measures_in_edits')->row()->measures;
	}
	
	// Get the stats needed to generate the chart without using files.
	public function getEditChartStats($eid)
	{
		return $this->db->where('id', $eid)
			->get('edit_chart_stats')->row_array();
	}
}
