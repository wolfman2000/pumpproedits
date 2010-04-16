<?php

class Contact extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
  }
  
  function index()
  {
    $this->load->view('contact/main');
  }
  
  function mail()
  {
    if ($this->form_validation->run() === false)
    {
      $this->load->view('contact/error');
    }
    $this->load->library('email');
    $this->email->from('jafelds@gmail.com', 'Jason "Wolfman2000" Felds');
    $this->email->to('jafelds@gmail.com');
    $this->email->bcc('jafelds@gmail.com');
    $this->email->reply_to($this->input->post('email'), $this->input->post('name'));
    $this->email->subject('PPEdits Contact Form - ' . $this->input->post('subject'));
    $this->email->message($this->input->post('content'));
    $this->email->set_newline("\r\n");
    if ($this->email->send())
    {
      $this->load->view('contact/sent');
    }
    else
    {
      $this->load->view('contact/unsent');
    }
  }
}