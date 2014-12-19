<!DOCTYPE html><html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Site Closed</title>
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
//else if($_SERVER['HTTP_HOST'] === 'garsies.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'duo-edu.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'cbc.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'midstreamridge.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'midstreamprimary.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'midstreamcollege.boekwinkel.info'){
	if ($_SERVER['HTTP_HOST'] === 'klofies.boekwinkel.info') {
  	 echo '<img src="public/img/construction_klofies.png" class="img">';
	}
	else if($_SERVER['HTTP_HOST'] === 'duo-edu.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'garsies.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'cbc.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'midstreamridge.boekwinkel.info'  || $_SERVER['HTTP_HOST'] === 'midstreamprimary.boekwinkel.info' || $_SERVER['HTTP_HOST'] === 'midstreamcollege.boekwinkel.info'){
	 header('location: http://'.$_SERVER['HTTP_HOST'].'/public/index.php');
}  
	
?>
       <p><h1>WEBSITE CLOSED FOR ORDERS 2015
AT 12H00 27-11-2014</h1></br>

<h1>WEBSITE WILL REOPEN AGAIN FOR ORDERS
05-12-2014</br>

ORDERS PLACED ON OR BEFORE THE 27-11-2014
AT 12HOO WILL BE READY FOR COLLECTION
ON THE 2ND & 3RD OF DECEMBER 2014 
BETWEEN 08HOO AND 18H00 AT
MIDSTREAM RIDGE PRIMARY SCHOOL</br>
</br></br>
WEBTUISTE GESLUIT VIR BESTELLINGS 2015
12H00 27-11-2014</br>

WEBTUISTE WORD HEROPEN VIR
BESTELLINGS 05-12-2014</br>
</br>
BESTELLINGS WAT VOOR OF OP 27-11-2014 VOOR 
12HOO GEPLAAS IS KAN AFGEHAAL WORD 2 EN 3 DESEMBER 2014 BY MIDSTREAM RIDGE LAERSKOOL
TUSSEN 08H00 EN 18HOO</br></h1>
</p>
    </div>
</body>
</html>
