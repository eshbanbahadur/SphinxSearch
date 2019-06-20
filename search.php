<?php 

if($_SERVER['HTTP_HOST']=='localhost')
{	
	include 'C:\Sphinx\api\sphinxapi.php';
	//include 'C:\Sphinx\share\doc\api\sphinxapi.php';
}
else
{	
	include '/etc/sphinx/sphinxapi.php';
}


// Build search query
$cl = new SphinxClient();

if($_SERVER['HTTP_HOST']=='localhost')
{	
	$cl->SetServer( "localhost", 3312 );
	//$cl->SetServer( "localhost", 9312 );
}

else
{
	$cl->SetServer( "localhost", 9312 );
}

$cl->SetLimits(0, 250);
$cl->SetMatchMode( SPH_MATCH_EXTENDED  );
$cl->SetRankingMode ( SPH_RANK_SPH04 );


// Execute the query
//$q  = '"' . $cl->EscapeString($_REQUEST['input']) . '"/1';


if($_REQUEST['lang_name']=='en')
	$q = '"' . $cl->EscapeString($_REQUEST['input']) . '"/1 @language en';	
else
	$q = '"' . $cl->EscapeString($_REQUEST['input']) . '"/1 @language ar';	

print $q
$searchresults = $cl->Query('lukas @language en', 'drupal_search' );
print_r($cl-> GetLastError());

if (!isset($searchresults["matches"]) ) {
	   // echo config_pages_render_field('site_labels','field_no_result_found');
	print("No Result Found");
	exit();
}

include('db.php');
$prot = $_SERVER['HTTP_HOST'];  
$path_val=$_REQUEST['path_base']; 
$protocol_val = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';


$arrTitleAndBundle = array();
foreach($searchresults["matches"] as $match)
{
  ///$entry = array("title" => "", "bundle" => "");
  $entry = array("title" => "");
  if(isset($match["attrs"]))
  {
    "<br>".$entry["title"] = (isset($match["attrs"]["title"]) ? $match["attrs"]["title"] : "");	
	$entity["entity_id"]  = (isset($match["attrs"]["entity_id"]) ? $match["attrs"]["entity_id"] : "");
	
						// Getting Link //
	$qry = "select * from url_alias where source like 'node/$entity[entity_id]'";
	$rst = mysqli_query($conn,$qry) or die(mysqli_error());
	$rst_fieds = mysqli_fetch_array($rst);
	$url_alias_link = $rst_fieds['alias'];
	   
	$entry["bundle"] = (isset($match["attrs"]["field_body_value"]) ? $match["attrs"]["field_body_value"] : "")."<br>";
	$entry["bundle2"] = strip_tags(html_entity_decode($entry["bundle"]));
	
		print "<div class='search-web'><a href='$protocol_val$prot$path_val$_REQUEST[lang_name]/$url_alias_link' target=_blank><h5 class='search-title color'>".$entry["title"]."</h5><span class='text hover-color'>".substr($entry["bundle2"], 0, 1000)."...........<br><br></span></a></div>";
	
  }
  
  $arrTitleAndBundle[] = $entry;
}
?>
