# Minifyr 1.8 beta

A PHP script that minify and group CSS and JS scripts.

You may be interested on save bandwith and reduce the load time of your website or application, then, **Minifyr** is good for you.

From version 1.6 it supports external files and prevent double minification on already minified files.

Many minor bugfixes from this version.

See the examples below:

   [Not minified](http://awin.com.br/assets/css/icomoon.css) |
   [Minified](http://awin.com.br/minifyr.php?f=assets/css/icomoon.css&screen) |
   [Minified (forcing download)](http://awin.com.br/minifyr.php?f=assets/css/icomoon.css&screen) |
   [Debug mode](http://awin.com.br/minifyr.php?f=assets/css/icomoon.css&screen&debug)

## How to use

Download this project, copy and paste the "minifyr.php" file into any folder of your project. I strongly
suggest that you paste it in root directory, this way it becomes easy to refer.

For example:


```
/ (project root directory)
/minifyr.php
```

Now, everything you have to do is call it in your HTML file:

```
<link type="text/css" media="all" href="minifyr.php?f=path/to/css/file.css" />
```

That's it. Easy and simple. A piece of cake! :)

## Options

There are two options that you can pass via query string:

### f: string

The path of file you'd like to load and minify.


```
minifyr.php?f=assets/css/my-css-file.css
```

You can also pass a list of files. In this case, all files will be loaded and will be returned minified as a unique file. This technique is interesting to reduces the number of calls you make to your server.

To pass a list of files, you should give file names separated by comma (,):

```
minifyr.php?f=assets/css/my-css-file-1.css,assets/css/my-css-file-2.css,...
```

Whenever it's necessary to load external files, you can just pass the file with a prefix ```external|```.

```
minifyr.php?f=external|code.jquery.com/jquery-2.1.1.min.js[, ...]
```

### screen: void

By default the minified result is returned as a file for your browser.

In case you'd like to get the minified result as string, you should use the *screen* option.

Just add it in the end of your query string:


```
minifyr.php?f=assets/css/my-css-file.css&screen
```

### debug: void

By default all given files are minifies and grouped into one unique file, but, sometimes it's necessary to debug problems with your code and debug mode is to attend it. It's really usefull when minifying javascript files.

In case you'd like to get the result on debug mode, you should use the *debug* option.

Just add it in the end of your query string:


```
minifyr.php?f=assets/css/my-css-file.css&debug
```

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/rogeriotaques/minifyr/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
