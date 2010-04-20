<?php
class Ppe_user_user extends Model
{
  function __construct()
  {
    parent::Model();
  }
  
  // Add the new user to the database.
  function addUser($name, $email, $pass)
  {
    $data = array('name' => $name, 'lc_name' => strtolower($name),
      'email' => $email, 'lc_email' => strtolower($email),
    );
    // use transactions to ensure validity.
    $this->db->trans_start();
    
    $this->db->insert('ppe_user_user', $data);
    $id = $this->db->insert_id();
    
    $this->load->helper('salter');
    $salt = genSalt();
    $md5 = hash("md5", $pass . $salt);
    
    $data = array('oregano' => $md5, 'salt' => $salt,
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
    $q = $this->db->select('id')->where('lc_email', strtolower($email))
      ->get('ppe_user_user');
    return $q->num_rows() ? $q->row()->id : null;
  }
  
  // get the ID of the user via username.
  function getIDByUser($name)
  {
    $q = $this->db->select('id')->where('lc_name', strtolower($name))
      ->get('ppe_user_user');
    return $q->num_rows() ? $q->row()->id : null;
  }
  
  // get the list of users with edits.
  public function getUsersWithEdits()
  {
    return $this->db->select('a.id, a.name core, COUNT(b.id) num_edits')
      ->from('ppe_user_user a')
      ->join('ppe_edit_edit b', 'a.id = b.user_id')
      ->where('a.num_edits >', 0)
      ->where('b.is_problem', 0)
      ->where('b.deleted_at', null)
      ->where_not_in('a.id', array(2, 95))
      ->group_by(array('a.id', 'a.name'))
      ->order_by('a.lc_name')
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
      ->where('lc_name', strtolower($name))
      ->get('ppe_user_user')->row()->name;
  }
}