<?php
/**
 * @package WiKi Style Table of Content
 * @version 1.0
 */
/*
Plugin Name: WiKi Style Table of Content
Description: Automatically adds WiKi Style TOC inside the post or page. Just place [toc] where you want the TOC to show.
Author: Rabin Biswas
Version: 1.0
Author URI: http://updateox.com/
*/

function wk_st_toc_install() {
   add_option("wk_st_toc_table_title", "Table of content");
   add_option("wk_st_toc_table_list_type", "ol");
   add_option("wk_st_toc_first", "2");
   add_option("wk_st_toc_load_style", "y");
}
register_activation_hook(__FILE__,'toc_install');

include "settings.php";
add_action( 'admin_menu', 'wk_st_toc_settings_load' );

function wk_st_toc_settings_load() {
	add_options_page( 'WiKi Style Table of Content Options', 'WiKi Style TOC', 'manage_options', 'toc', 'wk_st_toc_settings' );
}

//[toc] shortcode
function wk_st_toc_shortcode() {
	global $post;
	$toc = $post->post_content;	
	$output = "";
	//get all [toccon] tags from post body
	preg_match_all('/\[toccon ([^\[]+)\]/',$toc, $toc_tags);
	
	foreach ($toc_tags[1] as $toc_tags_val){
		//get all [toccon] values
		$toc_tags_arr = shortcode_parse_atts($toc_tags_val);
		$toc_tags_on_post[] = $toc_tags_arr;
	}
	if ($toc_tags_on_post == null) {
		$output .= 'No content in the body for TOC.';
		return $output;
	} else {
		$toc_links = array();
		foreach($toc_tags_on_post as $item) {
			//group them by id
			$toc_links[$item['id']][] = $item;
		}
		$toc_title = get_option('wk_st_toc_table_title');
		$toc_first = get_option('wk_st_toc_first');
		if ($toc_first != null){
			$title_o   = '<h'.$toc_first.'>';
			$title_c   = '</h'.$toc_first.'>';
		}
		$list_type = get_option('wk_st_toc_table_list_type');
		if ($list_type != null){
			$list_o = '<'.$list_type.'>';
			$list_c = '</'.$list_type.'>';
		}
		$output .= "\n";
		$output .= '<table id="Table_of_content" class="toc">';
		$output .= '<tr>';
		$output .= '<td style="text-align:center;">';
		$output .= $title_o.'<span id="toc">'.$toc_title.'</span> '.' <span style="font-size:11px;">[<a href="#h" class="hidetoc">Hide</a>]</span>'.$title_c;
		$output .= '</td>';
		$output .= '</tr>';
		$output .= "\n";
		$output .= '<tr class="toccon">';
		$output .= '<td>';
		$output .= "\n";
		$output .= $list_o;
		$output .= "\n";

		foreach($toc_links as $parent){
			$output .= "\n";
			$output .= '<li>';
			$output .= "\n";
			$output .= '<a href="#'.$parent[0]['id'].'">'.$parent[0]['title'].'</a>';
			$sub = $parent;
			if (count($sub) > 1){
				$sub_id = array();
				foreach ($sub as $sub_item){
					if (isset($sub_item['sub'])){
						//group them by sub
						$sub_id[$sub_item['sub']][] = $sub_item;
					}	
				}
				$output .= "\n";
				$output .= $list_o;
				foreach ($sub_id as $sub_id_item) {
					$output .= "\n";
					$output .= '<li>';
					$output .= '<a href="#'.$sub_id_item[0]['sub'].'">'.$sub_id_item[0]['title'].'</a>';
					
					$sub2 = $sub_id_item;
					if (count($sub2) >1) {
						$sub2_id = array();
						foreach ($sub2 as $sub2_item){
							if (isset($sub2_item['sub2'])){
								$sub2_id[$sub2_item['sub2']][] = $sub2_item;
							}
						}
						$output .= "\n";
						$output .= $list_o;
						foreach ($sub2_id as $sub2_id_item){
							$output .= "\n";
							$output .= '<li>';
							$output .= '<a href="#'.$sub2_id_item[0]['sub2'].'">'.$sub2_id_item[0]['title'].'</a>';
							$output .= '</li>';
						}
						$output .= "\n";
						$output .= $list_c;
					}
					$output .= "\n";
					$output .= '</li>';
					$output .= "\n";
				}
				$output .= $list_c;
			}
			$output .= "\n";
			$output .= '</li>';
			$output .= "\n";
		}
		$output .= "\n";
	}
	$output .= $list_c;
	$output .= "\n";
	$output .= '</td>';
	$output .= '</tr>';
	$output .= '</table>';
	$output .= "\n";
	return $output;
}
add_shortcode( 'toc', 'wk_st_toc_shortcode' );

function wk_st_toc_content_shortcode($atts) {
	$title = $atts['title'];
	$toc_first = get_option('wk_st_toc_first');
	$top_link = "<span style='font-size:11px;font-weight:normal'> [<a href='#toc' title='Back to Table of Content'>Toc</a>]</span>";
	if ($toc_first != null){
		$id   = $toc_first;
		$sub  = $toc_first+1;
		$sub2 = $toc_first+2;
		
		$id_o   = '<h'.$id.'>';
		$id_c   = '</h'.$id.'>';
		$sub_o  = '<h'.$sub.'>';
		$sub_c  = '</h'.$sub.'>';		
		$sub2_o = '<h'.$sub2.'>';
		$sub2_c = '</h'.$sub2.'>';
	}
	
	if ($atts['sub2'] != NULL){
		$id 	   = $atts['sub2'];
		$output = $sub2_o."<span id='".$id."'>".$title."</span>".$top_link.$sub2_c;
	} elseif ($atts['sub'] != NULL) {
		$id 	   = $atts['sub'];		
		$output = $sub_o."<span id='".$id."'>".$title."</span>".$top_link.$sub_c;	
	} elseif ($atts['id'] != NULL){
		$id    	   = $atts['id'];
		$output = $id_o."<span id='".$id."'>".$title."</span>".$top_link.$id_c;
	}
	return $output;
}
add_shortcode( 'toccon', 'wk_st_toc_content_shortcode' );

function wk_st_toc_stylesheet() {
	wp_register_style( 'wk_st_toc_style', plugins_url('style.css', __FILE__) );
	wp_enqueue_style( 'wk_st_toc_style' );
}
if ( get_option('wk_st_toc_load_style') == "y") {
	add_action( 'wp_enqueue_scripts', 'wk_st_toc_stylesheet' );
}
function wk_st_toc_shortcode_empty_p_br_fix($content){
    $pattern = array (
		'<p>['    => '[', 
		']</p>'   => ']', 
		']<br />' => ']'
    );
    $content = strtr($content, $pattern);
	return $content;
}
add_filter('the_content', 'wk_st_toc_shortcode_empty_p_br_fix');
