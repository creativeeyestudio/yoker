/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

var xhr = new XMLHttpRequest();
xhr.open("GET", "../../../build/entrypoints.json", true);
xhr.send();

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	config.allowedContent = true;
	config.baseHref = '/';
	config.bodyClass = 'no-js';
	config.contentsCss = '';
	config.defaultLanguage = 'fr';
	config.emailProtection = 'encode';
	config.entities = false;
	config.extraAllowedContent = 'i';
	config.extraPlugins = 'bgimage,codemirror,templates';
	config.height = 300;
};