<?php
/*
 * B-studio : Content Management System
 * Copyright (c) BigBeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	class resource_upload extends B_AdminModule {
		function __construct() {
			parent::__construct(__FILE__);

			$this->dir = B_RESOURCE_DIR;
			$this->current_folder = $this->global_session['resource']['current_node'];
		}

		function confirm() {
			$status = true;

			try {
				if(!$this->current_folder) {
					throw new Exception(__('No folder selected'));
				}

	 			// Check file size
				$filesize = $_POST['filesize'];
				$post_max_size = $this->util->decode_human_filesize(ini_get('post_max_size'));
				$upload_max_filesize = $this->util->decode_human_filesize(ini_get('upload_max_filesize'));
				if($filesize > $post_max_size || $filesize > $upload_max_filesize) {
					if($post_max_size < $upload_max_filesize) {
						$limit = ini_get('post_max_size');
					}
					else {
						$limit = ini_get('upload_max_filesize');
					}
					$message = __('The file size is too large. The maximun file upload size is %LIMIT%');
					$message = str_replace('%LIMIT%', $limit, $message);
					throw new Exception($message);
				}
				// Get file info
				$file = pathinfo($_POST['filename']);

				// Unify extension to lowercase for windows system
				$file['extension'] = strtolower($file['extension']);

				if(strlen($file['basename']) != mb_strlen($file['basename'])) {
					throw new Exception(__('Multi-byte characters cannot be used'));
				}
				if(preg_match('/[\\\\:\/\*\?<>\|\s]/', $file['basename'])) {
					throw new Exception(__('The following charcters cannot be used in file or folder names (\ / : * ? " < > | space)'));
				}

				if($file['extension'] == 'zip') {
					switch($this->request['extract_mode']) {
					case 'confirm':
						$response_mode = 'zipConfirm';
						$message = __('Extract %FILE_NAME% ?');
						$message = str_replace('%FILE_NAME%', $file['basename'], $message);
						break;

					case 'noextract':
						if($this->file_exists($file['basename']) && $this->request['mode'] == 'confirm') {
							$response_mode = 'confirm';
							$message = __('%FILE_NAME% already exists. Are you sure you want to overwrite?');
							$message = str_replace('%FILE_NAME%', $file['basename'], $message);
						}
						break;
					}
				}
				else {
					if($this->request['mode'] == 'confirm' && $this->file_exists($file['basename'])) {
						$response_mode = 'confirm';
						$message = __('%FILE_NAME% already exists. Are you sure you want to overwrite?');
						$message = str_replace('%FILE_NAME%', $file['basename'], $message);
					}
				}
			}
			catch(Exception $e) {
				$status = false;
				$message = $e->getMessage();
			}

			$response['status'] = $status;
			$response['mode'] = $response_mode;
			$response['message'] = $message;

			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function file_exists($file_name){
			$parent_node = $this->current_folder;
			$this->resource_node_table = new B_Table($this->db, B_RESOURCE_NODE_TABLE);

			$sql = "select * from %NODE_VIEW%
					where parent_node = '%PARENT_NODE%'
					and node_name='%FILE_NAME%'
					and del_flag = '0'";
			$sql = str_replace('%NODE_VIEW%', B_DB_PREFIX . B_WORKING_RESOURCE_NODE_VIEW, $sql);
			$sql = str_replace('%PARENT_NODE%', $this->current_folder, $sql);
			$sql = str_replace('%FILE_NAME%', $file_name, $sql);
			$rs = $this->db->query($sql);
			$row = $this->db->fetch_assoc($rs);
			if($row) {
				return true;
			}
			else {
				return false;
			}
		}

		function upload() {
			$status = true;

			try {
				// Get file info
				$file = pathinfo($_FILES['Filedata']['name']);

				if(!is_dir($this->dir)) {
					if(mkdir($this->dir)) {
						chmod($this->dir, 0777);
					}
					else {
						throw new Exception(__('Faild to create directory'));
					}
				}

				if(strtolower($file['extension']) == 'zip' && class_exists('ZipArchive') && $this->request['extract_mode'] == 'extract') {
					// Set time limit to 10 minutes
					set_time_limit(600);

					// Continue whether a client disconnect or not
					ignore_user_abort(true);

					// Check zip file inside
					$this->checkZipFile($_FILES['Filedata']['tmp_name']);

					$zip_file = B_RESOURCE_WORK_DIR . $file['basename'];
					if($status = $this->_move_uploaded_file($_FILES['Filedata']['tmp_name'], $zip_file)) {
						// send progress
						header('Content-Type: application/octet-stream');
						header('Transfer-encoding: chunked');
						flush();
						ob_flush();

						// Send start message
						$response['progress'] = 0;
						$this->sendChunk(json_encode($response));

						// Extract
						$zip = new ZipArchive();
						$zip->open($zip_file);
						$zip->extractTo(B_RESOURCE_EXTRACT_DIR);
						$zip->close();
						unlink($zip_file);

						// Controll extracted files
						$node = new B_FileNode(B_RESOURCE_EXTRACT_DIR, '/', null, null, 'all');

						// Count extract files
						$this->extracted_files = $node->nodes_count();
						$this->registerd_files = 0;

						// Register extract files
						$node->walk($this, register_archive);
						$node->remove();
						$this->removeCacheFile();

						// Create response node_info
						foreach($this->registered_archive_node as $value) {
							$node = new B_Node($this->db
												, B_RESOURCE_NODE_TABLE
												, B_WORKING_RESOURCE_NODE_VIEW
												, $this->version['working_version_id']
												, $this->version['revision_id']
												, $value
												, ''
												, 0
												, ''
												, 'auto');
							$path = $node->getParentPath();
							$response['node_info'][] = $node->getNodeList('', '', B_RESOURCE_DIR, $path);
						}
					}

					$response['status'] = $status;
					$response['progress'] = 100;
					$this->sendChunk(',' . json_encode($response));
					$this->sendChunk();	// terminate
					exit;
				}
				else {
					$node_id = $this->register($file);
					$node = new B_Node($this->db
										, B_RESOURCE_NODE_TABLE
										, B_WORKING_RESOURCE_NODE_VIEW
										, $this->version['working_version_id']
										, $this->version['revision_id']
										, $node_id
										, ''
										, 1
										, ''
										, 'auto');
					$path = $node->getParentPath();
					$response['node_info'][] = $node->getNodeList('', '', B_RESOURCE_DIR, $path);
				}
			}
			catch(Exception $e) {
				$status = false;
				$message = $e->getMessage();
			}

			$response['status'] = $status;
			$response['message'] = $message;
			header('Content-Type: application/x-javascript charset=utf-8');
			echo json_encode($response);
			exit;
		}

		function checkZipFile($zip_file) {
			$zip = new ZipArchive();
			$zip->open($zip_file);

			for($i=0; $i < $zip->numFiles; $i++) {
				$stat = $zip->statIndex($i);
				$file_name = mb_convert_encoding($stat['name'], 'UTF-8', 'auto');
				if(strlen($file_name) != mb_strlen($file_name)) {
					$message = __('Multi-byte characters cannot be used in file names. Please check contents of the zip file.');
					throw new Exception($message);
				}
			}
		}

		function register_archive($node) {
			if(!$node->parent) return;

			$ret = $this->_register_archive($node, $node_id, $contents_id);
			if($ret) {
				$node->db_node_id = $node_id;
			}
			if($node->node_type != 'folder') {
				$file = pathinfo($node->path);
				if(rename(B_RESOURCE_EXTRACT_DIR . $node->path, $this->dir . $contents_id . '.' . $file['extension'])) {
					chmod($this->dir . $contents_id . '.' . $file['extension'], 0777);
					$this->createthumbnail($this->dir, $contents_id, $file['extension'], B_THUMB_PREFIX, B_THUMB_MAX_SIZE);
				}
			}

			if(!$node->parent->db_node_id) {
				$this->registered_archive_node[] = $node_id;
			}

			$this->registerd_files++;
			$response['progress'] = round($this->registerd_files / $this->extracted_files * 100);
			$this->sendChunk(',' . json_encode($response));
		}

		function sendChunk($response=null) {
			if($response) {
				$response = $response . str_repeat(' ', 8000);
				echo sprintf("%x\r\n", strlen($response));
				echo $response . "\r\n";
			}
			else {
				echo "0\r\n\r\n";
			}
			flush();
			ob_flush();
		}

		function _register_archive($node, &$node_id, &$contents_id) {
			$this->resource_node_table = new B_Table($this->db, B_RESOURCE_NODE_TABLE);

			if($node->parent->db_node_id) {
				$parent_node = $node->parent->db_node_id;
			}
			else {
				$parent_node = $this->current_folder;
			}

			$sql = "select * from " . B_DB_PREFIX . B_WORKING_RESOURCE_NODE_VIEW . "
					where parent_node = '%PARENT_NODE%'
					and node_name='%FILE_NAME%'
					and del_flag = '0'";
			$sql = str_replace('%PARENT_NODE%', $parent_node, $sql);
			$sql = str_replace('%FILE_NAME%', $node->file_name, $sql);
			$rs = $this->db->query($sql);
			$row = $this->db->fetch_assoc($rs);

			if($row) {
				if($row['version_id'] == $this->version['working_version_id'] && $row['revision_id'] == $this->version['revision_id']) {
					return $this->update($node->fullpath, $row, $node->file_name, $node_id, $contents_id);
				}
				else {
					return $this->updateNode($node->fullpath, $row, $node->file_name, $node_id, $contents_id);
				}
			}
			else {
				return $this->insert($node->fullpath, $parent_node, $node->file_name, $node->node_type, $node->node_class, $node_id, $contents_id);
			}
		}

		function register($file) {
			$this->_register($file['basename'], $node_id, $contents_id);

			if($this->_move_uploaded_file($_FILES['Filedata']['tmp_name'], $this->dir . $contents_id . '.' . $file['extension'])) {
				chmod($this->dir . $contents_id . '.' . $file['extension'], 0777);
				$this->createthumbnail($this->dir, $contents_id, $file['extension'], B_THUMB_PREFIX, B_THUMB_MAX_SIZE);
				$this->removeCacheFile();
			}
			return $node_id;
		}

		function _register($file_name, &$node_id, &$contents_id) {
			$parent_node = $this->current_folder;
			$this->resource_node_table = new B_Table($this->db, B_RESOURCE_NODE_TABLE);

			$sql = "select * from %NODE_VIEW%
					where parent_node = '%PARENT_NODE%'
					and node_name='%FILE_NAME%'
					and del_flag = '0'";
			$sql = str_replace('%NODE_VIEW%', B_DB_PREFIX . B_WORKING_RESOURCE_NODE_VIEW, $sql);
			$sql = str_replace('%PARENT_NODE%', $this->current_folder, $sql);
			$sql = str_replace('%FILE_NAME%', $file_name, $sql);
			$rs = $this->db->query($sql);
			$row = $this->db->fetch_assoc($rs);

			if($row) {
				if($row['version_id'] == $this->version['working_version_id'] && $row['revision_id'] == $this->version['revision_id']) {
					return $this->update($_FILES['Filedata']['tmp_name'], $row, $file_name, $node_id, $contents_id);
				}
				else {
					return $this->updateNode($_FILES['Filedata']['tmp_name'], $row, $file_name, $node_id, $contents_id);
				}
			}
			else {
				return $this->insert($_FILES['Filedata']['tmp_name'], $parent_node, $file_name, 'file', 'leaf', $node_id, $contents_id);
			}
		}

		function update($filepath, $row, $file_name, &$node_id, &$contents_id) {
			// start transaction
			$this->db->begin();

			$node_id = $row['node_id'];
			$contents_id = $row['node_id'] . '_' . $this->version['working_version_id'] . '_' . $this->version['revision_id'];
			$param['node_id'] = $row['node_id'];
			$param['node_name'] = $file_name;
			$param['contents_id'] = $contents_id;
			$param['version_id'] = $this->version['working_version_id'];
			$param['revision_id'] = $this->version['revision_id'];
			$param['update_user'] = $this->user_id;
			$param['update_datetime'] = time();
			$param['create_datetime'] = time();

			// set file size
			if($row['node_type'] == 'file') {
				$param['file_size'] = filesize($filepath);
				$param['human_file_size'] = B_Util::human_filesize($param['file_size'], 'K');
			}
			$size = getimagesize($filepath);
			if($size) {
				$param['image_size'] = $size[0] * $size[1];
				$param['human_image_size'] = $size[0] . 'x' . $size[1];
			}

			$ret = $this->resource_node_table->update($param);

			if($ret) {
				$this->db->commit();
			}
			else {
				$this->db->rollback();
			}

			return $ret;
		}

		function updateNode($filepath, $row, $file_name, &$node_id, &$contents_id) {
			$node_id = $row['node_id'];

			$node = new B_Node($this->db
							, B_RESOURCE_NODE_TABLE
							, B_WORKING_RESOURCE_NODE_VIEW
							, $this->version['working_version_id']
							, $this->version['revision_id']
							, $row['node_id']
							, null
							, 1
							, null);

			// start transaction
			$this->db->begin();
			$node->cloneNode($row['node_id']);
			$contents_id = $row['node_id'] . '_' . $this->version['working_version_id'] . '_' . $this->version['revision_id'];
			$ret = $node->setContentsId($contents_id, $this->user_id);

			// set file size
			if($row['node_type'] == 'file') {
				$param['file_size'] = filesize($filepath);
				$param['human_file_size'] = B_Util::human_filesize($param['file_size'], 'K');
			}
			$size = getimagesize($filepath);
			if($size) {
				$param['image_size'] = $size[0] * $size[1];
				$param['human_image_size'] = $size[0] . 'x' . $size[1];
			}
			$param['node_name'] = $file_name;

			$ret &= $node->updateNode($param, $this->user_id);

			if($ret) {
				$this->db->commit();
			}
			else {
				$this->db->rollback();
			}

			return $ret;
		}

		function insert($filepath, $parent_node, $file_name, $node_type, $node_class, &$node_id, &$contents_id) {
			$node = new B_Node($this->db
							, B_RESOURCE_NODE_TABLE
							, B_WORKING_RESOURCE_NODE_VIEW
							, $this->version['working_version_id']
							, $this->version['revision_id']
							, $parent_node
							, null
							, 1
							, null);

			// start transaction
			$this->db->begin();
			$ret = $node->insert($node_type, $node_class, $this->user_id, $new_node_id, $new_node_name);
			if($ret) {
				$node_id = $new_node_id;
				$new_node = new B_Node($this->db
									, B_RESOURCE_NODE_TABLE
									, B_WORKING_RESOURCE_NODE_VIEW
									, $this->version['working_version_id']
									, $this->version['revision_id']
									, $new_node_id
									, null
									, 1
									, null);

				$contents_id = $new_node_id . '_' . $this->version['working_version_id'] . '_' . $this->version['revision_id'];
				$ret = $new_node->setContentsId($contents_id, $this->user_id);

				$param['node_name'] = $file_name;

				// set file size
				if($node_type == 'file') {
					$param['file_size'] = filesize($filepath);
					$param['human_file_size'] = B_Util::human_filesize($param['file_size'], 'K');
					$size = getimagesize($filepath);
					if($size) {
						$param['image_size'] = $size[0] * $size[1];
						$param['human_image_size'] = $size[0] . 'x' . $size[1];
					}
				}

				$ret &= $new_node->updateNode($param, $this->user_id);
				$this->db->commit();
			}
			else {
				$this->db->rollback();
			}
			return $ret;
		}

		function _move_uploaded_file($source, $destination) {
			if(!move_uploaded_file($source, $destination)) {
				switch($_FILES['Filedata']['error']) {
				case 1:
					$this->error_message = __('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
					break;

				case 2:
					$this->error_message = __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
					break;

				case 3:
					$this->error_message = __('The uploaded file was only partially uploaded.');
					break;

				case 4:
					$this->error_message = __('No file was uploaded.');
					break;

				case 6:
					$this->error_message = __('Missing a temporary folder. Introduced in PHP 5.0.3.');
					break;

				case 7:
					$this->error_message = __('Failed to write file to disk. Introduced in PHP 5.1.0.');
					break;

				case 8:
					$this->error_message = __('A PHP extension stopped the file upload.');
					break;

				default:
					$this->error_message = 'move_uploaded_file error';
					break;
				}
				$this->log->write('ERROR:' . $this->error_message);
				throw new Exception($this->error_message);

				return false;
			}

			return true;
		}

		function createthumbnail($dir, $file_name, $file_extension, $prefix, $max_size) {
			$source_file_path = $dir . $file_name . '.' . $file_extension;
			switch(strtolower($file_extension)) {
			case 'jpg':
			case 'jpeg':
				if(!function_exists('imagecreatefromjpeg')) return;
				$image = @imagecreatefromjpeg($source_file_path);
				break;

			case 'gif':
				if(!function_exists('imagecreatefromgif')) return;
				$image = @imagecreatefromgif($source_file_path);
				break;

			case 'png':
				if(!function_exists('imagecreatefrompng')) return;
				$image = @imagecreatefrompng($source_file_path);
				break;

			case 'bmp':
				$image = B_Util::imagecreatefrombmp($source_file_path);
				break;

			case 'avi':
			case 'flv':
			case 'mov':
			case 'mp4':
			case 'mpg':
			case 'mpeg':
			case 'wmv':
				$source_file_path = $this->createMovieThumbnail($source_file_path);
				if(!function_exists('imagecreatefromjpeg')) return;
				$image = @imagecreatefromjpeg($source_file_path);
				break;

			default:
				return;
			}

			$image_size = getimagesize($source_file_path);
			$width = $image_size[0];
			$height = $image_size[1];

			if($width > $max_size) {
				if($width > $height) {
					$height = round($height * $max_size / $width);
					$width = $max_size;
				}
				else {
					$width = round($width * $max_size / $height);
					$height = $max_size;
				}
			}
			else if($height > $max_size) {
				$width = round($width * $max_size / $height);
				$height = $max_size;
			}
			if(!$width) $width=1;
			if(!$height) $height=1;

			$new_image = imagecreatetruecolor($width, $height);

			switch(strtolower($file_extension)) {
			case 'gif':
				$trnprt_indx = imagecolortransparent($image);
				if($trnprt_indx >= 0) {
					$trnprt_color = imagecolorsforindex($image, $trnprt_indx);
					$trnprt_indx = imagecolorallocate($new_image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
					imagefill($new_image, 0, 0, $trnprt_indx);
					imagecolortransparent($new_image, $trnprt_indx);

				} 
				break;

			case 'png':
				$trnprt_indx = imagecolortransparent($image);
				if($trnprt_indx >= 0) {
					$trnprt_color = imagecolorsforindex($image, $trnprt_indx);
					$trnprt_indx = imagecolorallocate($new_image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
					imagefill($new_image, 0, 0, $trnprt_indx);
					imagecolortransparent($new_image, $trnprt_indx);

				} 
				imagealphablending($new_image, false);
				$color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
				imagefill($new_image, 0, 0, $color);
				imagesavealpha($new_image, true);
				break;
			}

			imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $image_size[0], $image_size[1]);

			$thumbnail_file_path = $dir . $prefix . $file_name . '.' . $file_extension;

			switch(strtolower($file_extension)) {
			case 'jpg':
			case 'jpeg':
			case 'bmp':
				imagejpeg($new_image, $thumbnail_file_path, 100);
				break;

			case 'gif':
				imagegif($new_image, $thumbnail_file_path);
				break;

			case 'png':
				imagepng($new_image, $thumbnail_file_path);
				break;

			case 'avi':
			case 'flv':
			case 'mov':
			case 'mp4':
			case 'mpg':
			case 'mpeg':
			case 'wmv':
				$thumbnail_file_path = $dir . $prefix . $file_name . '.jpg';
				imagejpeg($new_image, $thumbnail_file_path, 100);
				unlink($source_file_path);
				break;
			}

			chmod($thumbnail_file_path, 0777);
		}

		function createMovieThumbnail($filename) {
			$ffmpeg = FFMPEG;
			$output = B_RESOURCE_WORK_DIR . time() . 'tmp.jpg';
			if(substr(PHP_OS, 0, 3) === 'WIN') {
				$cmdline = "$ffmpeg -ss 3 -i $filename -f image2 -vframes 1 $output 2>&1";
				$p = popen($cmdline, 'r');
				if($p) {
					$this->log->write(fread($p, 2096));
					pclose($p);
				}
				else {
					$this->log->write('error');
				}
			}
			else {
				$cmdline = "$ffmpeg -ss 3 -i $filename -f image2 -vframes 1 $output";
				exec("$cmdline");
			}
			return $output;
		}
	}
