<?php
// This file handles all of the validation rules.

$config = array(
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
			'rules' => 'required|is_natural_no_zero|callback__edit_exists'
		),
		array(
			'field' => 'kind',
			'label' => 'Noteskin',
			'rules' => 'required|callback__noteskin_exists'
		),
		array(
			'field' => 'red4',
			'label' => '4th Note Color',
			'rules' => 'required|is_natural|callback__red_exists'
		),
		array(
			'field' => 'speed',
			'label' => 'Speed Mod',
			'rules' => 'required|is_natural_no_zero|callback__speed_valid'
		),
		array(
			'field' => 'mpcol',
			'label' => 'Measures per Column',
			'rules' => 'required|is_natural_no_zero|callback__mpc_valid'
		),
		array(
			'field' => 'scale',
			'label' => 'Scale',
			'rules' => 'required|numeric|callback__scale_valid'
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
			'rules' => 'required|callback__noteskin_exists'
		),
		array(
			'field' => 'red4',
			'label' => '4th Note Color',
			'rules' => 'required|is_natural|callback__red_exists'
		),
		array(
			'field' => 'speed',
			'label' => 'Speed Mod',
			'rules' => 'required|is_natural_no_zero|callback__speed_valid'
		),
		array(
			'field' => 'mpcol',
			'label' => 'Measures per Column',
			'rules' => 'required|is_natural_no_zero|callback__mpc_valid'
		),
		array(
			'field' => 'scale',
			'label' => 'Scale',
			'rules' => 'required|numeric|callback__scale_valid'
		),
	)
);
