<?php

/***********************************************************************
== Author: Florian Bersier
== Organization: Oxford Internet Institute, University of Oxford
== MIT Licence

Copyright (c) 2012 Florian Bersier

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without 
restriction, including without limitation the rights to use, 
copy, modify, merge, publish, distribute, sublicense, and/or 
sell copies of the Software, and to permit persons to whom 
the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall 
be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY 
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH 
THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

== Object: Scrape Data coming from Android Applications Marketplace
== Languages: PHP, JavaScript
== To run on a Local Server
*************************************************************************/

set_time_limit(0);
ob_start(); 
//session_start(); 
$errors=0;
$base = $_SERVER['HTTP_REFERER'];
$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];
$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$date = date('j/m/Y');
$time = date('G:i');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Android App Scraper (Paid)</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<meta http-equiv="Content-Language" content="en" />
<style type="text/css">
body{font-family:Helvetica,Arial,sans-serif;font-size:16px;background-color:#eee}
#wrapper{width:1400px;margin:70px auto 0;padding:2% 0;background-color:#fff;border:1px solid #dadada}
#select{position:absolute;display:block;margin-left:800px;top:15px;width:200px;height:55px;background:#fff;border-radius:10px 10px 0 0;border:1px solid #ccc;border-bottom:0}#select input{cursor:pointer;margin:15px 25px 0;padding:4px 0 4px 16px;text-align:center;font-size:14px;font-weight:bold;color:#333;text-shadow:0 1px 1px #fff;width:150px;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9wCCA8hIkT/HkEAAADeSURBVDjLxZKhCgJBEIa/UzEJimAQwQcwHZi2Huw7CCIXBB/AF7DbRDFZtplNwoIYhC0XTILRbDWZDO7Bcp7InYJ/2p3Z//9nZgd+CaVFK+ubQiK/V1r4H8iR0qLyTmACzB1CVWnRcPJTYBZKc4sDXopLBBSBOtC24StwBjpAM5Tm/iKgtCgDK2DwYQw7oBdKc022sLTkI2BSiCdLDoC1NXwKKC0kMHQctikCB2BjzwEwAijZgFv2+E3po8R9ACziFvwca9N1K+gD5TzL59kZXJwvy4Ja4dv1/7/A13gAI2Ew+pb50OwAAAAASUVORK5CYII=) no-repeat #f1f1f1 4px 4px;}
h1{position:absolute;font-size:22px;top:10px;left:22px;padding:0;}
#ctrl{position:absolute;color:#999;top:20px;display:inline-block;}
table{border:0;margin:0;padding:0;width:100%;}#labels,th{font-size:12px;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-o-user-select:none;user-select:none;}tbody{border:0;margin:0,padding:0}
tbody{font-size:10px;text-align:center}tbody td.non{text-align:left}
#footer{margin:20px 0 10px;font-size:12px;color:#999;text-align:center;text-shadow:0 1px 1px #fff}
</style>
</head>
<body>
<h1>Android Applications Marketplace Scraper</h1>
<div id="wrapper">
<div id="select"><input type="button" value="Select Results" onclick="selectElementContents( document.getElementById('blah') );"></div>
<table id="blah"><tr id="labels"><th>ID</th><th>Label</th><th>Price (£)</th><th>#Users</th><th>#Ratings</th><th>#Reviews</th><th>Ratio</th><th>Grade</th><th>#Pages</th><th>TotChars</th><th>AvgChars</th></tr><tbody>

<?php

//GET URL
$url00 = "https://market.android.com/";
$url0 = "https://market.android.com/details?id=apps_topselling_paid";
//https://market.android.com/details?id=apps_topselling_free&feature=top-free
//https://market.android.com/details?id=apps_topselling_paid&start=24&num=24

//AUTO LOOP DETERMINING THE RANGE OF PAGES TO SCRAPE

foreach(range(0, 480, 24) as $x) {

$frompge = $x;
$pages = 1; //For info only

//MULTI cURL INIT
$mh = curl_multi_init();
$running = null;

//GENERATE URLs ARRAY
$urls = array();

$aa = $url0 . '&start='. $x .'&num=24';
$urls[] = $aa;


foreach ($urls as $name => $url) 
{
        $c[$name]=curl_init($url);
	curl_setopt($c[$name], CURLOPT_HEADER, false);
	curl_setopt($c[$name], CURLOPT_FAILONERROR, true);
	curl_setopt($c[$name], CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($c[$name], CURLOPT_AUTOREFERER, true);
	curl_setopt($c[$name], CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c[$name], CURLOPT_TIMEOUT, 10);
        curl_multi_add_handle ($mh,$c[$name]);
}

// execute all queries simultaneously, and continue when all are complete
do {
curl_multi_exec($mh, $running);
    } while ($running >0);

$html = array();
foreach ($urls as $name => $url) 
{
	$html[]=curl_multi_getcontent($c[$name]);
	curl_multi_remove_handle($mh,$c[$name]);
	curl_close($c[$name]);
}
curl_multi_close($mh);

for ($b = 0; $b <= 0; $b++) {

// Parse the HTML information and return the results.
$dom = new DOMDocument(); 
@$dom->loadHtml($html[$b]);
		
$xpath = new DOMXPath($dom);
$links = $xpath->query("//li/div[@class='snippet snippet-medium']");
$result = array();

foreach ( $links as $item ) {
	$newDom = new DOMDocument;
	$newDom->appendChild($newDom->importNode($item,true));
 
	$xpath = new DOMXPath( $newDom );
        $cleaner = array(" Buy", "UKÂ£", "Rating: ","Above", ")", ",", "Average", "average", " ", "stars", "Below", "(");
        $cleanhref = array("&feature=apps_topselling_paid", "/details?");

//ORDINAL VALUE
	$ord = ($xpath->query("//div[@class='ordinal-value']")->item(0)->nodeValue);

//NAME ADDON       
        $extname = trim($xpath->query("//a[@class='title']")->item(0)->nodeValue);

//GENERATE LABEL 
       $label = str_replace(" ", "", strtolower(ereg_replace("[^A-Za-z0-9 ]", "", $extname)));

//RETRIEVE Link
        $id = str_replace($cleanhref,"",trim($xpath->query("//a[@class='thumbnail']/@href")->item(0)->nodeValue));

//PRICE
	$p = str_replace($cleaner,"",trim($xpath->query("//span[@class='buy-button-price']")->item(0)->nodeValue));
			if ($p == "Install"){$p = 0;}

//RETRIEVE STARS
	$grade = str_replace($cleaner,"",trim($xpath->query("//div[contains(@class, 'ratings goog-inline-block')]/@title")->item(0)->nodeValue));

/////////////////////////////////////////////LOOP REVIEWS and USERS

//GENERATE URLs ARRAY FOR REVIEWS SCRAPING
$mh2 = curl_multi_init();

//////
$c0 = curl_init('https://market.android.com/details?'.$id);

$options0 = array(
	CURLOPT_HEADER => false,
	CURLOPT_FAILONERROR => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_AUTOREFERER => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT => 10,
);

curl_setopt_array($c0, $options0);
curl_multi_add_handle($mh2,$c0);

//////
$c1 = curl_init('https://market.android.com/getreviews?'. $id .'&reviewSortOrder=2&reviewType=1&pageNum=0');

$fields1 = http_build_query(array( 'req' => '{xhr:1}', ));

$options1 = array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 10,
	CURLOPT_POSTFIELDS => $fields1,
);


curl_setopt_array($c1, $options1);
curl_multi_add_handle($mh2,$c1);

//////////////////cURL
$active = null;

do {
curl_multi_exec($mh2, $active);
    } while ($active >0);

$htmlb=curl_multi_getcontent($c0);
$data2=curl_multi_getcontent($c1);

curl_multi_remove_handle($mh2, $c0);
curl_multi_remove_handle($mh2, $c1);
curl_multi_close($mh2);
//////////////////END cURL

//Get Number of ratings and number of downloads (category)
$domb = new DOMDocument(); 
		@$domb->loadHtml($htmlb);
		
		$xpath2 = new DOMXPath($domb);

$links2= $xpath2->query("//div[@class='doc-metadata']");

foreach ( $links2 as $item ) {
	$newDom2 = new DOMDocument;
	$newDom2->appendChild($newDom2->importNode($item,true));
 
	$xpath3 = new DOMXPath( $newDom2 );
	$cleaner2 = array("last 30 days", " ", ".", ",");

	$nbratings = str_replace($cleaner2,"",trim($xpath3->query("//span[@itemprop='ratingCount']")->item(0)->nodeValue));
	$nbusers = str_replace($cleaner2,"",trim($xpath3->query("//dd[@itemprop='numDownloads']")->item(0)->nodeValue));
} 

//Reviews
//Get the number of pages

$htmlc = str_replace("})","</span>",str_replace("\"numPages\":","<span class=\"numPages\">",str_replace("]}","</div>",str_replace("{\"status\":\"OK\",","<div class=\"set\">",str_replace("u003C","<",str_replace("\\","",$data2))))));


$domc = new DOMDocument(); 
		@$domc->loadHtml($htmlc);
		
		$xpath4 = new DOMXPath($domc);

$links4= $xpath4->query("//div[@class='set']");

foreach ( $links4 as $item ) {
	$newDom3 = new DOMDocument;
	$newDom3->appendChild($newDom3->importNode($item,true));
        $cle = array("}", ")", " ");
	$xpath4 = new DOMXPath( $newDom3 );
	$nbpages = str_replace($cle,"",trim($xpath4->query("//span[@class='numPages']")->item(0)->nodeValue));
	//Max is 48
}

//////////////////////////////// ALGORITHM PAGES SCRAPING

if ($nbpages > 11){
	$scraper = 9; 		//Nb of pages to scrape on Android Marketplace
}

else {	
	$scraper = $nbpages-1;  //Nb of pages to scrape on Android Marketplace
}

/////////////////////// Parallel cURL Process

$mh3 = curl_multi_init();

$urls = array();

for ($z = 0; $z <= $scraper; $z++){
	$zz = 'https://market.android.com/getreviews?'. $id .'&reviewSortOrder=2&reviewType=1&pageNum='. $z;
	$urls[] = $zz;
}


$fields = http_build_query(array( 'req' => '{xhr:1}', ));

foreach ($urls as $name => $url) 
{
        $cs[$name]=curl_init($url);
	curl_setopt($cs[$name], CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cs[$name], URLOPT_SSL_VERIFYPEER, false);
	curl_setopt($cs[$name], CURLOPT_POST, true);
	curl_setopt($cs[$name], CURLOPT_TIMEOUT, 10);
	curl_setopt($cs[$name], CURLOPT_POSTFIELDS, $fields);
        curl_multi_add_handle ($mh3,$cs[$name]);
}

$active2 = null;

do {
curl_multi_exec($mh3, $active2);
    } while ($active2 >0);

$htmlz = array();

foreach ($urls as $name => $url) 
{
	$htmlz[]=str_replace("})","</span>",str_replace("\"numPages\":","<span class=\"numPages\">",str_replace("]}","</div>",str_replace("{\"status\":\"OK\",","<div class=\"set\">",str_replace("u003C","<",str_replace("\\","",curl_multi_getcontent($cs[$name])))))));
	curl_multi_remove_handle($mh3,$cs[$name]);
	curl_close($cs[$name]);
}

curl_multi_close($mh3);

///////////////////////////END cURL REVIEWS

//print_r($htmlz,false);

//INITIALIZATION
$add = 0;
	

// Parse the HTML information and return the results.
for ($e = 0; $e <= $scraper; $e++) {
		$doms = new DOMDocument(); 
		@$doms->loadHtml($htmlz[$e]);
		
		$xpaths = new DOMXPath($doms);

$linkss = $xpaths->query("//div[@class='set']/div[@class='doc-review']");

$return = array();

foreach ( $linkss as $item ) {
	$newDoms = new DOMDocument;
	$newDoms->appendChild($newDoms->importNode($item,true));
 
	$xpaths = new DOMXPath( $newDoms ); 
	$review = trim($xpaths->query("//p[@class='review-text']")->item(0)->nodeValue); 	//REVIEW'S TEXT
	$review2 = trim($xpaths->query("//h4[@class='review-title']")->item(0)->nodeValue);	//REVIEW'S TITLE
	$return[] = array($review,$review2);
} 

// REVIEWS ARRAY
//print_r($return,false);

	$count = sizeof($return);		//SIZE OF ARRAY
	$nbreviews = ($scraper*10) + $count; 	//COMPUTE THE NUMBER OF REVIEWS
	$return = print_r($return,true);
	$cleanreviews = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", " ","[0]","Array","(",")","=","&gt;","[","]",">");
	$return = str_replace($cleanreviews, "", $return);
	$strlen = strlen($return);
	$add += $strlen;

	$final = $add/$nbreviews;
	$ratio = $nbreviews/$nbratings;

}
////////////////////////////////////////////END LOOP REVIEWS and USERS

/////////////////////////////////////////////////PUT VALUES TOGETHER

$result[] = array($ord,$label,$p,$nbusers,$nbratings,$nbreviews,$ratio,$grade,$nbpages,$add,$final);

}//END FOREACH

//print_r($result,false);

//DISPLAY RESULTS

for ($z = 0; $z <= 24; $z++) {

echo "<tr><td class=\"non\">" .$result[$z][0] . "</td><td class=\"non\">" .$result[$z][1] . "</td><td>" .$result[$z][2] . "</td><td>" .$result[$z][3] . "</td><td>" .$result[$z][4] . "</td><td>" .$result[$z][5] . "</td><td>" .$result[$z][6] . "</td><td>" .$result[$z][7] . "</td><td>" .$result[$z][8] . "</td><td>" .$result[$z][9] . "</td><td>" .$result[$z][10] . "</td></tr>";
ob_flush();
flush();
}

}
}//END FOREACH

?>
</tbody></table>
</div><!--#wrapper-->

<!-- DO NOT REMOVE THIS -->
<div id="footer">
&copy; 2012 <a href="http://www.florianbersier.com" target="_blank">Florian Bersier</a> - Oxford Internet Institute, University of Oxford
</div>

<script type="text/javascript">
//JS Script to select results within the table

 function selectElementContents(el) {
        var body = document.body, range, sel;
        if (body.createTextRange) {
            range = body.createTextRange();
            range.moveToElementText(el);
            range.select();
        } else if (document.createRange && window.getSelection) {
            range = document.createRange();
            range.selectNodeContents(el);
            sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }
</script>
</body>
</html>