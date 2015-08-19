<?php
/*
 Plugin Name: Sitemap Robots Auto Generator
 Plugin URI:  http://permutat.de
 Description: Creates on post or postupdate sitemap and robots.txt in wordpress home folder
 Version:     1.0
 Author:      Mamun Hodali
 Author URI:  http://permutat.de
 License:     GPL3
 License URI: https://www.gnu.org/licenses/gpl-3.0.html
 Domain Path: /languages
 Text Domain: sitemap_robots_auto_generator
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function sitemap_robots_auto_generator(){
	
	global $wpdb;
	$path = get_home_path();
	$query = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE (post_type like 'post' OR post_type like 'page') AND post_status NOT LIKE 'auto-draft'"));
	
	if(mysql_num_rows($query)!== 0){
		
		$doc = new DOMDocument('1.0', 'UTF-8');
		// we want a nice output
		$doc->formatOutput = true;
		
		$def = $doc->createElement('urlset');
		$def = $doc->appendChild($def);
		
		$xmlns = $doc->createAttribute('xmlns');
		$xmlns->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
		$xmlns = $def->appendChild($xmlns);
		
		$xmlnsXsi = $doc->createAttribute('xmlns:xsi');
		$xmlnsXsi->value = 'http://www.w3.org/2001/XMLSchema-instance';
		$xmlnsXsi = $def->appendChild($xmlnsXsi);
		
		$xsi = $doc->createAttribute('xsi');
		$xsi->value = 'http://www.w3.org/2001/XMLSchema-instance http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd';
		$xsi = $def->appendChild($xsi);
		
		foreach ($query as $key => $value){
			$url = $doc->createElement('url');
			$url = $def->appendChild($url);
		
			$loc = $doc->createElement('loc');
			$loc = $url->appendChild($loc);
		
			$link = $doc->createTextNode(get_permalink($value));
			$link = $loc->appendChild($link);
		}
		$doc->save($path."/sitemap");
	}

	//Create robots.txt
	
	$txtFile = fopen($path."/robots.txt", "w");	
	if($txtFile){
		$text = "User-agent: *". PHP_EOL . "Disallow: /wp-admin/". PHP_EOL . "Sitemap: ".get_site_url()."/sitemap";
		fwrite($txtFile, $text);
	}
	fclose($txtFile);
	
}
add_action('save_post', 'sitemap_robots_auto_generator');
?>