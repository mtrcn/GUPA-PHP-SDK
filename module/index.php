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
	if (!empty($_GET['license']) && ){ //license code is not empty
		//check if license query parameter is valid
		$license_code = $gupa->validateQueryLicenseCode();
		
		//if license code is invalid and session is not exist
		if ($license_code==FALSE && !session_is_registered('is_logged')){
			//create login url
			$loginURL=$gupa->getLoginUrl();
			
			//print login message
			echo 'Oturum açmanız gerekiyor, <a href="'.$loginURL.'">giriş için tıklayın.</a>';
		}
	}else{
			//get user's OAuth parameters with the license code		
			$license_token_json=$gupa->api('/license/get_token',array('license'=>$license_code),NULL);
			$license_token=json_decode($license_token_json);
			if ($license_token==NULL) die($license_token_json);
			if ($license_token->error_code!=0) die('License Service Error :'.$license_token->error_code);
			#debug var_dump($license_token);
			
			//save OAuth paramters to create a new session
			$_SESSION['token']=$license_token->token;
			$_SESSION['token_secret']=$license_token->token_secret;
			$_SESSION['is_logged']=TRUE;
	}
	
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
