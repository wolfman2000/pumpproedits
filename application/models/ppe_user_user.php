<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
class Ppe_user_user extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Add the new user to the database.
  function addUser($name, $email, $pass)
  {
    // use transactions to ensure validity.
    $this->db->trans_start();

    $id = $this->db->select('MAX(id) AS lid')->get('ppe_user_user')
      ->row()->lid + 1;
    $data = array('name' => $name, 'email' => $email, 'id' => $id);
    
    $this->db->insert('ppe_user_user', $data);
    #$id = $this->db->insert_id();
    
    $this->load->helper('salter');
    $salt = genSalt();
    $md5 = hash("md5", $pass . $salt);
    
    $oid = $this->db->select('MAX(id) AS lid')->get('ppe_user_condiment')
      ->row()->lid + 1;
    $data = array('oregano' => $md5, 'salt' => $salt, 'id' => $oid,
      'pepper' => hash("sha256", $pass . $salt), 'user_id' => $id);
    $this->db->insert('ppe_user_condiment', $data);
    
    $this->db->insert('ppe_user_power', array('user_id' => $id, 'role_id' => APP_USER_ROLE));
    $this->db->trans_complete(); // transaction complete.
    return $md5;
  }
  // confirm the user (or unconfirm) as required
  function confirmUser($id, $confirm = 1)
  {
    $this->db->update('ppe_user_user', array('is_confirmed' => $confirm), "id = $id");
  }
  
  // get the name of the user by its ID.
  public function getUserByID($id)
  {
    return $this->db->select('name')->where('id', $id)
      ->get('ppe_user_user')->row()->name;
  }
  
  // Check if the user is confirmed based on ID.
  function getConfirmedByID($id)
  {
    return $this->db->select('is_confirmed')->where('id', $id)
      ->get('ppe_user_user')->row()->is_confirmed;
  }
  
  // get the id of the user via email.
  function getIDByEmail($email)
  {
    $q = $this->db->select('id')->where('LOWER(email)', strtolower($email))
      ->get('ppe_user_user');
    return $q->num_rows() ? $q->row()->id : null;
  }
  
  // get the ID of the user via username.
  function getIDByUser($name)
  {
    $q = $this->db->select('id')->where('LOWER(name)', strtolower($name))
      ->get('ppe_user_user');
    return $q->num_rows() ? $q->row()->id : null;
  }
  
  // get the list of users with edits.
  public function getUsersWithEdits()
  {
    return $this->db->select('a.id, a.name core, COUNT(b.id) num_edits')
      ->from('ppe_user_user a')
      ->join('ppe_edit_edit b', 'a.id = b.user_id')
      ->where('b.is_problem', 0)
      ->where('b.deleted_at', null)
      ->where('b.is_public', 1)
      ->where_not_in('a.id', array(2, 95, 97, 113, 120, 124))
      ->group_by(array('a.id', 'a.name'))
      ->order_by('LOWER(a.name)')
      ->get();
  }
  
  // get the name of the author of the edit.
  public function getUserByEditID($eid)
  {
    return $this->db->select('u.name')
      ->join('ppe_edit_edit e', 'u.id = e.user_id')
      ->where('e.id', $eid)->get('ppe_user_user u')
      ->row()->name;
  }
  
  // get the intended name based off of the lowercase name.
  public function getCasedName($name)
  {
    return $this->db->select('name')
      ->where('LOWER(name)', strtolower($name))
      ->get('ppe_user_user')->row()->name;
  }
  
	// get the list of other users that have edits.
	function getOtherUsers($ids)
	{
		return $this->db
			->where('min_role >=', 3)
			->where_not_in('id', $ids)
			->order_by('LOWER(name)')
			->get('users_with_edits')->result_array();
	}
}
