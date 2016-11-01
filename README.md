# Minifyr

[![Latest Version](https://img.shields.io/github/release/rogeriotaques/minifyr.svg)](https://github.com/rogeriotaques/minifyr/releases)

Minifies and group CSS or JS scripts.

If you're interested on save bandwith and reduce the load time of your website or application, then, **Minifyr** is good for you.

From version 1.6 it supports external files and prevent double minification on already minified files.

From version 2.0 it was fully rewriten from a script to a class, which has a more elegant code and is much more flexible with new features.

See the examples below:

   [Not minified](http://awin.com.br/minifyr/sample.css) |
   [Minified](http://awin.com.br/minifyr/sample.php?f=sample.css&screen) |
   [Minified (forcing download)](http://awin.com.br/minifyr/sample.php?f=sample.css) |
   [Debug mode](http://awin.com.br/minifyr/sample.php?f=sample.css&screen&debug)

## How to use

Fork (or download) this project; Copy and paste the "minifyr.php" file into any folder from your project. This's the class.

For example:

```bash
/ (project root directory)
/ classes/minifyr.php
/ ...
```

Now create the script that is gonna use it for minify the resources you need. As an example, create a file called _min.php_ in the 
root folder of your project, like below:

```bash
/ (project root directory)
/ classes/minifyr.php
/ min.php
/ ...
```

So, you can use the following code to make it happen:

```php
require_once('classes/minifyr.php');

// get settings and files to minify
// options are:
//   f			- Required. File or comma separated file list
//	 screen	- Optional. Void. Forces the download of minified file.
// 	 debug	- Optional. Void. When given, skip minification.
// 
// @use http://domain.tld/min.php?f=assets/my.css[&screen[&debug]]

$debug  = isset( $_GET[ 'debug' ] ) ? TRUE : FALSE;
$screen = isset( $_GET[ 'screen' ] ) ? TRUE : FALSE;
$files  = isset( $_GET[ 'f' ] ) ? $_GET[ 'f' ] : NULL;

$m = new RT\Minifyr($debug, $screen);
$m->files( explode(',', $files) )
  ->compression(true)   // can be true/false. enables the gzip compression 
  ->cache(true)         // can be true/false. enables header for caching 
  ->uglify(true)        // can be true/false. uglify js codes
  ->expires('...')      // a string that defines the expiration date
  ->charset('...')      // the charset. default is utf-8
  ->files([])           // an array of strings containing files paths
  ->file('...')         // when only one file, a string with file path 
  ->render(false);      // renders the output. 
                        // if a true boolean is given, returns the output as string.

```

Now, everything you have to do is call it in your HTML file:

```html
<link type="text/css" media="all" href="min.php?f=path/to/css/file.css" />
```

That's it. Easy and simple. A piece of cake! :)

## Options

These are the options you can pass:

| Option    | Sample | Description |
| --------- | ------ | ----------  |
| f      | `min.php?f=file-path.css` | It's the file to be minified. * |
| screen | `min.php?screen&f=...`    | It's the way to render the content on browser instead return it as a file. |
| debug  | `min.php?debug&f=...`     | It's a way to don't minify the content. That helps you for debug your codes. |

### Advanced usage for:

#### Option `f` : `string`

You can also pass a list of files. In this case, all files will be loaded and will be returned minified as a unique file. This technique is interesting to reduces the number of calls you make to your server.
To pass a list of files, you should give file names separated by comma (,):

E.g:
```
min.php?f=assets/css/my-css-file-1.css,assets/css/my-css-file-2.css,...
```

You can also load external resources.
To do that, just pass the file with a prefix: `external|`.

E.g:
```
min.php?f=external|code.jquery.com/jquery-2.1.1.min.js[, ...]
```

