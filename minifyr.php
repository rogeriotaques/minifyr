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
 */

$am_version = '1.0 beta';

function minify( $file_path ) 
{
	// get file content
	$content_file = @file_get_contents( $file_path );
	
	// remove all comment lines
	$content_file = preg_replace( '#//(.*)$#m', '', $content_file );
	
	// remove all comment blocks
	$content_file = preg_replace( '#/\*.*?\*/#s', '', $content_file );
	
	// remove all blank spaces
	$content_file = preg_replace( '#\s+#', ' ', $content_file );
	
	// adjust some missing details ...
	$content_file = str_replace( array('{ ',' {'), '{', $content_file );
	$content_file = str_replace( array('} ',' }'), '}', $content_file );
	$content_file = str_replace( array('( ',' ('), '(', $content_file );
	$content_file = str_replace( array(') ',' )'), ')', $content_file );
	$content_file = str_replace( array('; ',' ;'), ';', $content_file );
	$content_file = str_replace( array(': ',' :'), ':', $content_file );
	$content_file = str_replace( array(', ',' ,'), ',', $content_file );
	$content_file = str_replace( array('= ',' ='), '=', $content_file );
	$content_file = str_replace( array('+ ',' +'), '+', $content_file );
	$content_file = str_replace( array('- '), '-', $content_file );
	$content_file = str_replace( array('* ',' *'), '*', $content_file );
	$content_file = str_replace( array('/ ',' /'), '/', $content_file );
	$content_file = str_replace( array('|| ',' ||'), '||', $content_file );
	$content_file = str_replace( array('! ',' !'), '!', $content_file );

	return trim( $content_file );
}

$allow = array('css','js');	// allowed file extensions
$minified = array();		// a list of minified files

$content_types = array('css' => 'text/css', 'js' => 'text/javascript');
$content_type  = null;
$content = '';

// get settings and files to minify
// options are:
//   
$screen = isset( $_GET[ 'screen' ] ) ? TRUE : FALSE;
$files  = isset( $_GET[ 'f' ] ) ? $_GET[ 'f' ] : NULL;
$files  = explode( ',', $files );
$file_ext = null;

foreach ($files as $file)
{
	$inf = pathinfo($file);
	
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
	
	// avoid minify it again ...
	$minified[] = $file;
			
	$content .= "/* File: {$file} */".PHP_EOL.PHP_EOL;
	$content .= minify( $file ).PHP_EOL.PHP_EOL;	
	
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