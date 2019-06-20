<link rel="stylesheet" href="<?php echo $_REQUEST['path_base']?>sites/all/themes/assets/css/lightbox.min.css">
<script src="<?php echo $_REQUEST['path_base']?>sites/all/themes/assets/scripts/lightbox-plus-jquery.min.js"></script>
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
$cl2->SetMatchMode(SPH_MATCH_EXTENDED);
$cl2->SetRankingMode ( SPH_RANK_SPH04 );
// Execute the query

if($_REQUEST['lang_name']=='en')
	$q2 = '"' . $cl2->EscapeString($_REQUEST['input']) . '"/1';	
	//$q2 = '"' . $cl2->EscapeString($_REQUEST['input']) . '"/1 @lang en';	

else
	$q2 = '"' . $cl2->EscapeString($_REQUEST['input']) . '"/1';	
	//$q2 = '"' . $cl2->EscapeString($_REQUEST['input']) . '"/1 @lang ar';	

if($_REQUEST['lang_name']=='en')
    $indexer_name = 'drupal_images_search';
  else
    $indexer_name = 'drupal_images_search_arabic';

$searchresults2 = $cl2->Query('@(img_names) '.$q2, $indexer_name );

 print_r($cl2-> GetLastError());

if (!isset($searchresults2["matches"]) )
{
  print("No Result Found");
  exit();
}

$keys = array_keys($searchresults2['matches']);
$count=count($keys);

echo '<div class="gallery-search">';

for ($i = 0; $i < $count; $i++) {
    $name = $searchresults2['matches'][$keys[$i]]['attrs']['img_names'];  
     $dir = $searchresults2['matches'][$keys[$i]]['attrs']['dir'];
     //print $src =  $dir .$name ;
   $src =  $_REQUEST['path_base'].$dir .$name ;
  
     $alt = substr($name, 0 , -4);

  // echo '<div class="col-md-4 col-xl-4 media"><img class="myImg" id="'.$keys[$i].'" src="'.$src.'" onclick="image(this)" alt="'.$alt.'" ></div>';
 //echo '<div class="col-md-4 col-xl-4 media"><a href=class="example-image-link" data-lightbox="example-set"> <img class="myImg" id="'.$keys[$i].'" src="'.$src.'" alt="'.$alt.'" ></a></div>';  
  echo'<div class="col-md-4 col-xl-4 media"><a class="example-image-link" href="'.$src.'" data-lightbox="example-set" data-title="'.$name.'"><img class="example-image"  id="'.$keys[$i].'" src="'.$src.'" alt="'.$alt.'"  /></a></div>';

    
} 
echo '</div>';
echo '<!-- The Modal -->
<div id="SearchImgModal" class="search-modal">
  <span class="close">&times;</span>
  <img class="search-modal-content" id="img01">
  <div id="caption"></div>
</div>' ;
?>
