<?php
session_start();
if ($_GET['logout']==1) session_destroy();

require ('../gupa.php');

// Create our Application instance (replace this with your Application ID, OAuth Consumer Key and OAuth Consumer Secret).
$gupa = new GUPA(
			$app_id=YOUR APPLICATION ID,
			$key='YOUR OAUTH CONSUMER KEY',
			$secret='YOUR OAUTH CONSUMER SECRET'
			);
			
?>
<html>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <head>
    <title>gupa-php</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      hr{
      	width:60%;
      	text-align: left;/*this will align it for IE*/
		margin: 0 auto 0 0; /*this will align it left for Mozilla*/
      }
    </style>
  </head>
  <body>
<?php
	//check whether the session is exist or not
	if (!session_is_registered('is_logged')){
		if (isset($_SESSION['request_token_secret']) && !empty($_GET['oauth_token'])){
			//get new access token if the user authorized this application
			$access_token_json=$gupa->getAccessToken($_GET['oauth_token'],$_SESSION['request_token_secret']);
			$access_token=json_decode($access_token_json);
			//check errors
			if ($access_token==NULL) die($access_token_json);
			if ($access_token->error_code!=0) die('Token Service Error :'.$access_token->error_code);
			//clear saved session variable
			session_unset();
			//save new tokens
			$_SESSION['token_secret']=$access_token->oauth_token_secret;	
			$_SESSION['token']=$access_token->oauth_token;
			$_SESSION['is_logged']=TRUE;	
		}else{
			//get request token to build url for authorization
			$request_token_json=$gupa->getRequestToken();
			$request_token=json_decode($request_token_json);
			//check errors
			if ($request_token==NULL) die($request_token_json);
			if ($request_token->error_code!=0) die('Token Service Error :'.$request_token->error_code);
			//save request token secret to get access token
			$_SESSION['request_token_secret']=$request_token->oauth_token_secret;
?>
			Uygulamayı kullanabilmeniz için yetki vermeniz gerekiyor, <a href="http://gupa.geomatikuygulamalar.com/v1/oauth/authorize?oauth_token=<?php echo $request_token->oauth_token?>&callback_url=http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']; ?>">bu uygulamayı yetkilendirmek için tıklayın.</a>
<?php
		}
	}
	
	//if session is exsists get user info
	if (session_is_registered('is_logged')){
		//get user information to store or show
		$user_json=$gupa->api('/user/get_info/',array(),array($_SESSION['token'],$_SESSION['token_secret']));
		$user=json_decode($user_json);
		if ($user==NULL) die($user_json);
		if ($user->error_code!=0) die('User Service Error :'.$user->error_code);
		#debug var_dump($user);
?>
	<h1>Merhaba <?php echo $user->first_name.' '.$user->last_name; ?>;</h1>
	<hr>
	<h3>1. Temel Ödev Hesabı</h3>
	<form action="calculate.php?method=coord" method="POST">
	<b>İlk Nokta (Lokal Koordinat Sistemi):</b>
	<br/><br/>
	X: <input type="text" name="X_1" size="20"> (metre) | Y: <input type="text" name="Y_1" size="20"> (metre)
	<br/><br/>
	<b>Uzaklık:</b>
	<br/><br/>
	<input type="text" name="distance" size="20"> (metre)
	<br/><br/>
	<b>Açıklık Açısı:</b>
	<br/><br/>
	<input type="text" name="azimuth" size="20"> (grad)
	<br/><br/>
	<input type="submit" value="Hesapla"> (Arananlar: İkinci Noktanın Koordinatları)
	</form>
	<hr>
	<h3>2. Temel Ödev Hesabı</h3>
	<form action="calculate.php?method=azmt_dist" method="POST">
	<b>İlk Nokta (Lokal Koordinat Sistemi):</b>
	<br/><br/>
	X: <input type="text" name="X_1" size="20"> (metre) | Y: <input type="text" name="Y_1" size="20"> (metre)
	<br/><br/>
	<b>İkinci Nokta (Lokal Koordinat Sistemi):</b>
	<br/><br/>
	X: <input type="text" name="X_2" size="20"> (metre) | Y: <input type="text" name="X_2" size="20"> (metre)
	<br/><br/>
	<input type="submit" value="Hesapla"> (Arananlar: Açıklık Açısı, Uzaklık)
	</form>
	<hr>
	<a href="index.php?logout=1">Çıkış</a>
<?php 
	}
?>
  </body>
</html>
