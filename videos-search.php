<?php 

$q=$_REQUEST['input'];
if (empty($q)) {
  print("No Result Found");
  exit();
}
if($_SERVER['HTTP_HOST']=='localhost')
{	
	include 'C:\sphinx2210\api\sphinxapi.php';
}
else if($_SERVER['HTTP_HOST']=='MYSERVER.com')
{
	include '/etc/sphinx/sphinxapi.php';
}
else
{	
	include '/etc/sphinx/sphinxapi.php';
}


// Build search query

$cl2 = new SphinxClient();

if($_SERVER['HTTP_HOST']=='localhost')
{	
	$cl2->SetServer( "localhost", 3312 );
}

else
{
	$cl2->SetServer( "localhost", 9312 );
}
$cl2->SetLimits(0, 250);
$cl2->SetMatchMode( SPH_MATCH_EXTENDED  );
$cl2->SetRankingMode ( SPH_RANK_SPH04 );
// Execute the query
$q2 = '"' . $cl2->EscapeString($_REQUEST['input']) . '"/1';
if($_REQUEST['lang_name']=='en')
    $indexer_name = 'drupal_videos_search';
  else
    $indexer_name = 'drupal_videos_search_arabic';

$searchresults2 = $cl2->Query($q2, $indexer_name);


if (!isset($searchresults2["matches"]) ) {
	print("No Result Found");
	exit();
}

$prot = $_SERVER['HTTP_HOST'];  
$path_val=$_REQUEST['path_base']; 
$protocol_val = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';

$arrVideo = array();
foreach($searchresults2["matches"] as $match2)
{
  $entry2 = array("title_video" => "", "description_video" => "", "link_video" => "", "thumbnail_video" => "");
  if(isset($match2["attrs"]))
  {
    
    $entry2["description_video"] = (isset($match2["attrs"]["description"]) ? $match2["attrs"]["description"] : "");
	$entry2["link_video"] = (isset($match2["attrs"]) ? $match2["attrs"]["link"]: "");
	$entry2["title_video"] = (isset($match2["attrs"]["title"]) ? $match2["attrs"]["title"] : "");
	$entry2["thumbnail_video"] = (isset($match2["attrs"]["thumbnail_path"]) ? $match2["attrs"]["thumbnail_path"] : "");
	
	echo "<div class='single-video-result'>
			<a onclick='videoIframe(this)' src='$entry2[link_video]' target=_blank >
				<img src='$entry2[thumbnail_video]' width=50px height=50px > <p>".$entry2["title_video"]."</p></a></div>";   
	// print($entry2["link_video"]) ;

  }
  $arrTitleAndBundle2[] = $entry2;
}


echo '<div id="videoIframe" class="search-modal">
  <span class="close searchclose" id="searchclose"><i class="icon-close"></i></span>
  <div id="iframe"></div>
</div>' ;
?>

