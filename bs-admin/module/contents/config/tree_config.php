<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
$tree_config = array(
	'id'			=> 'tree',
	'start_html'	=> '<div id="tree" class="bframe_tree">',
	'end_html'		=> '</div>',
	'script'		=>
	array(
		'bframe_tree'	=>
		array(
			'module'		=> $this->module,
			'file'			=> 'tree',
			'editable'		=> 'true',
			'sort'			=> 'manual',
			'root_name'		=> 'root',
			'root_path'		=> B_CURRENT_ROOT,
			'trash_name'	=> 'trash box',
			'method'		=>
			array(
				'getNodeList'	=> 'getNodeList',
				'pasteNode'		=> 'pasteNode',
				'pasteAriasNode'=> 'pasteNode',
				'deleteNode'	=> 'deleteNode',
				'truncateNode'	=> 'truncateNode',
				'closeNode'		=> 'closeNode',
				'createNode'	=> 'createNode',
				'updateDispSeq'	=> 'updateDispSeq',
				'saveName'		=> 'saveName',
				'preview'		=> 'preview',
				'property'		=> 'property',
			),
			'relation'	=>
			array(
				'selectNode'	=>
				array(
					'url'		=> DISPATCH_URL . '&module=' . $this->module . '&page=form&method=select',
					'frame'		=> 'contents_form',
				),
				'truncateNode'	=>
				array(
					'url'		=> DISPATCH_URL . '&module=' . $this->module . '&page=form&method=truncate',
					'frame'		=> 'contents_form',
				),
				'preview'	=>
				array(
					'url'		=> DISPATCH_URL . '&module=' . $this->module . '&page=tree&method=preview',
				),
				'open_property'	=>
				array(
					'url'		=> DISPATCH_URL . '&module=' . $this->module . '&page=property&method=select',
					'params'	=> 'width:360,height:200',
					'title'		=> __('Properties'),
					'func'		=> 'reloadTree',
				),
			),
			'icon'		=>
			array(
				'plus'			=> array('src' => './images/folders/plus.gif'),
				'minus'			=> array('src' => './images/folders/minus.gif'),
				'blank'			=> array('src' => './images/folders/blank.gif'),
				'root'			=> array('src' => './images/folders/contents_root.png'),
				'trash'			=> array('src' => './images/folders/trash.png'),
				'forbidden'		=> array('src' => './images/folders/forbidden.png'),
				'forbidden_big'	=> array('src' => './images/folders/forbidden_big.png'),
				'line'			=> array('src' => './images/folders/line.gif'),
				'folder'		=> array('src' => './images/folders/folder.png', 'new' => 'newFolder'),
				'folder_open'	=> array('src' => './images/folders/folder_open.png'),
				'page'			=> array('src' => './images/folders/file_icon.png', 'new' => 'newPage'),
				'status9'		=> array('src' => './images/folders/private.png'),
			),
			'context_menu'		=>
			array(
				array(
					'menu'		=> __('Cut'),
					'func'		=> 'cutNode',
				),
				array(
					'menu'		=> __('Copy'),
					'func'		=> 'copyNode',
				),
				array(
					'menu'		=> __('Paste'),
					'func'		=> 'pasteNode',
				),
				array(
					'menu'		=> __('Delete'),
					'func'		=> 'deleteNode',
					'confirm'	=>
					array(
						'single'	=> __('Are you sure you want to move %NODE_NAME% to the trash?'),
						'plural'	=> __('Are you sure you want to move these %NODE_COUNT% objects to the trash?'),
					),
				),
				array(
					'menu'		=> __('New'),
					'func'		=> 'createNode',
					'submenu'	=>
					array(
						array(
							'menu'		=> __('Folder'),
							'func'		=> 'createNode',
							'icon'		=> './images/folders/folder.png',
							'param'		=> 'node_type=folder&node_class=folder',
						),
						array(
							'menu'		=> __('Page'),
							'func'		=> 'createNode',
							'icon'		=> './images/folders/file_icon.png',
							'param'		=> 'node_type=page&node_class=leaf',
						),
					),
					'submenu_width'	=> '130',
				),
				array(
					'menu'		=> __('Rename'),
					'func'		=> 'editName',
				),
				array(
					'menu'		=> __('Preview'),
					'func'		=> 'preview',
				),
				array(
					'menu'		=> __('Properties'),
					'func'		=> 'open_property',
				),
			),
			'trash_context_menu'	=>
			array(
				array(
					'menu'		=> __('Empty the trash'),
					'func'		=> 'truncateNode',
					'confirm'	=> __('Are you sure you want to permanently remove the items in the trash?'),
				),
			),
			'context_menu_width'	=> '138',
			'context_menu_frame'	=> 'top',
		),
	),
);
