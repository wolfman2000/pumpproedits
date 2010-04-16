<?php

class Chart extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->library('form_validation');
  }
  
  function index()
  {
    redirect('chart/edits');
  }
  
  function edits()
  {
    $this->load->view('chart/edits');
  }
  
  function editProcess()
  {
  
  }
  
  function songs()
  {
  
  }
  
  function songProcess()
  {
  
  }
  
  function quick()
  {
    $id = $this->uri->segment(3, FALSE);
    $kind = $this->uri->segment(4, FALSE);
    if (!(is_numeric($id) and ($kind === "classic" or $kind === "rhythm")))
    {
      # Return error here: parameters must match.
    }
    $id = sprintf("%06d", $id);
    $name = sprintf("edit_%s.edit.gz", $id);
    $path = sprintf("%s/data/user_edits/%s", APPPATH, $name);
    
    if (!file_exists($path))
    {
      # Return error: file must exist.
    }
    // Validate the file and print the chart here.
    $this->load->library('EditParser');
    $notedata = $this->editparser->get_stats(gzopen($path, "r"),
      array('notes' => 1, 'strict_edit' => 0));
    $p = array('cols' => $notedata['cols'], 'kind' => $kind);
    $this->load->library('EditCharter', $p);
    header("Content-Type: application/xhtml+xml");
    $xml = $this->editcharter->genChart($notedata);
    
    echo $xml->saveXML();
  }
}