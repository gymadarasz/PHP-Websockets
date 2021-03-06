PHP WebSockets
==============

A WebSockets server written in PHP.
-----------------------------------

This project provides the functionality of an RFC-6455 (or Version 13) WebSockets server.  It can be used as a stand-alone server, or as the back-end of a normal HTTP server that is WebSockets aware.

In order to use PHP WebSockets, you must have the ability to arbitrarilly execute scripts, which almost always means having shell access to your server, at a minimum.  It is strongly encouraged that you have the ability to configure your machine's HTTP server.  It is strongly discouraged to allow arbitrary execution of scripts from a web interface, as this is a major security hole.

To use:

Do not place the files in your web server's document root -- they are not intended to be ran through a web browser or otherwise directly accessible to the world.  They are intended to be ran through PHP's Command Line Interface (CLI).

The main class, `WebSocketServer`, is intended to be inherited by your class, and the methods `connected`, `closed`, and `process` should be overridden.  In fact, they are abstract, so they _must_ be overridden.

Future plans include allowing child processes forked from the controlling daemon to support broadcasts and to relay data from one socket in a child process to another socket in a separate child proccess.

Browser Support
---------------

Broswer Name        Earliest Version

Google Chrome       16

Mozilla Firefox     11

Internet Explorer   10

Safari              6

Opera               12.10

Android Browser     4.4

Note: Current browser support is available at http://en.wikipedia.org/wiki/WebSocket#Browser_support under the RFC-6455 row.

For Support
-----------

Right now, the only support available is in the Github Issues ( https://github.com/ghedipunk/PHP-Websockets/issues ).  Once I reach my $250/mo Patreon reward level, I'll be able to maintain support forums for non-core code issues.  If you'd like to support the project, and bring these forums closer to reality, you can do so at https://www.patreon.com/ghedipunk .

Install:
--------
`composer require gymadarasz/phpwebsockets`

Packagist: 
https://packagist.org/packages/gymadarasz/phpwebsockets

Example
-------
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use gymadarasz\phpwebsocket\WebSocketServer;
use gymadarasz\phpwebsocket\WebSocketUserInterface;

class echoServer extends WebSocketServer {
  //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.
  
  protected function process (WebSocketUserInterface $user, $message) {
    $this->send($user,$message);
  }
  
  protected function connected (WebSocketUserInterface $user) {
    // Do nothing: This is just an echo server, there's no need to track the user.
    // However, if we did care about the users, we would probably have a cookie to
    // parse at this step, would be looking them up in permanent storage, etc.
  }
  
  protected function closed (WebSocketUserInterface $user) {
    // Do nothing: This is where cleanup would go, in case the user had any sort of
    // open files or other objects associated with them.  This runs after the socket 
    // has been closed, so there is no need to clean up the socket itself here.
  }
}

$echo = new echoServer("0.0.0.0","9000");

try {
  $echo->run();
}
catch (Exception $e) {
  $echo->stdout($e->getMessage());
}

```

Client HTML and JS
------------------

```html
<html><head><title>WebSocket</title>
        <style type="text/css">
            html,body {
                font:normal 0.9em arial,helvetica;
            }
            #log {
                width:600px; 
                height:300px; 
                border:1px solid #7F9DB9; 
                overflow:auto;
            }
            #msg {
                width:400px;
            }
        </style>
        <script type="text/javascript">
            var socket;
            function init() {
              var host = "ws://127.0.0.1:9000/echobot"; // SET THIS TO YOUR SERVER
              try {
                socket = new WebSocket(host);
                log('WebSocket - status ' + socket.readyState);
                socket.onopen = function (msg) {
                  log("Welcome - status " + this.readyState);
                };
                socket.onmessage = function (msg) {
                  log("Received: " + msg.data);
                };
                socket.onclose = function (msg) {
                  log("Disconnected - status " + this.readyState);
                };
              } catch (ex) {
                log(ex);
              }
              $("msg").focus();
            }
            function send() {
              var txt, msg;
              txt = $("msg");
              msg = txt.value;
              if (!msg) {
                alert("Message can not be empty");
                return;
              }
              txt.value = "";
              txt.focus();
              try {
                socket.send(msg);
                log('Sent: ' + msg);
              } catch (ex) {
                log(ex);
              }
            }
            function quit() {
              if (socket != null) {
                log("Goodbye!");
                socket.close();
                socket = null;
              }
            }
            function reconnect() {
              quit();
              init();
            }
        // Utilities
            function $(id) {
                return document.getElementById(id);
            }
            function log(msg) {
                $("log").innerHTML += "<br>" + msg;
            }
            function onkey(event) {
                if (event.keyCode == 13) {
                    send();
                }
            }
        </script>

    </head>
    <body onload="init()">
        <h3>WebSocket v2.00</h3>
        <div id="log"></div>
        <input id="msg" type="textbox" onkeypress="onkey(event)"/>
        <button onclick="send()">Send</button>
        <button onclick="quit()">Quit</button>
        <button onclick="reconnect()">Reconnect</button>
    </body>
</html>
```
