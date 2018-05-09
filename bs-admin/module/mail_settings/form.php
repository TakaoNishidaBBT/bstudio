<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class mail_settings_form extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			require_once('./config/editor_config.php');
			require_once('./config/settings_config.php');
			$this->settings = new B_Element($settings_config);
			$this->tab_control = new B_Element($tab_control_config);
			$this->editor = new B_Element($editor_config);
			$this->result = new B_Element($result_config);
			$this->result_control = new B_Element($result_control_config);

			$this->main_table = new B_Table($this->db, 'mail_settings');

			$this->input_control_config = $input_control_config;
			$this->confirm_control_config = $confirm_control_config;
			$this->delete_control_config = $delete_control_config;
		}

		function select() {
			$this->session['mode'] = $this->request['mode'];

			switch($this->request['mode']) {
			case 'delete':
				$this->control = new B_Element($this->delete_control_config);
				$row = $this->main_table->selectByPk($this->request);
				$this->settings->setValue($row);
				$this->editor->setValue($row);
				break;

			default:
				$this->control = new B_Element($this->input_control_config);
				if($this->request['mail_id']) {
					$row = $this->main_table->selectByPk($this->request);
					$this->editor->setValue($row);
					$this->settings->setValue($row);
				}
				break;
			}
			$this->settings->setFilterValue($this->session['mode']);
		}

		function register() {
			try {
				$mail_id = $this->request['mail_id'];

				$this->editor->setValue($this->request);
				$this->settings->setValue($this->request);

				if($this->post['external_link'] && !$this->post['url']) {
					$obj = $this->settings->getElementByName('url');
					$obj->status = false;
				}


				$ret = $this->settings->validate();
				$ret &= $this->editor->validate();

				if($ret) {
					if($this->_register()) {
						$this->message = __('Saved');
						$this->status = true;
						$this->session['mode'] = 'select';
					}
				}
				else {
					$this->message = __('This is an error in your entry');
					$this->status = false;
				}
			}
			catch(Exception $e) {
				$this->status = false;
				$this->mode = 'alert';
				$this->message = $e->getMessage();
			}

			$this->settings->setFilterValue($this->session['mode']);

			$title = $this->editor->getElementById('title-container');
			$response['innerHTML'] = array(
				'settings-inner'	=> $this->settings->getHtml(),
				'title-container'	=> $title->getHtml(),
			);

			$response['status'] = $this->status;
			$response['mode'] = $this->mode;
			$response['message_obj'] = 'message';
			$response['message'] = $this->message;
			if($this->status && $this->mode != 'confirm') {
				$response['values'] = array('mail_id' => $mail_id, 'update_datetime' => time());
			}

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function _register() {
			$this->editor->getValue($param);
			$this->settings->getValue($param);
			$param['del_flag'] = '0';

			$this->db->begin();
			if($this->session['mode'] == 'insert' && $param['mail_id'] == '') {
				$param['create_user'] = $this->user_id;
				$param['create_datetime'] = time();
				$param['update_user'] = $this->user_id;
				$param['update_datetime'] = time();
				$ret = $this->main_table->selectInsert($param);

				$param['mail_id'] = $this->main_table->selectMaxValue('mail_id');
				$this->settings->setValue($param);
			}
			else {
				$param['update_user'] = $this->user_id;
				$param['update_datetime'] = time();
				$ret = $this->main_table->update($param);
			}

			if($ret) {
				$this->db->commit();
			}
			else {
				$this->db->rollback();
			}
			return $ret;
		}

		function delete() {
			$param = $this->post;
			$param['del_flag'] = '1';
			$param['update_user'] = $this->user_id;
			$param['update_datetime'] = time();

			$this->db->begin();
			$ret = $this->main_table->update($param);
			$row = $this->main_table->selectByPk($this->post);
			$param = $row;

			if($ret) {
				$this->db->commit();
				$param['action_message'] = __('was deleted.');
			}
			else {
				$this->db->rollback();
				$param['action_message'] = __('was failed to delete.');
			}
			$this->result->setValue($param);

			$this->setView('resultView');
		}

		function back() {
			$this->settings->setValue($this->session['request']);
			$this->editor->setValue($this->session['request']);
			$this->setThumnail($this->session['request']['title_img_file']);

			$this->control = new B_Element($this->input_control_config);
		}

		function view() {
			// Start buffering
			ob_start();

			require_once('./view/view_form.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/mail_settings.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/calendar.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/selectbox.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_tab.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_edit_check.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/ckeditor/ckeditor.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_visualeditor.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_calendar.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_selectbox.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_contenteditor.js"></script>');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}

		function resultView() {
			// Start buffering
			ob_start();

			require_once('./view/view_result.php');

			// Get buffer
			$contents = ob_get_clean();

			// Send HTTP header
			$this->sendHttpHeader();

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/mail_settings.css">');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}