<?php
/*
 Plugin Name: Sitemap Auto Generator
 Plugin URI:  http://permutat.de
 Description: Creates sitemap for website
 Version:     1.0
 Author:      Mamun Hodali
 Author URI:  http://permutat.de
 License:     GPL2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 Domain Path: /languages
 Text Domain: sitemap_auto_generator
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
function sitemap_auto_generator(){
	
	global $wpdb;
	$query = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM $wpdb->posts"));
	$path = get_home_path();
	
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
		$url = $doc->appendChild($url);
		
		$loc = $doc->createElement('loc');
		$loc = $url->appendChild($loc);
		
		$link = $doc->createTextNode(get_permalink($value));
		$link = $loc->appendChild($link);	
	}
	$doc->save($path."/sitemap");

}
add_action('save_post', 'sitemap_auto_generator');