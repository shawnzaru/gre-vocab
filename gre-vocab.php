<?php
/*
Plugin Name: GRE Vocabulary Randomizer
Plugin URI: http://www.mywebsite.com
Description: Randomly display GRE words on a webpage.
Author: Shawn Qingxiang Wang
Author URI: http://www.mywebsite.com
Version: 0.0.3
*/

/* this function is not used. */
function isMobile() {
	return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function show_word($atts){
	extract(shortcode_atts(array('word' => 'abc'), $atts));
	$word = trim($word);
	$link = "http://3g.dict.cn/s.php?q=$word";
	echo "<div><a href=$link>".$word."</a></div>";
}

add_shortcode('gre_word', 'show_word');

function show_pair($atts){
	extract(shortcode_atts(array('pair' => ''), $atts));
	$pair = explode(" ", $pair);
	$pair[0] = trim($pair[0]);
	$pair[1] = trim($pair[1]);
	$link0 = "http://3g.dict.cn/s.php?q=$pair[0]";
	$link1 = "http://3g.dict.cn/s.php?q=$pair[1]";
	echo "<div class='gre-div'>";
	if ($pair[0] != "")
		echo "<a href=$link0><span><strong>".$pair[0]."</strong></span></a>";
	if ($pair[1] != "")
		echo "<a href=$link1><span><strong>".$pair[1]."</strong></span></a>";
	echo "</div>";
}

add_shortcode('gre_pair', 'show_pair');

function show_gre_vocabulary($atts){
	extract(shortcode_atts(array('count' => 10, 'mode' => 'mixed'), $atts));

	for ($i = 0; $i < $count; $i++) {
		$page = mt_rand(1, 38);
		//$page = 13;
		//$page = mt_rand(36, 38);

		if ($page == 1) $line = mt_rand(1, 152);
		elseif ($page == 14) $line = mt_rand(1, 215);
		elseif ($page == 35) $line = mt_rand(1, 169);
		elseif ($page == 36 || $page == 37) $line = mt_rand(1, 150);
		elseif ($page == 38) $line = mt_rand(1, 124);
		else $line = mt_rand(1, 216);

		$dirpath = plugin_dir_path( __FILE__ )."/data/gre-vocab-$page.txt";
		$file = new SplFileObject($dirpath);
		$file->seek($line-1);
		$content = $file->current();
		$content = explode("/", $content);
		$content[0] = trim($content[0]);
		$content[1] = trim($content[1]);
		$content[2] = trim($content[2]);

		$link = "http://3g.dict.cn/s.php?q=$content[0]";
		$link_youdao = "http://m.youdao.com/dict?le=eng&q=$content[0]";
		$link_etymonline = "http://etymonline.com/index.php?search=$content[0]";
		
		if ($mode == 'unfamiliar' && $content[2] != '?' && $content[2] != '?p') {
			$i--;
			continue;
		}

		echo "<div class='gre-div'>";
		echo "<span class='gre-span'>";
		echo "<a class='gre-link' href=$link>";
		if ($content[2] == '?' || $content[2] == '?p') echo "<strong><u>";
		echo "$content[0]";
		if ($content[2] == '?' || $content[2] == '?p') echo "</u></strong>";
		if ($content[2] == '?p' || $content[2] == 'p') echo "<sub>p</sub>";
		if ($content[2] == 'r') echo "<sub>r</sub>";
		if ($content[2] == 'pr' || $content[2] == 'rp') echo "<sub>pr</sub>";
		echo "</a>";
		echo "</span>";
		echo "<span class='gre-span'>";
		echo "<a class='gre-link' href=$link>($page, $line, $content[1])</a>";
		echo "<a class='gre-link' href=$link_youdao><img class='qlink' src='http://mywebsite.com/wp-content/uploads/2016/12/favicon_youdao.ico'/></a>";
		echo "<a class='gre-link' href=$link_etymonline><img class='qlink' src='http://mywebsite.com/wp-content/uploads/2016/12/favicon_etymonline.png'/></a>";
		echo "</span>";
		echo "</div>";
	}
}

add_shortcode('gre_vocab', 'show_gre_vocabulary');

function vocab_css() {
	echo "
	<style type='text/css'>
	.gre-link {
		border-bottom: 0px !important;
	}
	.gre-div {
		display: flex;
		justify-content: space-between;
	}
	.gre-span {
		font-size: 1.25em;
		vertical-align: bottom;
	}
	.qlink {
		display: inline !important;
	}
	</style>";
}

add_action('wp_head', 'vocab_css');
