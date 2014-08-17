<?php

	include_once("executesearch.php");
	include_once('admin/functions.php');

	if(isset($adult) && $adult) {
		include_once("adult/html_boilerplate.php");
	}	
	else {
		include_once("html_boilerplate.php");
	}

	//for pagination. will need to be fixed for division
	$query = urlencode($_GET['q']);
	if(!isset($_GET['start'])) {
		$start = urlencode("1");
	} else {
		$start = urlencode($_GET['start']);
	}   

	$section_num = 1;
	if(array_key_exists('section_num', $_GET)) {
		$section_num = intval($_GET['section_num']);
	} 

	if(array_key_exists('num', $_GET)) {
		$num = intval($_GET['num']);
	}
	else { 
		$num=10;
	}

   $title = 'Generic';
   $section_name = runQuery('SELECT name FROM search_categories WHERE id='.$section_num);
	$section_name = $section_name[0]['name'];




	// Code for getting the results from the Google Custom Search Engine
	$url = "https://www.googleapis.com/customsearch/v1?key=AIzaSyDBzCfhslTSWG6hVgaZ9eFgVqc1Ck5jxRE&cx=013942562424063258541:ofu8c_sygk4&q={$query}&start={$start}&callback=json&num=$num";
	$results = execute_search($url);

	$pageNext = 2;
	$pagePrev = 0;
	
	if($results) {


		if(array_key_exists('queries', $results)) {
			$q = $results['queries'];

			if(array_key_exists('nextPage', $q)) {
				$pageNext = $q['nextPage'][0]['startIndex'];
			}
			
			if(array_key_exists('previousPage', $q)) {
				$pagePrev = $q['previousPage'][0]['startIndex'];
			}
		}
		
		if(array_key_exists('searchInformation', $results)) {

			$searchTime = $results['searchInformation']['formattedSearchTime'];

			$totalResults = $results['searchInformation']['formattedTotalResults'];
		}
	}

//	print_header();

	echo "<div id='sub_main_wrapper_results'>";

	echo "<div id='sub_content_wrapper'>";

//	echo "<div id='result_info'>About {$totalResults} ({$searchTime} seconds)</div>";

         /*

		// Wrap the recommended sites in its own section
		echo "<div id='recommended_section'>";

			// Print out the recommended sites header
			echo "<div id='recommended_header'>Our recommended sites</div>";

//
//			// Holds the recommended title
//			$rec_result_title = $results['items'][0]['title'];
//			// Holds the recommended link
//			$rec_result_link = $results['items'][0]['link'];
//			// Holds the recommneded formatted link
//			$rec_formatted_link = $results['items'][0]['formattedUrl'];
//			// Holds the recommended snippet
//			$rec_result_snippet = $results['items'][0]['snippet'];
//
//			echo "<div id='result'>
//						<a id='title_link' href='{$rec_result_link}'><span id='result_title'>{$rec_result_title}</span></a><br>
//						<a id='result_link' href='{$rec_result_link}'>{$rec_formatted_link}</a><br>
//						<span id='result_snip'>{$rec_result_snippet}</span>
//					  </div>";
//
//			echo "<script async src='//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>
//				<!-- search ad -->
//				<ins class='adsbygoogle'
////   			style='display:inline-block;width:728px;height:90px'
////   			data-ad-client='ca-pub-3723102550752370'
////   			data-ad-slot='7469889246'></ins>
//				<script>
//					(adsbygoogle = window.adsbygoogle || []).push({});
//				</script>";
//			echo "<script async src='//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>
//					<!-- test ad -->
//					<ins class='adsbygoogle'
//					     style='display:inline-block;width:970px;height:90px'
//					     data-ad-client='ca-pub-3723102550752370'
//					     data-ad-slot='6194219644'></ins>
//					<script>
//					(adsbygoogle = window.adsbygoogle || []).push({});
//					</script>";
//			echo "<div id='empty_space'></div>";
//			echo "<script async src='//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>
//					<!-- test ad -->
//					<ins class='adsbygoogle'
//					     style='display:inline-block;width:970px;height:90px'
//					     data-ad-client='ca-pub-3723102550752370'
//					     data-ad-slot='6194219644'></ins>
//					<script>
//					(adsbygoogle = window.adsbygoogle || []).push({});
//					</script>";
//
//			
			echo "<div id='ads'>";
			echo "<script type='text/javascript'><!--
					google_ad_client = 'ca-pub-3723102550752370';
					// test ad 

					google_ad_slot = '6194219644';
					google_ad_width = 970;
					google_ad_height = 90;
					//-->
					</script>
					<script type='text/javascript'
					src='//pagead2.googlesyndication.com/pagead/show_ads.js'>
					</script>";
			echo "</div>";
		// Close the recommended sites section
		echo "</div>";

*/

		//todo:change the ids here
		// Wrap the general medical search in its own section                     o
      echo '
      <script>
		function togglePlusMinus(section_num) {
			
         me = $("#result_wrapper"+section_num);

			me.toggle();

			myimg = $("#general_search_section"+section_num+" img:eq(0)");

			if(myimg.attr("src").indexOf("minus") > -1) {
         	myimg.attr("src", "myHealthSitesImgs/plus.png");
			}
			else {
         	myimg.attr("src", "myHealthSitesImgs/minus.png");
			}
		}

		</script>

		<style>
		.plusminus {
      	float:right;
		}

		</style>



		';

		echo "<div class='general_search_section' id='general_search_section$section_num' class='general_search_section'>";

			// Print out title header
			echo "<div id='general_header'>$section_name
			 <img class='plusminus' id='plusminus$section_num' onclick='togglePlusMinus($section_num);'  src='myHealthSitesImgs/minus.png' />
			
			</div>
			
			<div id='result_wrapper".$section_num."'>
			
			
			";

			// Loop that prints out each result that was returned, default here will be 10


			if(!array_key_exists('items', $results)) {
         	echo "There has been a problem fetching your results. We are sorry for the inconvenience.";
				die;
			}
			
			foreach ($results['items'] as $result) {
				$result_title = $result['title'];
				$result_link = $result['link'];
				$formatted_link = $result['formattedUrl'];
				$result_snippet = $result['snippet'];
				// Print out each result title, link, and snippet
				echo "<div class='.result' id='result'>
						<a id='title_link' href='{$result_link}'><span id='result_title'>{$result_title}</span></a><br>
						<a id='result_link' href='{$result_link}'>{$formatted_link}</a><br>
						<span id='result_snip'>{$result_snippet}</span>
					  </div>";
			}

			echo "<div id='forward_back'>";
			echo "<span>";
			
			if($pagePrev > 0) {
				echo "<a id='prev_page' href='results.php?q={$query}&start={$pagePrev}'>Previous</a>";
			}
			else {
				echo "Previous";
			}
			
			echo "&nbsp;</span>";

			for($i=1;$i<=10;$i++) {
				$startLocal = (($i-1)*10)+1;

				if($i == 0) $i='Previous';
				if($start == $startLocal) {
            	echo $i;
				}          
				else {
					echo "<span><a id='page' href='results.php?q={$query}&start=$startLocal'>$i</a></span>";
				}
			}

			echo "<span>";
			if($pageNext < 100) { //no more results
				echo "<a id='next_page' href='results.php?q={$query}&start={$pageNext}'>Next</a>";
			}
			else {
         	echo '&nbsp;Next';
			}
			echo "</span></div></div>";

		// Close the general medical search section
		echo "</div>";

		//close content wrapper
		echo "</div>"; 

	// Close the main page wrapper
	echo "</div>";

