<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class resource_popup extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			// bframe_message
			$this->bframe_message = new B_Element($this->bframe_message_config, $this->user_auth);

			$this->_setProperty('target', '');
			$this->_setProperty('target_id', '');

			require_once('./config/tree_config.php');
			$this->tree_config = $tree_config;
			$this->tree = new B_Node($this->db
									, B_RESOURCE_NODE_TABLE
									, B_WORKING_RESOURCE_NODE_VIEW
									, $this->version['working_version_id']
									, $this->version['revision_id']
									, null
									, null
									, 1
									, null);

			$this->tree->setConfig($this->tree_config);

			if($this->request['node_id']) {
				$this->openCurrentNode($this->request['node_id']);
			}

			$this->status = true;

			if(is_array($_FILES['upload'])) {
				$response['CKEditorFuncNum'] = $this->request['CKEditorFuncNum'];
				$response['url'] = '';
				$response['message'] = __('Please use server browser');

				// Start buffering
				ob_start();

				require_once('./view/view_quick_upload.php');

				// Get buffer
				$contents = ob_get_clean();

				// Send HTTP header
				$this->sendHttpHeader();

				// Show HTML header
				$this->showHtmlHeader();

				// Show HTML body
				echo $contents;

				exit;
			}
		}

		function open() {
			// target
			$this->_setRequest('target');
			$this->_setRequest('target_id');
		}

		function view() {
			// Start buffering
			ob_start();

			require_once('./view/view_index.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/resource.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/resource_tree.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/upload.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/progress_bar.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_message.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_tree.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_dialog.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_progress_bar.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_splitter.js"></script>');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
