<?php
	//$twtrfile = $_SERVER['DOCUMENT_ROOT'] ."/assets/includes/history/diw-tweets-history-05-05-16.txt";
	$twtrfile = $_SERVER['DOCUMENT_ROOT'] ."/assets/includes/diw-tweets.txt";
	$twtr = json_decode(file_get_contents($twtrfile));
	$t = 0;
	function linkify_tweet($status_text){
	  // linkify URLs
	  $status_text = preg_replace(
		'/(https?:\/\/\S+)/',
		'<a href="\1">\1</a>',
		$status_text
	  );			
	  // linkify twitter users
	  $status_text = preg_replace(
		'/(^|\s)@(\w+)/',
		'\1<a href="http://twitter.com/\2">@\2</a>',
		$status_text
	  );			
	  // linkify tags
	  $status_text = preg_replace(
		'/(^|\s)#(\w+)/',
		'\1<a href="http://search.twitter.com/search?q=%23\2">#\2</a>',
		$status_text
	  );			
	  return $status_text;
	}
	foreach($twtr->statuses as $itemdata):
		//if(!isset($itemdata->retweeted_status)):
			$t++;
			echo '<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6">
			<article class="twitter">';
			if(!empty($itemdata->entities->media[0]->media_url)):
				$media = '<span class="tweet-media"><p><a href="http://'.linkify_tweet($itemdata->entities->media[0]->display_url).'" target="_blank"><img src="'.$itemdata->entities->media[0]->media_url.'" alt="twitter media" /></a></p></span>';
			else:
				$media = '';
			endif;					
			echo '<div class="twitter-article" id="'.$t.'"><div class="item">';
			echo '<div class="twitter-pic"><a href="https://twitter.com/'.$itemdata->user->screen_name.'" target="_blank"><img src="'.$itemdata->user->profile_image_url.'" width="48" height="48" alt="twitter icon" /></a></div>';
			echo '<div class="twitter-text"><p><span class="tweetprofilelink"><strong><a href="https://twitter.com/'.$itemdata->user->screen_name.'" target="_blank">'.$itemdata->user->name.'</a></strong> <a href="https://twitter.com/'.$itemdata->user->screen_name.'" target="_blank" class="tweetuser">@'.$itemdata->user->screen_name.'</a></span><span class="tweet-time"><a href="https://twitter.com/'.$itemdata->user->screen_name.'/status/'.$itemdata->id_str.'" target="_blank">'.date("l d M Y", strtotime($itemdata->created_at)).'</a></span><span class="tweet-content">'.linkify_tweet($itemdata->text).'</p></span>'.$media;
			echo '</div></div><div class="clear"></div></div></article></div>';
			if($t == 12): break; else: endif;
		//else:
		//endif;
	endforeach;