# Minifyr 1.0 beta

A PHP script that minify CSS and JS scripts.

You should use it in order to save bandwidth and accelerate load time for your site or web-app.

   [Not minified sample](http://awin.com.br/assets/css/icomoon.css) | 
   [Minified sample](http://awin.com.br/minifyr.php?f=assets/css/icomoon.css)

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

### screen: void

By default the minified result is returned as a file for your browser.

In case you'd like to get the minified result as string, you should use the *screen* option.

Just add it in the end of your query string:


```

   minifyr.php?f=assets/css/my-css-file.css&screen

```
