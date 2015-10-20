<?php

/**
 * Minifyr - A minifier PHP script for CSS and JS scripts.
 * Copyright (c) 2014, Rogério Taques.
 *
 * Licensed under MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons
 * to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
 * BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @requires PHP4+
 * @author Rogério Taques (rogerio.taques@gmail.com)
 * @see https://github.com/rogeriotaques/minifyr
 *
 * @uses
 * 		minifyr.php?f=path/to/file.css ( force to download file )
 * 		minifyr.php?f=path/to/file.css&screen ( not force download )
 * 		minifyr.php?f=path/to/file.css&screen&debug ( do not minify and not force download )
 */

$am_version = '1.6 beta';

/**
 * Do minify ...
 */
function minify( $file_path, $content_type )
{
	// get file content
	$content_file = @file_get_contents( $file_path );

	// remove all comment blocks
	$content_file = preg_replace( '#/\*.*?\*/#s', '', $content_file );

	// remove all comment lines
	$content_file = preg_replace( '#//(.*)$#m', '', $content_file );

	// remove all blank spaces
	$content_file = preg_replace( '#\s+#', ' ', $content_file );

	// remove unecessary spaces (before|after) some signs ...
	$content_file = str_replace( array('{ ',' {'), '{', $content_file );
	$content_file = str_replace( array('} ',' }'), '}', $content_file );
	$content_file = str_replace( array('; ',' ;'), ';', $content_file );
	$content_file = str_replace( array(': ',' :'), ':', $content_file );
	$content_file = str_replace( array(', ',' ,'), ',', $content_file );
	$content_file = str_replace( array('|| ',' ||'), '||', $content_file );
	$content_file = str_replace( array('! ',' !'), '!', $content_file );

    // perform different ways to remove some unecesary spaces (before|after) some signs ...
    switch( $content_type )
    {
        case 'css':

            $content_file = str_replace( array('( ',' ('), '(', $content_file );
            $content_file = str_replace( array(' )'), ')', $content_file );
            $content_file = str_replace( array('= ',' ='), '=', $content_file );

            break;

        case 'js' :

            $content_file = str_replace( array('( ',' ('), '(', $content_file );
            $content_file = str_replace( array(' )',') '), ')', $content_file );
            $content_file = str_replace( array('= ',' ='), '=', $content_file );
            $content_file = str_replace( array('+ ',' +'), '+', $content_file );
            $content_file = str_replace( array('- ',' -'), '-', $content_file );
            $content_file = str_replace( array('* ',' *'), '*', $content_file );
            $content_file = str_replace( array('/ ',' /'), '/', $content_file );

            break;
    }

	return trim( $content_file );
}

/**
 * Fix all relative paths that are given into file's content.
 * It's useful when designers provide relative paths for image or additional css files into given css file.
 * Avoid loose reference for images set in css file.
 */
function path_fix( $content, $file_path )
{
	$content = preg_replace('/(\'|\"|\()(\.\.\/)/', "$1{$file_path}/$2", $content);
	$content = preg_replace('/(url\()(\'|\"){0,1}([a-zA-Z0-9\-\_\.]+)(\.png|\.jpg|\.jpge|\.gif|\.bmp|\.PNG|\.JPG|\.JPEG|\.GIF|\.BMP])/', "$1$2{$file_path}/$3$4", $content);

	return $content;
}

$allow = array('css','js');		// allowed file extensions
$minified = array();					// a list of minified files

$content_types = array('css' => 'text/css', 'js' => 'text/javascript');
$content_type  = null;
$content = '';

// get settings and files to minify
// options are:
//   f			- Required. File or comma separated file list
//	 screen	- Optional. Void. Forces the download of minified file.
// 	 debug	- Optional. Void. When given, skip minification.

$debug  = isset( $_GET[ 'debug' ] ) ? TRUE : FALSE;
$screen = isset( $_GET[ 'screen' ] ) ? TRUE : FALSE;
$files  = isset( $_GET[ 'f' ] ) ? $_GET[ 'f' ] : NULL;
$files  = explode( ',', $files );

$file_ext = null;
$is_minified = false;

foreach ($files as $file)
{
	// allow external files
	// whenever it's an external file, load it from its source
	$external = preg_match('/^external\|/', $file) ? TRUE : FALSE;
	if ($external) $file = preg_replace('/^external\|/', 'http://', $file);

	$inf = pathinfo($file);
	$is_minified = strpos($inf['basename'], '.min') !== false;

	// ignore file if it's invalid or was minified already.
	// files considered invalid are: not allowed extensions or with path pointing to parent folders (../)
	if ( !$file || !in_array( $inf['extension'], $allow ) || strpos( $inf['dirname'], '../' ) !== false || in_array($inf['basename'], $minified) )
	{
		$content .= "/* File: {$file} was ignored. It's invalid or was minified already. */".PHP_EOL.PHP_EOL;
		continue;
	}

	// decide the content type according first file type.
	if (! $content_type)
	{
		$content_type = $content_types[ $inf['extension'] ];
		$file_ext = $inf['extension'];
	}

	// if extension isn't the same of first minified files, then ignore it
	if ( $content_type != $content_type )
	{
		$content .= "/* File: {$file} was ignored. File's extension doesn't match file type pattern. */".PHP_EOL.PHP_EOL;
		continue;
	}



	// prevent double minification ...
	$minified[] = $file;

	if (!$is_minified)
	{
	$minified_content = !$debug ? minify( $file, $content_type ) : @file_get_contents( $file );
	$minified_content = $content_type == 'css' ? path_fix( $minified_content, $inf['dirname'] ) : $minified_content;
	}
	else
	{
		$minified_content = @file_get_contents($file);
	}

	$content .= "/* File: {$file} */".PHP_EOL.PHP_EOL;
	$content .= $minified_content.PHP_EOL.PHP_EOL;

}

// Enable gzip compression
ob_start("ob_gzhandler");

// Allow cache
header('Cache-Control: public');

// Expires in a day
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

// Set right content-type and charset ...
header("Content-type: {$content_type}; charset=utf-8" );

if (! $screen)
{
	// force file as 'downloadble file
	header("Content-disposition: attachment; filename=minified.{$file_ext}");
}

$header  = '/** '.PHP_EOL;
$header .= " * Minifyr #v.{$am_version} ".PHP_EOL;
$header .= ' * Licensed under MIT license:'.PHP_EOL;
$header .= ' * http://www.opensource.org/licenses/mit-license.php'.PHP_EOL;
$header .= ' * @author Rogerio Taques (rogerio.taques@gmail.com)'.PHP_EOL;
$header .= ' * @see https://github.com/rogeriotaques/minifyr'.PHP_EOL;
$header .= ' */'.PHP_EOL.PHP_EOL;

echo $header, $content;

?>
