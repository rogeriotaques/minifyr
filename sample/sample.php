<?php 

require_once('../minifyr.php');

// get settings and files to minify
// options are:
//   f			- Required. File or comma separated file list
//	 screen	- Optional. Void. Forces the download of minified file.
// 	 debug	- Optional. Void. When given, skip minification.

$debug  = isset( $_GET[ 'debug' ] ) ? TRUE : FALSE;
$screen = isset( $_GET[ 'screen' ] ) ? TRUE : FALSE;
$files  = isset( $_GET[ 'f' ] ) ? $_GET[ 'f' ] : NULL;

$m = new RT\Minifyr($debug, $screen);
$m->files( explode(',', $files) )->uglify(true)->render();

?>