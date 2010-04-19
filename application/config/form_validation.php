<?php
// This file handles all of the validation rules.

$config = array(
	'reset/check' => array(
		array(
			'field' => 'confirm',
			'label' => 'Confirmation Code',
			'rules' => 'required'
		),
		array(
			'field' => 'password',
			'label' => 'Password',
			'rules' => 'required'
		),
		array(
			'field' => 'passdual',
			'label' => 'Confirm Password',
			'rules' => 'required|matches[password]'
		),
	),
	'help/check' => array(
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'valid_email',
		),
		array(
			'field' => 'choice',
			'label' => 'Choice',
			'rules' => 'callback__valid_choice',
		),
	),
	'confirm/check' => array(
		array(
			'field' => 'confirm',
			'label' => 'Confirmation Code',
			'rules' => 'required',
		),
		array(
			'field' => 'password',
			'label' => 'Password',
			'rules' => 'required',
		),
	),
	'register/check' => array(
		array(
			'field' => 'username',
			'label' => 'Username',
			'rules' => 'min_length[4]|max_length[12]',
		),
		array(
			'field' => 'password',
			'label' => 'Password',
			'rules' => 'min_length[5]',
		),
		array(
			'field' => 'passdual',
			'label' => 'Confirm Password',
			'rules' => 'min_length[5]|matches[password]',
		),
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'valid_email',
		),
	),
	'login/check' => array(
		array(
			'field' => 'username',
			'label' => 'Username',
			'rules' => 'required',
		),
		array(
			'field' => 'password',
			'label' => 'Password',
			'rules' => 'required',
		),
	),
	'contact/mail' => array(
		array(
			'field' => 'name',
			'label' => 'Name',
			'rules' => 'required'
		),
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'valid_email'
		),
		array(
			'field' => 'subject',
			'label' => 'Subject',
			'rules' => 'required|min_length[3]'
		),
		array(
			'field' => 'content',
			'label' => 'Content',
			'rules' => 'required|min_length[20]'
		),
	),
	'chart/editProcess' => array(
		array(
			'field' => 'edits',
			'label' => 'Edit',
			'rules' => 'callback__edit_exists'
		),
		array(
			'field' => 'kind',
			'label' => 'Noteskin',
			'rules' => 'callback__noteskin_exists'
		),
		array(
			'field' => 'red4',
			'label' => '4th Note Color',
			'rules' => 'callback__red_exists'
		),
		array(
			'field' => 'speed',
			'label' => 'Speed Mod',
			'rules' => 'callback__speed_valid'
		),
		array(
			'field' => 'mpcol',
			'label' => 'Measures per Column',
			'rules' => 'callback__mpc_valid'
		),
		array(
			'field' => 'scale',
			'label' => 'Scale',
			'rules' => 'callback__scale_valid'
		),
	),
	'chart/songProcess' => array(
		array(
			'field' => 'songs',
			'label' => 'Edit',
			'rules' => 'is_natural_no_zero'
		),
		array(
			'field' => 'diff',
			'label' => 'Difficulty',
			'rules' => 'callback__diff_exists'
		),
		array(
			'field' => 'kind',
			'label' => 'Noteskin',
			'rules' => 'callback__noteskin_exists'
		),
		array(
			'field' => 'red4',
			'label' => '4th Note Color',
			'rules' => 'callback__red_exists'
		),
		array(
			'field' => 'speed',
			'label' => 'Speed Mod',
			'rules' => 'callback__speed_valid'
		),
		array(
			'field' => 'mpcol',
			'label' => 'Measures per Column',
			'rules' => 'callback__mpc_valid'
		),
		array(
			'field' => 'scale',
			'label' => 'Scale',
			'rules' => 'callback__scale_valid'
		),
	)
);
