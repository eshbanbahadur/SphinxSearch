<?php
$q = $_REQUEST['input'];

if (empty($q))
	{
	print ("No Result Found");
	exit();
	}

define('DRUPAL_ROOT', dirname(dirname(dirname(dirname(dirname(__DIR__))))));

// Bootstrap to database level for db api

require_once DRUPAL_ROOT . '/includes/bootstrap.inc';

require_once DRUPAL_ROOT . '/includes/common.inc';

drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

if ($_SERVER['HTTP_HOST'] == 'localhost')
	{
	include 'C:\Sphinx\api\sphinxapi.php';

	}
  else
	{
	include '/etc/sphinx/sphinxapi.php';

	}

// Build search query

$cl = new SphinxClient();
$cl_news = new SphinxClient();

if ($_SERVER['HTTP_HOST'] == 'localhost')
	{
	$cl->SetServer("localhost", 3312);
	$cl_news->SetServer("localhost", 3312);

	// $cl->SetServer( "localhost", 9312 );

	}
  else
	{
	$cl->SetServer("localhost", 9312);
	$cl_news->SetServer("localhost", 9312);

	}


$cl->SetMatchMode(SPH_MATCH_EXTENDED);
$cl->SetRankingMode(SPH_RANK_SPH04);

$cl_news->SetMatchMode(SPH_MATCH_EXTENDED);
$cl_news->SetRankingMode(SPH_RANK_SPH04);

$cl_news->SetSortMode(SPH_SORT_EXTENDED, 'field_date_posted_value DESC' );

//$cl_news->SetSortMode(SPH_SORT_EXTENDED, 'field_date_posted_value ASC' ); //new to sort by latest news


$query = '"'.$cl->EscapeString($q).'"';
$query_news = '"'.$cl_news->EscapeString($q).'"';
$exact_phrase;
$Missing;
$word_array;
$size;

	$query = $query . '/1';
	$query_news = $query_news . '/1';
	$exact_phrase = false;
	
	if ($_REQUEST['lang_name'] == 'en')
		{
			
			$word_array= preg_split('/\s+/', $q);
			$size = count($word_array);
			$Missing = "Missing";
		}
	  else
		{
			$word_array= preg_split('/\s+/', $q);
			$size = count($word_array);
			$Missing = "المفقودة";
		}

	
	// }
	
	if($_REQUEST['lang_name']=='en')
	{
		$indexer_name = 'drupal_search';
		$indexer_name_news = 'main_search_news';
	}
	else
	{	
		$indexer_name = 'drupal_search_arabic';
		$indexer_name_news = 'main_search_news_arabic';
	}

	

	if ($size > 1) {
	$cl->SetLimits(0, 50);

		$cl->AddQuery('@(title) ' . $query . ' & @(field_body_value)' . $query . '', $indexer_name);		
		$cl->AddQuery('@(title) ' . $query . ' & @(field_body_value) !' . $query . '', $indexer_name);		
		$cl->AddQuery('@(title) !' . $query . ' & @(field_body_value)' . $query . '', $indexer_name);

	
		// for partial search
		for ($i=0; $i < $size ; $i++) { 
		
		
			$cl->SetLimits(51, 100);
		
			$cl->AddQuery('@(title) ' . $word_array[$i] . ' & @(field_body_value)' . $word_array[$i] ,$indexer_name);			
			$cl->AddQuery('@(title) ' . $word_array[$i] . ' & @(field_body_value) !'. $word_array[$i]  , $indexer_name);
			$cl->AddQuery('@(title) !' . $word_array[$i]  .' & @(field_body_value) ' . $word_array[$i],  $indexer_name);

		}
		// echo "ok ".$size;
	}
	else{
		$cl->SetLimits(0, 250);
		$cl->AddQuery('@(title) ' . $query . ' & @(field_body_value)' . $query . '', $indexer_name);
		$cl->AddQuery('@(title) ' . $query . ' & @(field_body_value) !' . $query . '', $indexer_name);		
		$cl->AddQuery('@(title) !' . $query . ' & @(field_body_value)' . $query . '', $indexer_name);
	}
	
$cl_news->SetLimits(0, 150);
$cl_news->AddQuery($query_news,$indexer_name_news);
$searchresults_news = $cl_news->RunQueries();
	
$searchresults = $cl->RunQueries();


if ((!isset($searchresults[0]["matches"])) && (!isset($searchresults[1]["matches"])) && (!isset($searchresults[2]["matches"])) && (!isset($searchresults_news[0]["matches"])) && (!isset($searchresults_news[1]["matches"])) && (!isset($searchresults_news[2]["matches"])))

	{
	print ("No Result Found");
	exit();
	}

include ('db.php');

$prot = $_SERVER['HTTP_HOST'];
$path_val = $_REQUEST['path_base'];
$protocol_val = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$addfullstop = "";
$lastchar = "";
$arrTitleAndBundle = array();
$i = 0;

for ($j = 0; $j < 6; $j++)
foreach($searchresults[$j]["matches"] as $match)
	{
		$entry = [];
		if (isset($match["attrs"]))
			{
				"<br />" . $entry["title"] = (isset($match["attrs"]["title"]) ? $match["attrs"]["title"] : "");
				$entity["entity_id"] = (isset($match["attrs"]["nid"]) ? $match["attrs"]["nid"] : "");
				$entity["content"] = (isset($match["attrs"]["field_body_value"]) ? $match["attrs"]["field_body_value"] : "");
				$entry["content_type"] = (isset($match["attrs"]["bundle"]) ? $match["attrs"]["bundle"] : "");

				// Getting Link //

				$url_alias_link = db_select('url_alias', 'ua')->fields('ua', array(
					'alias'
				))->condition('source', 'node/' . $entity["entity_id"])->execute()->fetchField();

				// for meta desc //

				$entry["bundle"] = (isset($match["attrs"]["field_page_meta_description_value"]) ? $match["attrs"]["field_page_meta_description_value"] : "") . "<br />";
				$entry["bundle2"] = strip_tags(html_entity_decode($entry["bundle"]));

				// / Add Fullstop with Arabic content. ///

				$lastchar = substr(trim($entry["bundle2"]) , -1);
				if ($lastchar == ".")
					{
					$addfullstop = "";
					}
				  else
					{
					$addfullstop = ".";
					}

					//echo "i ".$i;
				//if ($entry["content_type"] != "newsarticle")
				//	{
						// echo "<br>size ".$size;
						// echo "<br>record for pages";
							print "<div class='search-web'><a href='$_REQUEST[path_base]$_REQUEST[lang_name]/$url_alias_link' target=_blank><h5 class='search-title color'>" . $entry["title"] . "</h5><span class='text hover-color'>" . $entry["bundle2"] . $addfullstop . $entry["created"]. "</span></a>";
							if ($size > 1)
								{
									$first = true;									
									echo "<div class='search-help'>";
									for ($ind = 0; $ind < $size; $ind++)
										{
										$pos = stripos($entry["title"], $word_array[$ind]);										
										if ((stripos($entry["title"], $word_array[$ind]) === false) && (stripos($entity["content"], $word_array[$ind]) === false))
										{
												if ($first == true)
												{
												echo "<span class='help-title'>" . $Missing . " : </span> <span>" . $word_array[$ind] . "</span>";
												$first = false;
												//$status='no';
												}
											  else
												{
												echo "<span>" . " , " . $word_array[$ind] . "</span>";
												}
											}

										// echo "";

										}

									echo "</div>";
								}

							print "</div>";
					//}
				

				
			}
			

		$arrTitleAndBundle[] = $entry;
		$i++;
	}






	// fopr news articles
	for ($j = 0; $j < 6; $j++)
foreach($searchresults_news[$j]["matches"] as $match)
	{
		$entry = [];
		if (isset($match["attrs"]))
			{
				"<br />" . $entry["title"] = (isset($match["attrs"]["title"]) ? $match["attrs"]["title"] : "");
				$entity["entity_id"] = (isset($match["attrs"]["nid"]) ? $match["attrs"]["nid"] : "");
				$entity["content"] = (isset($match["attrs"]["field_body_value"]) ? $match["attrs"]["field_body_value"] : "");
				$entry["content_type"] = (isset($match["attrs"]["bundle"]) ? $match["attrs"]["bundle"] : "");

	$unix_timestamp = $match["attrs"]["field_date_posted_value"];
	echo "<br>".date("d-m-Y\ TH:i:s\Z", $unix_timestamp);


				// Getting Link //

				$url_alias_link = db_select('url_alias', 'ua')->fields('ua', array(
					'alias'
				))->condition('source', 'node/' . $entity["entity_id"])->execute()->fetchField();

				// for meta desc //

				$entry["bundle"] = (isset($match["attrs"]["field_page_meta_description_value"]) ? $match["attrs"]["field_page_meta_description_value"] : "") . "<br />";
				$entry["bundle2"] = strip_tags(html_entity_decode($entry["bundle"]));

				// / Add Fullstop with Arabic content. ///

				$lastchar = substr(trim($entry["bundle2"]) , -1);
				if ($lastchar == ".")
					{
					$addfullstop = "";
					}
				  else
					{
					$addfullstop = ".";
					}

					//echo "i ".$i;
				//if ($entry["content_type"] == "newsarticle")
					//{
						// echo "<br>size ".$size;
						// echo "<br>record for news articles";
							print "<div class='search-web'><a href='$_REQUEST[path_base]$_REQUEST[lang_name]/$url_alias_link' target=_blank><h5 class='search-title color'>" . $entry["title"] . "</h5><span class='text hover-color'>" . $entry["bundle2"] . $addfullstop . "</span></a>";
							if ($size > 1)
								{
						// 			echo "<br>size ".$size;
						// echo "<br>record for news articles";
									$first = true;
									echo "<div class='search-help'>";
									for ($ind = 0; $ind < $size; $ind++)
										{
										if ((stripos($entry["title"], $word_array[$ind]) === false)  && (stripos($entity["content"], $word_array[$ind]) === false))
											{
											if ($first === true)
												{
												echo "<span class='help-title'>" . $Missing . " : </span> <span>" . $word_array[$ind] . "</span>";
												$first = false;
												}
											  else
												{
												echo "<span>" . " , " . $word_array[$ind] . "</span>";
												}
											}

										// echo "";

										}

									echo "</div>";
								}

							print "</div>";
					//}
				

				
			}
			

		$arrTitleAndBundle[] = $entry;
		$i++;
	}

?>