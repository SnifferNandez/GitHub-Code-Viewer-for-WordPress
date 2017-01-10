<?php
/*
Plugin Name: Code From Url
Version: 1.1
Description: Visit https://github.com/SnifferNandez/GitHub-Code-Viewer-for-WordPress for detailed instructions.
Author: Jared Barneck, modified by SnifferNandez
Author URI: http://sniffer.comparte.tips/
*/

class github {
  var $db;
  var $table;
  var $cache = array();

  function github() {
    global $wpdb;

    $this->db = $wpdb;
    $this->table = $this->db->prefix . "CodeAsUrl";
  }

  function install() {
    $result = $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `url` text NOT NULL,
                               `code` text NOT NULL,
                               `updated` datetime NOT NULL default '0000-00-00 00:00:00',
                               PRIMARY KEY  (`id`)
                               )");
  }

  function uninstall() {
    $result = $this->db->query("DROP TABLE IF EXISTS `{$this->table}`");
  }

  function get_code($text='') {
    $pattern = '/(\[CodeFromUrl="[^"\']*"[ ]*(lang="[^"\']*")?[ ]*(opt="[^"]*")?\])/i';
    if (preg_match_all($pattern, $text, $matches)) {
      $urls = [];
      $i = 0;
      foreach($matches[0] as $match) {
        $urls[$i++] = trim(explode('"',$match)[1]);
      }
      $this->__loadCache($urls);
      $txtrans = array();
      foreach($matches[0] as $match) {
        $url = trim(explode('"',$match)[1]);
        $lang = explode('" lang="',$match);
        array_push($lang,"html");
        $lang = trim(explode('"',$lang[1])[0]);
        $options = explode('" opt="',$match);
        array_push($options,"");
        $options = trim(explode('"',$options[1])[0]);
        if (isset($this->cache[$url])) {
          $code = $this->cache[$url];
        } else {
          $code = wp_remote_fopen($url);
          // Trying to fix like https://github.com/Viper007Bond/syntaxhighlighter/issues/32
          //$code = str_replace('[', '[[', $code);
          //$code = str_replace(']', ']]', $code);
          //$corrections = array ('p' => '((((', ']' => ']]');
          $this->__setCache($url, $code);
        }
        $code = '[co'.'de lang="'.$lang.'" '.$options.']'.$code.'[/co'.'de]';
        $adjust = array($match => $code);
        $txtrans = array_merge($txtrans, $adjust);
      }
      $text = strtr($text,$txtrans);
    }
    return $text;
  }

  function __loadCache($urls) {
    $sql = $this->db->prepare( "SELECT * FROM $this->table WHERE url IN (%s)", implode('", "', $urls)); 
    $results = $this->db->get_results($sql, ARRAY_A);
    if ($results) {
      $old = array();
      foreach($results as $row) {
        if($row['updated'] < date('Y-m-d H:i:s', strtotime('-13 day'))) {
          $old[] = $row['id'];
        } else {
          $this->cache[$row['url']] = $row['code'];
        }
      }
      
      if($old) {
        $this->db->delete( $this->table, array( 'id' => implode(',', $old) ) );
      }
    }

    return true;
  }

  function __setCache($url, $code) {
    $this->db->insert( $this->table, array( 'url' => $url, 'code' => $code, 'updated' => date('Y-m-d H:i:s')));
  }
}

$github = new github();
register_activation_hook(__FILE__, array($github, 'install'));
register_deactivation_hook(__FILE__, array($github, 'uninstall'));
add_filter('the_content', array($github, 'get_code'), 7);
?>
