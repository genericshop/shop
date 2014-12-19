<!DOCTYPE html><html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Coming Soon</title>
	<meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    html, body {
    	width: 100%;
    	height: 100%;
    	margin: 0; 
    	padding: 0;
    }
   #mainContent {position:relative;top:0;margin:0;border:0;padding:0;height:100%;overflow:hidden} 
#mainContent img {height:100%;border-style:none;width:100%}

    </style>
</head>
<body>
    <div id="mainContent" align="Center"><?php 
    header('location: http://'.$_SERVER['HTTP_HOST'].'/public/index.php');
    exit();

	if ($_SERVER['HTTP_HOST'] === 'klofies.boekwinkel.info') {
  	 echo '<img src="public/img/construction_klofies.png" class="img">';
	}else if($_SERVER['HTTP_HOST'] === 'garsies.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'duo-edu.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'cbc.boekwinkel.info'){
	 header('location: http://'.$_SERVER['HTTP_HOST'].'/public/index.php');
	} 
	else{
	  echo '<img src="public/img/construction.png" class="img">';
	}
?>
       
    </div>
</body>
</html>