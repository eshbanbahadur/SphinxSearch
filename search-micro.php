<?php 
$q=$_REQUEST['input'];
if (empty($q)) {
	exit();
}
define('DRUPAL_ROOT', dirname(dirname(dirname(dirname(dirname(__DIR__))))));

// Bootstrap to database level for db api
require_once  DRUPAL_ROOT.'/includes/bootstrap.inc';
require_once  DRUPAL_ROOT.'/includes/common.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

if($_SERVER['HTTP_HOST']=='localhost')
{	
	include 'C:\Sphinx\api\sphinxapi.php';	
}
else
{	
	include '/etc/sphinx/sphinxapi.php';
}


// Build search query
$cl_m = new SphinxClient();

if($_SERVER['HTTP_HOST']=='localhost')
{	
	$cl_m->SetServer( "localhost", 3312 );
	//$cl_m->SetServer( "localhost", 9312 );
}

else
{
	$cl_m->SetServer( "localhost", 9312 );
}

$cl_m->SetLimits(0, 250);
$cl_m->SetMatchMode( SPH_MATCH_EXTENDED  );
$cl_m->SetRankingMode ( SPH_RANK_SPH04 );


// Execute the query
//$q  = '"' . $cl_m->EscapeString($_REQUEST['input']) . '"/1';
// $q=$_REQUEST['input'];
$query = '"'.$cl_m->EscapeString($q).'"';





$exact_phrase=true;

if(strpos ($q, '"') === false  && strpos($q[0],"'") === false)
{
    $query = $query.'/1';
    $exact_phrase=false;
}
$q = str_replace(["'","-"], " ", $q);

$word_array = str_word_count($q,1);
$size=str_word_count($q);

if($_REQUEST['lang_name']=='en'){
	//$q =  $query . '@language en';	
	$Missing = "Missing";
	$searchresults = $cl_m->Query($q, 'drupal_search_microsites' );
}
		
else{
	//$q = $query . '@language ar';
	$Missing = "المفقودة";
	$searchresults = $cl_m->Query($q, 'drupal_search_microsites_arabic_new' );


}		

//$searchresults = $cl_m->Query($q, 'drupal_search_microsites_rt' );


print_r($cl_m-> GetLastError());

if (!isset($searchresults["matches"]) ) {
	    ///echo config_pages_render_field('site_labels','field_no_result_found');
	print("No Result Found");
	exit();
}
	

//include('db.php');
$prot = $_SERVER['HTTP_HOST'];  
$path_val=$_REQUEST['path_base']; 
$protocol_val = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
	
	$rst_currpage = db_select('url_alias', 'ua')->fields('ua',array('alias'))->condition('source', 'node/'. $_REQUEST["nid_val"])->execute()->fetchField(); 
	$urlbreak_currpage = explode('/',$rst_currpage);		
	$urlbreak_currpage_val = $urlbreak_currpage[1];	
	

$eb=1;
$lastchar="";
$addfullstop="";
$arrTitleAndBundle = array();

foreach($searchresults["matches"] as $match)
{ 
//print "<br>Loop Counter=".$eb++;
//print "<br>";
  $entry = array("title" => ""); 
  if(isset($match["attrs"]))
  {
	//print "in matching array"."<br>";
    "<br>".$entry["title"] = (isset($match["attrs"]["title"]) ? $match["attrs"]["title"] : "");	
	$entity["entity_id"]  = (isset($match["attrs"]["nid"]) ? $match["attrs"]["nid"] : "");
	
		$rst_microcurrpage = db_select('url_alias', 'ua')->fields('ua',array('alias'))->condition('source', 'node/'. $entity["entity_id"])->execute()->fetchField(); 	
	$urlbreak_micro = explode('/',$rst_microcurrpage);
	
	//$urlbreak_micro = $urlbreak_micro[1];
	$urlbreak_micro_val = $urlbreak_micro[1];	
	


	$match_urls = similar_text($urlbreak_currpage_val, $urlbreak_micro_val, $perc);
	
	//echo "similarity: $sim ($perc %)\n";	
	
	

	 ///if(strcmp($urlbreak_currpage[1],$urlbreak_micro[1]))
	////if(strcmp($urlbreak_currpage[1],$urlbreak_micro[1]) == 0)
		//if(strcasecmp($urlbreak_currpage[1],$urlbreak_micro[1]) == 0)		
		//if(strcasecmp($urlbreak_currpage_val,$urlbreak_micro_val) === 0)		
		//if(strcmp($urlbreak_currpage_val,$urlbreak_micro_val)==0)		
		if(strcasecmp($urlbreak_currpage_val,$urlbreak_micro_val)==0 && strcmp($_REQUEST['input'],$entry["title"]) >= -3  )
		{		

		$url_alias_link = db_select('url_alias', 'ua')->fields('ua',array('alias'))->condition('source', 'node/'. $entity["entity_id"])->execute()->fetchField(); 				  
	
		
		// for meta desc //
		$entry["bundle"] = (isset($match["attrs"]["field_page_meta_description_value"]) ? $match["attrs"]["field_page_meta_description_value"] : "")."<br>";
				

		$entry["bundle2"] = strip_tags(html_entity_decode($entry["bundle"]));
				
		$lastchar = substr(trim($entry["bundle2"]), -1);
		if($lastchar=="."){			
			$addfullstop="";
		}else{			
			$addfullstop=".";
		} 	
		
		//print "<div class='search-web'><a href='$_REQUEST[lang_name]/$url_alias_link' target=_blank><h5 class='search-title color'>".$entry["title"]."</h5><span class='text hover-color'>".$entry["bundle2"].$addfullstop."<br><br></span></a></div>";
		print "<div class='search-web'><a href='$_REQUEST[path_base]$_REQUEST[lang_name]/$url_alias_link' target=_blank><h5 class='search-title color'>".$entry["title"]."</h5><span class='text hover-color'>".trim($entry["bundle2"]).$addfullstop."</span></a>";
		
		
		//echo "<br>Strings matched<br>";
		if($exact_phrase===false && $size > 1)
            {
                $first = true;
                echo "<div class='search-help'>";
                for($ind=0; $ind<$size; $ind++)
                {
                    if((stripos($entry["title"],$word_array[$ind])===false) && (stripos($entry["bundle2"],$word_array[$ind])===false)&& (stripos($entity["content"],$word_array[$ind])===false))
                    {
                        if($first===true)
                        {
                           echo "<span class='help-title'>" . $Missing . " : </span> <span>" . $word_array[$ind] . "</span>";
                            $first=false;
                        }
                         else
							{
							echo "<span>" . " , " . $word_array[$ind] . "</span>";
							}
                    }
                }
           
            }
		
		}
		 print "</div>";
		
		/*else
		{			
			//continue;
			///print "CUR = ".$urlbreak_currpage[1];
			///print "MIC = ".$urlbreak_micro[1];
			///print "<Br>SKIP THIS ID";
			///print "<br>TITLE = ".$entry["title"]."<br>";
			//echo "<br>strings not matched<br>";
			
		}*/
		
  }
  
  $arrTitleAndBundle[] = $entry;
}
?>
