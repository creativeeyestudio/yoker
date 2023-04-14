/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

var xhr = new XMLHttpRequest();
xhr.open("GET", "../../../build/entrypoints.json", true);
xhr.send();

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.language = 'fr';
	config.extraPlugins = 'codemirror,filebrowser';
	config.allowedContent = true;
	config.extraAllowedContent = 'i';
	config.entities = false;
	config.baseHref = '/';
	config.contentsCss = '';
	config.height = 300;
	config.bodyClass = 'no-js header-second';
};
