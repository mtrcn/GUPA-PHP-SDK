<?php
session_start();

require ('../gupa.php');

// Create our Application instance (replace this with your Application ID, OAuth Consumer Key and OAuth Consumer Secret).
$gupa = new GUPA(
			$app_id=6476653890,
			$key='CkPR2iI1i0Yzo4lOJA3Bk8OiFOPJGcOf',
			$secret='Pr0H737pZcoerrvxTRG7m4FmiaF5FKpE'
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
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
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
	//check whether the session was created or not
	if (!session_is_registered('is_logged')){
		$loginURL=$gupa->getLoginUrl();
?>
		Oturum açmanız gerekiyor, <a href="<?php echo $loginURL; ?>">giriş için tıklayın.</a>
<?php
	}else{
		$method=$_GET['method'];
		if (!in_array($method, array('coord','azmt_dist'))) die('Invalid Calculation Method');
		
		if($method=='coord'){
			$X_1=floatval($_POST['X_1']);
			$Y_1=floatval($_POST['Y_1']);
			$distance=floatval($_POST['distance']);
			$azimuth=floatval($_POST['azimuth']);
			$params=array('point'=>'POINT('.$X_1.' '.$Y_1.')',
						  'distance'=>$distance,
						  'azimuth'=>$azimuth);
			$coord=json_decode($gupa->api('/basic_calc/coord/',$params,NULL));
			if ($coord==NULL) die('Unknown Service Error');
			if ($coord->error_code!=0) die('Service Error :'.$coord->error_code);
			#debug var_dump($coord);
?>
			<h3>1. Temel Ödev Hesabı Sonucu</h3>
			İkinci Nokta : <?php echo $coord->point; ?> (<i>Biçim: <a href="http://www.geomatikuygulamalar.com/wiki/term_wkt">WKT</a></i>)
<?php
		}else{
			$X_1=floatval($_POST['X_1']);
			$Y_1=floatval($_POST['Y_1']);
			$X_2=floatval($_POST['X_2']);
			$Y_2=floatval($_POST['Y_2']);
			$params=array('point'=>'MULTIPOINT('.$X_1.' '.$Y_1.','.$X_2.' '.$Y_2.')');
			$azmt_dist=json_decode($gupa->api('/basic_calc/azmt_dist/',$params,NULL));
			if ($azmt_dist==NULL) die('Unknown Service Error');
			if ($azmt_dist->error_code!=0) die('Service Error :'.$azmt_dist->error_code);
			#debug var_dump($coord); 
?>
		<h3>2. Temel Ödev Hesabı Sonucu</h3>
		Uzaklık : <?php echo $azmt_dist->distance; ?> metre<br/>
		Açıklık Açısı : <?php echo $azmt_dist->azimuth; ?> grad
<?php
		}
	}
?>
  </body>
</html>
