<?php

/**
 * WordPress filter for tags with commas * replace '--' with ', ' in the output
 * allow tags with comma this way * e.g.
 * save tag as "Fox--Peter" but display like "Fox, Peter" * or "cafe--restaurant" for "cafe, restaurant"
 * Follow me on Twitter: @HertogJanR
 */
add_action('init', 'uwpqsf_ajax_tag_comma_filter');

function uwpqsf_ajax_tag_comma_filter(){
  if ( isset($_POST['getdata']) ) {
    $postdata = parse_str($_POST['getdata'], $getdata);
    $formid = $getdata['uformid'];
    $nonce = $getdata['unonce'];

    if ( isset($formid) && wp_verify_nonce($nonce, 'uwpsfsearch') ) {
      add_filter('get_kompetenzfelder', 'uwpqsf_tag_comma_filter');
      add_filter('get_ddownload_tag', 'uwpqsf_tag_comma_filter');
      add_filter('get_the_taxonomies', 'uwpqsf_tags_comma_filter');
      add_filter('get_terms', 'uwpqsf_tags_comma_filter');
      add_filter('get_the_terms', 'uwpqsf_tags_comma_filter');
    }
  }
}
function uwpqsf_tag_comma_filter($tag_arr){
  $filtered_taxonomies = array('ddownload_tag', 'kompetenzfelder');
  $tag_arr_new = $tag_arr;
  if ( in_array($tag_arr->taxonomy, $filtered_taxonomies) && strpos($tag_arr->name, '--') !== false ) {
    $tag_arr_new->name = str_replace('--', ', ', $tag_arr->name);
  }
  return $tag_arr_new;
}
function uwpqsf_tags_comma_filter($tags_arr){
  $tags_arr_new = array();

  foreach ( $tags_arr as $tag_arr ) {
    $tags_arr_new[] = sandbox_tag_comma_filter($tag_arr);
  }
  return $tags_arr_new;
}

/**
 * Ajax pagination
 */
function uwpqsf_ajax_pagination($pagenumber, $pages = '', $range = 4, $id,$getdata){
	$showitems = ($range * 2)+1;

	$paged = $pagenumber;
	if(empty($paged)) $paged = 1;

	if($pages == '')
	 {

	   global $wp_query;
	   $pages = $query->max_num_pages;

	    if(!$pages)
		 {
				 $pages = 1;
		 }
	}

	if(1 != $pages)
	 {
	  $html = "<div class=\"uwpqsfpagi\">  ";
	  $html .= '<input type="hidden" id="curuform" value="#uwpqsffrom_'.$id.'">';

	 if($paged > 2 && $paged > $range+1 && $showitems < $pages)
	 $html .= '<a id="1" class="upagievent" href="#">&laquo; '.__("First","UWPQSF").'</a>';
	 $previous = $paged - 1;
	 if($paged > 1 && $showitems < $pages) $html .= '<a id="'.$previous.'" class="upagievent" href="#">&lsaquo; '.__("Previous","UWPQSF").'</a>';

	 for ($i=1; $i <= $pages; $i++)
	  {
		 if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
		 {
		 $html .= ($paged == $i)? '<span class="upagicurrent">'.$i.'</span>': '<a id="'.$i.'" href="#" class="upagievent inactive">'.$i.'</a>';
		 }
	 }

	 if ($paged < $pages && $showitems < $pages){
		 $next = $paged + 1;
		 $html .= '<a id="'.$next.'" class="upagievent"  href="#">'.__("Next","UWPQSF").' &rsaquo;</a>';}
		 if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {
		 $html .= '<a id="'.$pages.'" class="upagievent"  href="#">'.__("Last","UWPQSF").' &raquo;</a>';}
		 $html .= "</div>\n";$max_num_pages = $pages;
		 return apply_filters('uwpqsf_pagination',$html,$max_num_pages,$pagenumber,$id);
	 }


}// pagination


function get_uwpqsf_form($args=array()){
	$default = array('id' => false, 'formtitle' =>1, 'button' => 1,'divclass' => '', 'infinite'=>'');
	$atts=array_merge($default,$args);
	extract($atts);
	if($id)
		{
			 ob_start();
			 $output = include UWPQSFBASE . '/html/searchform.php';
			 $output = ob_get_clean();
			 return $output;
		}
		else{
			return 'no form added.';
		}

}
?>
