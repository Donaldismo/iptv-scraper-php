<?php
$search = (isset($_GET['search']) ? '/s/'.$_GET['search'] : '');
$country = (isset($_GET['country']) ? $_GET['country'] : '');
$limit = intval((isset($_GET['limit']) ? $_GET['limit'] : 10));

header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=$country$search.m3u8");

$url = 'https://iptvcat.com/ajax/streams_a?action=playlist_download';

for ($i = 1; $i <= $limit; $i++) {
	if (ob_get_level()) {
      ob_end_clean();
    }
	$options = array(
		'http' => array(
			'header'  => 'Referer: http://iptvcat.com/',
			'method'  => 'POST',
			'content' => "$country$search/$i"
		)
	);
	$context = @stream_context_create($options);
	$result = @file_get_contents($url, false, $context);
	if ($result == true) {
		$result = json_decode($result, true);
		if($result['status'] == 'true')
		{	
			if($i > 1)
				echo mb_substr($result['data'], 7);
			else
				echo $result['data'];
			continue;
		}
		if($i > 1)
			echo writeMsg('iptvcat Possible Parse Error');
		exit;
	}
	echo writeMsg('iptvcat Possible Network Error');
	exit;
}

function writeMsg($message) {
  echo "#EXTM3U
#EXTINF:0,$message
https://via.placeholder.com/1280x720/000000/FFFFFF/?text=$message";
}
?>