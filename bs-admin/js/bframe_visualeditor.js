/*
 * B-frame : php web application framework
 * Copyright (c) Bigbeat Inc. All rights reserved. (http://www.bigbeat.co.jp)
 *
 * Licensed under the GPL, LGPL and MPL Open Source licenses.
*/
	bframe.addEventListener(window, 'load', bframeVisualEditorInit);

	function bframeVisualEditorInit() {
		var objects = document.querySelectorAll('textarea.bframe_visualeditor');

		for(var i=0; i < objects.length; i++) {
			var s = new bframe.visualeditor(objects[i]);
		}
	}

	// -------------------------------------------------------------------------
	// class bframe.visualeditor
	// 
	// -------------------------------------------------------------------------
	bframe.visualeditor = function(target) {
		var self = this;
		var editor;
		var param = target.getAttribute('bframe_visualeditor_param');
		var updating_flag;

		if(param) {
			var id = bframe.getParam('parent', param);
			var parent = document.getElementById(id);
		}

		setUpCKEditor();

		function setUpCKEditor() {

			CKEDITOR.config.toolbar = [
			 ['Undo','Redo','-','Bold','Italic','Underline','Strike','-','Subscript','Superscript']
			,['JustifyLeft','JustifyCenter','JustifyRight']
			,['Link','Unlink','Anchor']
			,['Image','Table']
			,'/'
			,['Styles','Format','FontSize']
			,['TextColor','BGColor']
			,['ShowBlocks']
			,['Find','Replace']
			,['Source','Preview','Maximize','Templates']
			];

			// language
			var visual_editor_language = document.getElementById('visual_editor_language');
			CKEDITOR.config.language = visual_editor_language.value;
			CKEDITOR.config.autoParagraph = false;

			// for HTML5 tags(cancel Automatic ACF Mode)
			CKEDITOR.config.allowedContent = true;

			// br settings
			CKEDITOR.config.enterMode = 2;
			CKEDITOR.config.shiftEnterMode = 1;

			if(parent) {
				var h = parent.style.height;
			}
			else {
				var h = target.style.height;
			}

			CKEDITOR.config.coreStyles_bold = {element : 'span', attributes : {'style' : 'font-weight:bold' }};
			CKEDITOR.config.coreStyles_italic = {element : 'span', attributes : {'style' : 'font-style:italic' }};

			// protect php
			CKEDITOR.config.protectedSource.push( /<\?[\s\S]*?\?>/g );

			// root dir
			var root_dir = document.getElementById('root_dir');
			if(root_dir) {
				CKEDITOR.config.root_dir =  root_dir.value;
			}

			// body class
			var visual_editor_body_class = document.getElementById('visual_editor_body_class');
			if(visual_editor_body_class) {
				CKEDITOR.config.bodyClass =  visual_editor_body_class.value;
			}

			// filebrowser
			var filebrowser = document.getElementById('filebrowser');
			CKEDITOR.config.filebrowserBrowseUrl = filebrowser.href;
			CKEDITOR.config.filebrowserUploadUrl = filebrowser.href;

			// styles
			var visual_editor_styles = document.getElementById('visual_editor_styles');
			if(visual_editor_styles) {
				CKEDITOR.config.stylesSet = visual_editor_styles.value;
			}

			// css
			var visual_editor_css = document.getElementById('visual_editor_css');
			if(visual_editor_css) {
				CKEDITOR.config.contentsCss = [visual_editor_css.value];
			}

			// templates
			var visual_editor_templates = document.getElementById('visual_editor_templates');
			if(visual_editor_templates) {
				CKEDITOR.config.templates_files = [visual_editor_templates.value];
			}
			CKEDITOR.config.templates_replaceContent = false;

			// format tags
			CKEDITOR.config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre;address;div';

			// empty tag 0: remain 1:remove
			CKEDITOR.dtd.$removeEmpty = {
				abbr: 0,
				acronym: 0,
				b: 0,
				bdi: 0,
				bdo: 0,
				big: 0,
				cite: 0,
				code: 0,
				del: 0,
				dfn: 0,
				em: 0,
				font: 0,
				i: 0,
				ins: 0,
				label: 0,
				kbd: 0,
				mark: 0,
				meter: 0,
				output: 0,
				q: 0,
				ruby: 0,
				s: 0,
				samp: 0,
				small: 0,
				span: 0,
				strike: 0,
				strong: 0,
				sub: 0,
				sup: 0,
				time: 0,
				tt: 0,
				u: 0
			};

			// baseHref
			var baseHref = document.getElementById('baseHref');
			if(baseHref) {
				CKEDITOR.config.baseHref = baseHref.value;
			}

			if(parent) {
				editor = CKEDITOR.appendTo(parent.id, {
					height: h,
					on : {
						instanceReady : function(ev) {
							var tag_group1 = new Array('h1', 'h2', 'h3', 'h4', 'h5', 'div', 'p', 'ul', 'ol', 'dl', 'dt', 'dd', 'li'
													, 'section', 'nav', 'article', 'aside', 'header', 'footer', 'figure', 'figcaption'
													, 'embed', 'video', 'canvas', 'audio', 'source');
							var tag_group2 = new Array('span', 'a', 'img', 'td', 'time', 'mark');
							var tag_group3 = new Array('tr');

							for(var i=0; i < tag_group1.length; i++) {
								this.dataProcessor.writer.setRules( tag_group1[i], {
									indent : true,
									breakBeforeOpen : true,
									breakAfterOpen : true,
									breakBeforeClose : true,
									breakAfterClose : true
								});
							}
							for(var i=0; i < tag_group2.length; i++) {
								this.dataProcessor.writer.setRules( tag_group2[i], {
									indent : true,
									breakBeforeOpen : false,
									breakAfterOpen : false,
									breakBeforeClose : false,
									breakAfterClose : false
								});
							}
							for(var i=0; i < tag_group3.length; i++) {
								this.dataProcessor.writer.setRules( tag_group3[i], {
									indent : true,
									breakBeforeOpen : true,
									breakAfterOpen : true,
									breakBeforeClose : true,
									breakAfterClose : false
								});
							}
							this.dataProcessor.writer.setRules('comment', {
								indent : true,
								breakAfterClose : true
							});

							// for sychronization
							bframe.addEventListener(target, 'change', applyValueToEditor);

							// for submit
							if(bframe.ajaxSubmit) {
								bframe.ajaxSubmit.registerCallBackFunction(onblurEditor);
							}

							// for edit check
							if(bframe.editCheck_handler) {
								bframe.editCheck_handler.registerCallBackFunction(editCheckCallback);
								bframe.editCheck_handler.registerResetCallBackFunction(resetDirtyCallback);
							}
						},

						mode : function(ev) {
							bframe.fireEvent(window, 'resize');
						},
					}
				});
			}
			else {
				editor = CKEDITOR.replace(target.id, {
					height: h,
					on : {
						instanceReady : function(ev) {
							var tag_group1 = new Array('h1', 'h2', 'h3', 'h4', 'h5', 'div', 'p', 'ul', 'ol', 'dl', 'dt', 'dd', 'li'
													, 'section', 'nav', 'article', 'aside', 'header', 'footer', 'figure', 'figcaption'
													, 'embed', 'video', 'canvas', 'audio', 'source');
							var tag_group2 = new Array('span', 'a', 'img', 'td', 'time', 'mark');
							var tag_group3 = new Array('tr');

							for(var i=0; i < tag_group1.length; i++) {
								this.dataProcessor.writer.setRules( tag_group1[i], {
									indent : true,
									breakBeforeOpen : true,
									breakAfterOpen : true,
									breakBeforeClose : true,
									breakAfterClose : true
								});
							}
							for(var i=0; i < tag_group2.length; i++) {
								this.dataProcessor.writer.setRules( tag_group2[i], {
									indent : true,
									breakBeforeOpen : false,
									breakAfterOpen : false,
									breakBeforeClose : false,
									breakAfterClose : false
								});
							}
							for(var i=0; i < tag_group3.length; i++) {
								this.dataProcessor.writer.setRules( tag_group3[i], {
									indent : true,
									breakBeforeOpen : true,
									breakAfterOpen : true,
									breakBeforeClose : true,
									breakAfterClose : false
								});
							}
							this.dataProcessor.writer.setRules('comment', {
								indent : true,
								breakAfterClose : true
							});

							// for sychronization
							bframe.addEventListener(target, 'change', applyValueToEditor);

							// for submit
							if(bframe.ajaxSubmit) {
								bframe.ajaxSubmit.registerCallBackFunction(onblurEditor);
							}

							// for edit check
							if(bframe.editCheck_handler) {
								bframe.editCheck_handler.registerCallBackFunction(editCheckCallback);
								bframe.editCheck_handler.registerResetCallBackFunction(resetDirtyCallback);
							}

							bframe.fireEvent(window, 'resize');
						},

						mode : function(ev) {
							bframe.fireEvent(window, 'resize');
						},

						resize : function(ev) {
							bframe.fireEvent(window, 'resize');
						},
					}
				});
			}

			bframe.addEventListener(window, 'unload', cleanUp);
		}

		function onblurEditor() {
			if(!updating_flag && editor.checkDirty()) {
				target.value = editor.getData();
				bframe.fireEvent(target, 'change');
			}
		}

		function applyValueToEditor() {
			if(target.value != editor.getData()) {
				updating_flag = true;
				editor.setData(target.value, function() {resetUpdatingFlag(); editor.resetDirty()}, false);
			}
		}

		function resetUpdatingFlag() {
			updating_flag = false;
		}

		function editCheckCallback() {
			if(editor.checkDirty()) {
				bframe.editCheck_handler.setEditFlag();
			}
		}

		function resetDirtyCallback() {
			editor.resetDirty();
		}

		function cleanUp() {
			if(bframe.ajaxSubmit) bframe.ajaxSubmit.removeCallBackFunction(onblurEditor);
			if(bframe.editCheck_handler) {
				bframe.editCheck_handler.removeCallBackFunction(editCheckCallback);
				bframe.editCheck_handler.removeResetCallBackFunction(resetDirtyCallback);
			}
			for(var instanceName in CKEDITOR.instances) {
				CKEDITOR.instances[instanceName].destroy(true);
			}
		}
	}
