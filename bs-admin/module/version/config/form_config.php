<?php
/*
 * B-studio : Contents Management System
 * Copyright (c) BigBeat Inc. all rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$form_config = array(
	array('class' => 'B_Hidden', 'name' => 'mode'),

	array(
		// Required message
		array(
			'class'			=> 'B_Guidance',
			'start_html'	=> '<p>',
			'end_html'		=> '</p>',
			array(
				'class'			=> 'B_Guidance',
				'start_html'	=> '<span class="require">',
				'end_html'		=> '</span>',
				'value'			=> _('*'),
			),
			array(
				'class'			=> 'B_Guidance',
				'value'			=> _(' is required field'),
			),
		),

		// Table
		'start_html'	=> '<table class="form" border="0" cellspacing="0" cellpadding="0"><tbody>',
		'end_html'		=> '</tbody></table>',

		// ID
		array(
			'name'			=> 'version_id_row',
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th class="top">',
				'end_html'		=> '</th>',
				array(
					'value'			=> _('ID'),
				),
				array(
					'class'			=> 'B_Guidance',
					'value'			=> '<span class="require">' . _('*') . '</span>',
				),				
			),
			array(
				'name'			=> 'version_id',
				'class'			=> 'B_Text',
				'start_html'	=> '<td class="top">',
				'end_html'		=> '</td>',
			),
		),

		// Publish date and time
		array(
			'error_group'	=> true,
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'			=> '<th>',
				'end_html'				=> '</th>',
				'invalid_start_html'	=> '<th class="error">',
				array(
					'value'				=> _('Publish date and time'),
				),
				array(
					'class'				=> 'B_Guidance',
					'value'				=> '<span class="require">' . _('*') . '</span>',
				),				
			),
			array(
				'start_html'    => '<td>',
				'end_html'	    => '</td>',
				array(
					'name'				=> 'publication_datetime_t',
					'class'				=> 'B_InputText',
					'special_html'		=> 'class="textbox ime_off"',
					'format'			=> 'Y/m/d H:i',
					'data_set'			=> 'datetime_error_message',
					'validate'			=>
					array(
						array(
							'type' 			=> 'text_datetime',
							'error_message'	=> _('Please enter correct date and time'),
						),
						array(
							'type' 			=> 'required',
							'delimiter'		=> '/',
							'error_message'	=> _('Please enter publish date and time'),
						),
					),
				),
				array(
					'class'		=> 'B_Guidance',
					'value'		=> _('Format: YYYY/MM/DD hh:mm'),
					array(
						'class'			=> 'B_Guidance',
						'start_html'	=> '<span class="example">',
						'end_html'		=> '</span>',
						'value'			=> _('ex) 2020/01/01 12:00'),
					),
				),
				array(
					'name'				=> 'error_message',
					'class'				=> 'B_ErrMsg',
					'start_html'		=> '<span class="error-message">',
					'end_html'			=> '</span>',
				),
			),
		),

		// Version（Name）
		array(
			'error_group'	=> true,
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'			=> '<th>',
				'end_html'				=> '</th>',
				'invalid_start_html'	=> '<th class="error">',
				array(
					'value'				=> _('Version name'),
				),
				array(
					'class'				=> 'B_Guidance',
					'value'				=> '<span class="require">' . _('*') . '</span>',
				),				
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'				=> 'version',
					'class'				=> 'B_InputText',
					'special_html'		=> 'class="textbox ime_on" size="80" maxlength="100"',
					'validate'			=>
					array(
						array(
							'type' 			=> 'required',
							'error_message'	=> _('Please enter version name'),
						),
					),
				),
				array(
					'name'				=> 'error_message',
					'class'				=> 'B_ErrMsg',
					'start_html'		=> '<span class="error-message">',
					'end_html'			=> '</span>',
				),
			),
		),

		// Notes
		array(
			'error_group'	=> true,
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				'value'			=> _('Notes'),
			),
			array(
				'name'			=> 'notes',
				'class'			=> 'B_TextArea',
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				'special_html'	=> 'class="textarea" rows="5"',
			),
	    ),
	),
);

//control
$input_control_config = array(
	'start_html'	=> '<ul class="control">',
	'end_html'		=> '</ul>',
	array(
		'name'			=> 'back',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="left-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'list\', \'back\', \'\')">',
			'end_html'		=> '</span>',
			'value'			=> _('Back'),
		),
	),
	array(
		'name'			=> 'confirm',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="right-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'form\', \'confirm\', \'\', true)">',
			'end_html'		=> '</span>',
			'value'			=> _('Confirm'),
		),
	),
);

//confirm control
$confirm_control_config = array(
	'start_html'	=> '<ul class="control">',
	'end_html'		=> '</ul>',
	array(
		'name'			=> 'back',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="left-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'form\', \'back\', \'\')">',
			'end_html'		=> '</span>',
			'value'			=> _('Back'),
		),
	),
	array(
		'name'			=> 'register',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="right-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'form\', \'register\', \'\')">',
			'end_html'		=> '</span>',
			'value'			=> _('Save'),
		),
	),
);

//delete control
$delete_control_config = array(
	'start_html'	=> '<ul class="control">',
	'end_html'		=> '</ul>',
	array(
		'name'			=> 'back',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="left-button" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'list\', \'back\', \'\')">',
			'end_html'		=> '</span>',
			'value'			=> _('Back'),
		),
	),
	array(
		'name'			=> 'register',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="right-button" onclick="bframe.confirmSubmit(\'' . _('All the contents you made on this version will be completely deleted.\nThis operation can not be undone.\n\nAre your sure to delete?') . '\', \'F1\', \'' . $this->module . '\', \'form\', \'delete\', \'\');">',
			'end_html'		=> '</span>',
			'value'			=> _('Delete'),
		),
	),
);

//control
$result_control_config = array(
	'start_html'	=> '<ul class="control">',
	'end_html'		=> '</ul>',
	array(
		'name'			=> 'backToList',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<span class="left-button" style="width:150px" onclick="bframe.submit(\'F1\', \'' . $this->module . '\', \'list\', \'back\', \'\')">',
			'end_html'		=> '</span>',
			'value'			=> _('Back to list'),
		),
	),
);

//Result
$result_config = array(
	array(
		'start_html'	=> '<form name="F1" method="post" action="index.php">',
		'end_html'		=> '</form>',
		array(
			'start_html'	=> '<p>',
			'end_html'		=> '</p>',
			array(
				array(
					'start_html'	=> '<span class="version">',
					'end_html'		=> '</span>',
					array(
						'value'			=> _('Version: '),
					),
					array(
						'name'			=> 'version',
						'class'			=> 'B_Text',
						'start_html'	=> '<span class="bold">',
						'end_html'		=> '</span>',
					),
				),
				array(
					'start_html'	=> '<span class="date-time">',
					'end_html'		=> '</span>',
					array(
						'value'			=> _('Publish date and time: '),
					),
					array(
						'name'			=> 'publication_datetime_t',
						'class'			=> 'B_Text',
						'start_html'	=> '<span class="bold">',
						'end_html'		=> '</span>',
					),
				),
				array(
					'name'		=> 'action_message',
				),
			),
		),
	),
);
