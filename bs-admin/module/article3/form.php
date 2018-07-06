<?php
/*
 * B-studio : Content Management System
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class article3_form extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			require_once('./config/editor_config.php');
			require_once('./config/settings_config.php');
			$this->settings = new B_Element($settings_config);
			$this->tab_control = new B_Element($tab_control_config);
			$this->editor = new B_Element($editor_config);
			$this->result = new B_Element($result_config);
			$this->result_control = new B_Element($result_control_config);

			$this->main_table = new B_Table($this->db, 'article3');

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
				$this->setThumnail($row['title_img_file']);
				$this->settings->setValue($row);
				$this->editor->setValue($row);
				$obj = $this->editor->getElementByName('readOnly');
				$obj->value = 'true';
				break;

			default:
				$this->control = new B_Element($this->input_control_config);
				if($this->request['article_id']) {
					$article_id = $this->db->real_escape_string($this->request['article_id']);
				}
				else {
					$article_id = '0000000000';
				}

				$sql = "select * from " . B_DB_PREFIX . "article3 where article_id = '$article_id'";

				$rs=$this->db->query($sql);
				$row=$this->db->fetch_assoc($rs);
				if(!$row) $row['article_id'] = '0000000000';

				$this->category = $this->getCategory();
				$row['category'] = $this->getCategoryName($this->category, $row['category_id']);
				$this->editor->setValue($row);
				$this->settings->setValue($row);
				$this->setThumnail($row['title_img_file']);
				$this->setDetailStatus();

				break;
			}
			$this->settings->setFilterValue($this->session['mode']);
		}

		function getCategoryName($category, $category_id) {
			$id_array = explode(',', $category_id);
			if(is_array($id_array)) {
				foreach($id_array as $id) {
					if(!$category[$id]) {
						continue;
					}
					if($name) $name.= ' / ';
					$name.= $category[$id];
				}
				return $name;
			}
		}

		function getCategory() {
			$sql = "select * from " . B_DB_PREFIX . "v_category2 where parent_node = 'root' order by disp_seq";
			$rs = $this->db->query($sql);
			while($row = $this->db->fetch_assoc($rs)) {
				$name = $row['node_name'];
				$data[$row['node_id']] = $name;
			}
			return $data;
		}

		function _validate_callback($param) {
			$article_id = $this->request['article_id'];
			if(!$article_id) return true;

			$obj = $param['obj'];
			$permalink = $org = $obj->value;

			$suffix = 2;
			while($this->checkPermalink($article_id, $permalink)) {
				$permalink = $org . '-' . $suffix++;
			}
			$obj->value = $permalink;

			return true;
		}

		function checkPermalink($article_id, $permalink) {
			$article_id = $this->db->real_escape_string($article_id);
			$permalink = $this->db->real_escape_string($permalink);

			$sql = "select count(*) cnt from " . B_DB_PREFIX . "article3 where permalink = binary '$permalink' and article_id <> '$article_id'";
			$rs = $this->db->query($sql);
			$row = $this->db->fetch_assoc($rs);

			return $row['cnt'];
		}

		function setThumnail($img_path) {
			if(!$img_path) return;
			if(!file_exists(B_UPLOAD_DIR . $img_path)) return;

			$file_info = pathinfo($img_path);
			$thumnail_path = $this->util->getPath(B_UPLOAD_URL, $this->util->getPath($file_info['dirname'], B_THUMB_PREFIX . $file_info['basename']));
			$html = '<img src="' . $thumnail_path . '" alt="title image" />';
			$obj = $this->settings->getElementByName('title_img');
			$obj->value = $html;
		}

		function setDetailStatus() {
			$obj = $this->settings->getElementByName('description_flag');
			if($obj->value == '1') {
				$obj = $this->settings->getElementByName('external_link_container');
				$obj->start_html = $obj->start_html_d;

				$obj = $this->settings->getElementByName('external_link');
				$obj->disabled = true;
				$obj = $this->settings->getElementByName('url');
				$obj->disabled = true;
				$obj = $this->settings->getElementByName('external_window');
				$obj->disabled = true;
			}
		}

		function register() {
			try {
				$ret = true;
				$article_id = $this->request['article_id'];

				$this->editor->setValue($this->request);
				$this->settings->setValue($this->request);

				if($this->post['external_link'] && !$this->post['url']) {
					$obj = $this->settings->getElementByName('url');
					$obj->status = false;
				}

				if($article_id != '0000000000') {
					$ret = $this->settings->validate();
					$ret &= $this->editor->validate();
				}

				if($ret) {
					if($this->_register()) {
						$this->message = __('Saved');
						$this->status = true;
						$this->session['mode'] = 'update';
					}
					else {
						$this->message = __('DB error');
						$this->status = false;
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

			$this->setThumnail($this->request['title_img_file']);
			$this->setDetailStatus();
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
				$response['values'] = array('article_id' => $article_id, 'update_datetime' => time());
			}

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function _register() {
			$this->editor->getValue($param);
			$this->settings->getValue($param);
			$param['del_flag'] = '0';
			$param['article_date_u'] = strtotime($param['article_date_t']);

			$this->db->begin();
			if($this->session['mode'] == 'insert' && $param['article_id'] == '') {
				$param['create_user'] = $this->user_id;
				$param['create_datetime'] = time();
				$param['update_user'] = $this->user_id;
				$param['update_datetime'] = time();
				$ret = $this->main_table->selectInsert($param);
				if($ret) {
					$param['article_id'] = $this->main_table->selectMaxValue('article_id');
					$param['permalink'] = $param['article_id'];
					$ret = $this->main_table->update($param);

					$this->settings->setValue($param);
				}
			}
			else {
				$param['update_user'] = $this->user_id;
				$param['update_datetime'] = time();
				if($param['article_id'] == '0000000000') {
					$param['del_flag'] = '1';
				}
				$ret = $this->main_table->upsert($param);
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

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/article.css">');
			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/calendar.css">');
			$this->html_header->appendProperty('script', '<script src="js/bframe_tab.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_edit_check.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/ckeditor/ckeditor.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_visualeditor.js"></script>');
			$this->html_header->appendProperty('script', '<script src="js/bframe_calendar.js"></script>');

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

			$this->html_header->appendProperty('css', '<link rel="stylesheet" href="css/article.css">');

			// Show HTML header
			$this->showHtmlHeader();

			// Show HTML body
			echo $contents;
		}
	}
