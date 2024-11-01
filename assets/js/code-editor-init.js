if ('undefined' === typeof window.wpgbcm) {
	// GutenBerg CodeMirror wpgbcm
	window.wpgbcm = {};
}
if ( 'undefined' === typeof window.wpgbcm.codeEditor ) {
	window.wpgbcm.codeEditor = {};
}

( function( $, wpgbcm, CodeMirror ) {
	'use strict';

		// set mode url path
		CodeMirror.modeURL = GB_CODEMIRROR_URL + "mode/%N/%N.js";

		/**
		 * Default settings for code editor.
		 *
		 * @type {object}
		 */
		wpgbcm.codeEditor.defaultSettings = {
			codemirror: {},
			// csslint: {},
			// htmlhint: {},
			// jshint: {},
			// onTabNext: function() {},
			// onTabPrevious: function() {},
			// onChangeLintingErrors: function() {},
			// onUpdateErrorNotice: function() {}
		};

		/**
		 * @typedef {object} CodeEditorInstance
		 * @property {object} settings - The code editor settings.
		 * @property {CodeMirror} codemirror - The CodeMirror instance.
		 */

		/**
		 * Initialize Code Editor (CodeMirror) for an existing textarea.
		 *
		 * @since 4.9.0
		 *
		 * @param {string|jQuery|Element} textarea - The HTML id, jQuery object, or DOM Element for the textarea that is used for the editor.
		 * @param {object}                [settings] - Settings to override defaults.
		 * @param {Function}              [settings.onChangeLintingErrors] - Callback for when the linting errors have changed.
		 * @param {Function}              [settings.onUpdateErrorNotice] - Callback for when error notice should be displayed.
		 * @param {Function}              [settings.onTabPrevious] - Callback to handle tabbing to the previous tabbable element.
		 * @param {Function}              [settings.onTabNext] - Callback to handle tabbing to the next tabbable element.
		 * @param {object}                [settings.codemirror] - Options for CodeMirror.
		 * @param {object}                [settings.csslint] - Rules for CSSLint.
		 * @param {object}                [settings.htmlhint] - Rules for HTMLHint.
		 * @param {object}                [settings.jshint] - Rules for JSHint.
		 * @returns {CodeEditorInstance} Instance.
		 */
		wpgbcm.codeEditor.initialize = function initialize(textarea, settings) {
			var $textarea, codemirror, instanceSettings, instance;
			if ('string' === typeof textarea) {
				$textarea = $('#' + textarea);
			} else {
				$textarea = $(textarea);
			}

			instanceSettings = $.extend({}, wpgbcm.codeEditor.defaultSettings, settings);
			instanceSettings.codemirror = $.extend({}, instanceSettings.codemirror);

			codemirror = CodeMirror.fromTextArea($textarea[0], instanceSettings.codemirror);

			instance = {
				settings: instanceSettings,
				codemirror: codemirror
			};

			// console.log(codemirror);
			codemirror.setOption("mode", instanceSettings.codemirror.mime);

			wpgbcm.autoLoadTheme(instanceSettings.codemirror.theme);
			CodeMirror.autoLoadMode(codemirror, instanceSettings.codemirror.mode);

			return instance;
		};

		wpgbcm.autoLoadTheme = function (theme) {
			var theme_id = 'gutenberg-codemirror-block-theme-' + theme + '-css',
				theme_href = GB_CODEMIRROR_URL + 'theme/' + theme + '.css',
				loaded_theme = document.getElementById(theme_id);

			if (loaded_theme == undefined) {
				var head = document.getElementsByTagName('head')[0];
				var link = document.createElement('link');
				link.rel = 'stylesheet';
				link.id = theme_id;
				link.href = theme_href;
				head.appendChild(link);
			} else {
				// loaded_theme.href = theme_href;
				console.log(theme, 'Loaded');
			}
		}

		wpgbcm.frontEndInitialization = function () {
			var codeBlocks = document.querySelectorAll('.code-block .CodeMirror');
			// console.log(codeBlocks);

			for (var i = 0; i < codeBlocks.length; i++) {
				// console.log(codeBlocks[i].textContent);

				var block = codeBlocks[i],
					dataset = block.dataset,
					mode = dataset.mode,
					mime = dataset.mime,
					theme = dataset.theme,
					line = (dataset.line == 'yes') ? true : false,
					code = codeBlocks[i].textContent,
					id = 'code-block-' + i;

				block.setAttribute('id', id);

				wpgbcm.autoLoadTheme(theme);

				if (mode !== undefined) {
					CodeMirror.modeURL = GB_CODEMIRROR_URL + "mode/%N/%N.js";
					// wpgbcm.codemirror_from_textarea(block, id, code, mode, mime, theme, line);
					wpgbcm.codemirrorInit(id, code, mode, mime, theme, line);
					// wpgbcm.runmode(id, code, mode, mime);
				}
			}
		};

		wpgbcm.codemirrorInit = function (id, code, mode, mime, theme, line) {
			var el = document.getElementById(id);
			el.style = "display: none";
			el.innerHTML = '';
			var editor = CodeMirror(el.parentNode, {
				value: code,
				lineNumbers: line,
				readOnly: 'nocursor',
			});

			editor.setOption("mode", mime);
			editor.setOption("theme", theme);

			CodeMirror.autoLoadMode(editor, mode);
		}

		wpgbcm.runmode = function (id, code, mode, mime) {
			CodeMirror.requireMode(mode, function () {
				CodeMirror.runMode(
					code, //Code Content
					mime, // Mode
					document.getElementById(id) // Element Node
				);
			});
		}

		/*
		wpgbcm.codemirror_from_textarea = function (block, id, code, mode, mime, theme, line) {
			var el = document.getElementById(id);
			el.style = "display: none";
			el.innerHTML = '';
			// console.log(block);

			// textarea element
			var textarea = document.createElement('textarea');
			id = id+ '-editor';
			textarea.id = id;
			textarea.value = code;
			textarea.dataset = block.dataset;
			// block.replaceWith(textarea);
			block.after(textarea);

			var editor = CodeMirror.fromTextArea(document.getElementById(id), {
				value: code,
				lineNumbers: line,
				readOnly: 'nocursor',
			});

			editor.setOption("mode", mime);
			editor.setOption("theme", theme);

			CodeMirror.autoLoadMode(editor, mode);
		}
		*/


})( window.jQuery, window.wpgbcm, window.CodeMirror );
