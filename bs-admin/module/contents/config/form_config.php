<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$form_config = array(
	array('class' => 'B_Hidden', 'name' => 'baseHref', 'value' => B_SITE_BASE),
	array('class' => 'B_Hidden', 'name' => 'visual_editor_language', 'value' => $_SESSION['language']),
	array('class' => 'B_Hidden', 'name' => 'node_id'),
	array('class' => 'B_Hidden', 'name' => 'contents_id'),
	array('class' => 'B_Hidden', 'name' => 'update_datetime'),
	array(
		'start_html'	=> '<div class="editor_container bframe_adjustwindow" data-param="margin:8" >',
		'end_html'		=> '</div>',
		array(
			'start_html'	=> '<div id="html" class="text_editor bframe_adjustparent" data-param="margin:21">',
			'end_html'		=> '</div>',
			array(
				'name'			=> 'html1',
				'class'			=> 'B_TextArea',
				'attr'			=> 'class="textarea bframe_adjustparent bframe_texteditor" data-param="margin:32" style="width:100%"',
				'no_trim'		=> true,
			),
			array(
				'class'			=> 'B_Link',
				'link'			=> 'index.php',
				'value'			=> __('Widgets'),
				'attr'			=> 'class="open_widgetmanager" title="' . __('Widgets') . '" style="display:none"',
				'fixedparam'	=>
				array(
					'terminal_id'	=> TERMINAL_ID,
					'module'		=> 'widget',
					'page'			=> 'select_tree',
					'target_id'		=> 'html1',
				),
			),
		),
		array(
			'start_html'	=> '<div id="visual" class="visual_editor bframe_adjustparent" data-param="margin:21" style="display:none">',
			'end_html'		=> '</div>',
			array(
				'start_html'	=> '<iframe id="inline_frame" name="inline_frame" class="bframe_adjustparent">',
				'end_html'		=> '</iframe>',
			),
		),
		array(
			'start_html'	=> '<div id="css" class="text_editor bframe_adjustparent" data-param="margin:21" style="display:none">',
			'end_html'		=> '</div>',
			array(
				'id'			=> 'css_editor',
				'name'			=> 'css',
				'class'			=> 'B_TextArea',
				'attr'			=> 'class="textarea bframe_adjustparent bframe_texteditor" data-param="margin:32" data-syntax="css" style="width:100%"',
				'no_trim'		=> true,
			),
		),
		array(
			'start_html'	=> '<div id="php" class="text_editor bframe_adjustparent" data-param="margin:21" style="display:none">',
			'end_html'		=> '</div>',
			array(
				'id'			=> 'php_editor',
				'name'			=> 'php',
				'class'			=> 'B_TextArea',
				'attr'			=> 'class="textarea bframe_adjustparent bframe_texteditor" data-param="margin:32" data-syntax="php" style="width:100%"',
				'no_trim'		=> true,
			),
			array(
				'id'			=> 'open_widgetmanager',
				'class'			=> 'B_Link',
				'link'			=> 'index.php',
				'value'			=> __('Widgets'),
				'attr'			=> 'title="' . __('Widgets') . '" style="display:none"',
				'fixedparam'	=>
				array(
					'terminal_id'	=> TERMINAL_ID,
					'module'		=> 'widget', 
					'page'			=> 'select_tree',
					'target_id'		=> 'php',
				),
			),
		),
		array(
			'name'			=> 'settings_form',
			'start_html'	=> '<div id="settings" class="bframe_adjustparent bframe_scroll" data-param="margin:25" style="display:none">',
			'end_html'		=> '</div>',
		),
		array(
			'start_html'	=> '<div id="preview" class="bframe_adjustparent" data-param="margin:22" style="display:none;position:relative;">',
			'end_html'		=> '</div>',
			array(
				'start_html'	=> '<div id="preview_frame_container" class="bframe_adjustparent" style="margin: 0 auto;">',
				'end_html'		=> '</div>',
				array(
					'start_html'	=> '<iframe id="preview_frame" name="preview_frame" class="bframe_adjustparent">',
					'end_html'		=> '</iframe>',
				),
			),
			array(
				'start_html'	=> '<div class="bframe_emulator">',
				'end_html'		=> '</div>',
			),
		),
	),
);

//tab control
$tab_control_config = array(
	'start_html'	=> '<ul class="tabcontrol">',
	'end_html'		=> '</ul>',
	array(
		'name'			=> 'html_editor_index',
		'class'			=> 'B_Link',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		'link'			=> 'html',
		'attr'			=> 'class="bframe_tab" onclick="bframe.inline.blur()"',
		'value'			=> __('HTML'),
	),
	array(
		'name'			=> 'visual_editor_index',
		'class'			=> 'B_Link',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		'link'			=> 'visual',
		'attr'			=> 'class="bframe_tab" onclick="bframe.inline.submit(\'F1\', \'' . B_SITE_BASE . 'index.php' . '\', \'inline\', \'inline_frame\'); return false;"',
		'value'			=> __('Visual'),
	),
	array(
		'name'			=> 'css_editor_index',
		'class'			=> 'B_Link',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		'link'			=> 'css',
		'value'			=> __('CSS'),
		'attr'			=> 'class="bframe_tab"',
	),
	array(
		'name'			=> 'php_editor_index',
		'class'			=> 'B_Link',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		'link'			=> 'php',
		'value'			=> __('PHP'),
		'attr'			=> 'class="bframe_tab"',
	),
	array(
		'name'			=> 'settings_index',
		'class'			=> 'B_Link',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		'link'			=> 'settings',
		'value'			=> __('Settings'),
		'attr'			=> 'class="bframe_tab"',
	),
	array(
		'name'			=> 'preview_index',
		'class'			=> 'B_Link',
		'start_html'	=> '<li>',
		'end_html'		=> '</li>',
		'link'			=> 'preview',
		'value'			=> __('Preview'),
		'attr'			=> 'class="bframe_tab" onclick="bframe.preview.submit(\'F1\', \'' . B_SITE_BASE . 'index.php' . '\', \'preview\', \'preview_frame\'); return false;"',
	),
	array(
		'name'			=> 'register_button',
		'start_html'	=> '<li class="register">',
		'end_html'		=> '</li>',
		array(
			'start_html'	=> '<div class="input-container">',
			'end_html'		=> '</div>',
			array(
				'name'			=> 'register',
				'start_html'	=> '<span id="register" class="register-button" onclick="bframe.ajaxSubmit.submit(\'F1\', \'' . $this->module . '\', \'form\', \'register\', \'confirm\', true)">',
				'end_html'		=> '</span>',
				'value'			=> '<img src="images/common/save.png" alt="Save" />' . __('Save'),
			),
		),
		array(
			'start_html'	=> '<div class="message-container">',
			'end_html'		=> '</div>',
			array(
				'start_html'	=> '<span id="message">',
				'end_html'		=> '</span>',
			),
		),
	),
);

$settings_form_config = array(
	array(
		// Table
		'start_html'	=> '<table class="form"><tbody>',
		'end_html'		=> '</tbody></table>',

		// Title
		array(
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'		=> '<th>',
				'end_html'			=> '</th>',
				array(
					'value'				=> __('Title'),
					'no_linefeed'		=> true,
				),
			),
			array(
				'start_html'		=> '<td>',
				'end_html'			=> '</td>',
				array(
					'name'				=> 'title',
					'class'				=> 'B_InputText',
					'attr'				=> 'class="title bframe_textarea"',
					'no_trim'			=> true,
				),
			),
		),
		// Template
		array(
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				'value'			=> __('Template'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'template',
					'class'			=> 'B_InputText',
					'attr'			=> 'class="template ime-off" readonly="readonly"',
				),
				array(
					'class'			=> 'B_Hidden',
					'name'			=> 'template_id',
				),
				array(
					'name'			=> 'open_template',
					'class'			=> 'B_Link',
					'link'			=> 'index.php',
					'attr'			=> 'title="' . __('Template') . '" class="settings-button" onclick="top.bframe.modalWindow.activate(this, window, \'template_id\'); return false;" data-param="width:350,height:400"',
					'fixedparam'	=>
					array(
						'terminal_id'	=> TERMINAL_ID,
						'module'		=> 'template', 
						'page'			=> 'select_tree',
					),
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('Template') . '" src="images/common/gear_gray.png" />',
				),
				array(
					'class'			=> 'B_Link',
					'link'			=> '#',
					'attr'			=> 'title="' . __('Clear') . '" class="clear-button" onclick="bstudio.clearText(\'template\', \'template_id\'); return false;" ',
					'specialchars'	=> 'none',
					'value'			=> '<img alt="' . __('Clear') . '" src="images/common/clear_gray.png" />',
				),
			),
		),
		// Breadcrumbs
		array(
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				array(
					'value'			=> __('Breadcrumbs'),
					'no_linefeed'	=> true,
				),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'breadcrumbs',
					'class'			=> 'B_InputText',
					'attr'			=> 'class="breadcrumbs bframe_textarea" ',
					'no_trim'		=> true,
				),
			),
		),
		// Keywords
		array(
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				'value'			=> __('Keywords'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'keywords',
					'class'			=> 'B_TextArea',
					'attr'			=> 'class="keywords bframe_textarea"',
				),
			),
		),
		// Description
		array(
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				'value'			=> __('Description'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'description',
					'class'			=> 'B_TextArea',
					'attr'			=> 'class="description bframe_textarea"',
				),
			),
		),
		// External CSS
		array(
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				'value'			=> __('External css'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'external_css',
					'class'			=> 'B_TextArea',
					'attr'			=> 'class="external_css bframe_textarea"',
				),
			),
		),
		// External javascript
		array(
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				'value'			=> __('External javascript'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'name'			=> 'external_js',
					'class'			=> 'B_TextArea',
					'attr'			=> 'class="external_js bframe_textarea"',
				),
			),
		),
		// Header elements
		array(
			'start_html'	=> '<tr>',
			'end_html'		=> '</tr>',
			array(
				'start_html'	=> '<th>',
				'end_html'		=> '</th>',
				'value'			=> __('Header elements'),
			),
			array(
				'start_html'	=> '<td>',
				'end_html'		=> '</td>',
				array(
					'class'			=> 'B_TextArea',
					'name'			=> 'header_element',
					'attr'			=> 'class="header_element bframe_textarea"',
				),
			),
		),
	),
);
