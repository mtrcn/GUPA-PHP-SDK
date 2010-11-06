GUPA PHP SDK
================

This repository contains the open source PHP SDK that allows you to utilize the
above on your website. Except as otherwise noted, the GUPA PHP SDK
is licensed under the Apache Licence, Version 2.0
(http://www.apache.org/licenses/LICENSE-2.0.html)


Usage
-----

Both the [client][client] and the [module][module] are a good place to start. The minimal you'll need to
have is:

    <?php

    require '../gupa.php';

    $gupa = new GUPA(
			$app_id=YOUR APPLICATION ID,
			$key='YOUR OAUTH CONSUMER KEY',
			$secret='YOUR OAUTH CONSUMER SECRET'
			);
			

To make [API][API] calls:

    $gupa->api('/user/get_info',array(),array('token'=>$token, 'token_secret'=>$token_secret));


[client]: http://github.com/mtrcn/GUPA-PHP-SDK/blob/master/client/index.php
[module]: http://github.com/mtrcn/GUPA-PHP-SDK/blob/master/module/index.php
[API]: http://developers.facebook.com/docs/api


Documentation
--------
Our [wiki] can help you develop your own application with it's rich content about GUPA APIs.

[wiki]: http://www.geomatikuygulamalar.com/wiki

Feedback
--------

File bugs or other issues [here][issues].

[issues]: http://github.com/facebook/GUPA-PHP-SDK/issues
