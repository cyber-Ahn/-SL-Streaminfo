<link rel=stylesheet href="../liberay/style.css" type="text/css">
<?php
include ("../TGN/gate/liberay/lib.php");
$server = $_GET["ip"];
$port = $_GET["port"];
$ip = $server;
$stream_title = $_GET["stream"];
$cachIP = explode("://",$server);
$server = $cachIP[1];
$iceport = $port;
$iceserver = $server;
$write = "";
if($stream_title == "")
{
$stream_title = "Contact Cyber Ahn for Update";
}
$fp = @fsockopen($server, $port, $errno, $errstr, 30);
if ($fp)
{
        fputs($fp, "GET /7.html HTTP/1.0\r\nUser-Agent: XML Getter (Mozilla Compatible)\r\n\r\n");
        while(!feof($fp))
        $page .= fgets($fp, 1000);
        fclose($fp);
        $page = ereg_replace(".*<body>", "", $page);
        $page = ereg_replace("</body>.*", ",", $page);
        $numbers = explode(",", $page);
        $shoutcast_currentlisteners = $numbers[0];
        $connected = $numbers[1];
        if($connected == 1) 
        {
            $radio_status = 1;
            $wordconnected = "yes";
        }
        else
        $wordconnected = "no";
        $shoutcast_peaklisteners = $numbers[2];
        $shoutcast_maxlisteners = $numbers[3]; 
        $shoutcast_reportedlisteners = $numbers[4];
        $shoutcast_bitrate = $numbers[5];
        $shoutcast_cursong = $numbers[6];
        $shoutcast_curbwidth = $shoutcast_bitrate * $shoutcast_currentlisteners;
        $shoutcast_peakbwidth = $shoutcast_bitrate * $shoutcast_peaklisteners;
} 
function getPageStream($web)
{
		$html = "";
		  $ch = curl_init($web);
		  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.12) Gecko/20070508 Firefox/1.5.0.12");
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_HEADER, 0);
		  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		  curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		  $html = curl_exec($ch);
		  if(curl_errno($ch))
		  {
			  $html = "";
		  }
		  curl_close ($ch);
		return $html;
}
function getBetweenStream($content,$start,$end)
{
		$a1 = strpos($content,$start);
		$content = substr($content,$a1 + strlen($start));
		while($a2 = strrpos($content,$end))
		{
			$content = substr($content,0,$a2);
		}
		return $content;
}  
if ($radio_status == 1)
{
  $stream_last = $ip.":".$port."/played.html";
  $data_last = getPageStream($stream_last);
  $last_song = getBetweenStream($data_last,'Current Song</b></td></tr><tr><td>','</tr></table><br><br><table cellpadding=0');
  $write = "stream_typ:ShoutCast|strema_title:".$stream_title."|stream_status:Stream is up at ".$shoutcast_bitrate." kbps with ".$shoutcast_reportedlisteners." of ".$shoutcast_maxlisteners." listeners (".$shoutcast_reportedlisteners." unique)|listener_peak:".$shoutcast_reportedlisteners."|current_song:".$shoutcast_cursong."|last_song<".$last_song;
}
elseif($fp = @fsockopen($iceserver, $iceport, $errno, $errstr, '1')) 
{
  fclose($fp);
  $string = file_get_contents("http://" . $server . ":" . $iceport . "/status-json.xsl");
  $json_a = json_decode($string, true);
  $icecast_reportedlisteners = $json_a['icestats']['source'][0]['listeners'];
  $icecast_bitrate = $json_a['icestats']['source'][0]['bitrate']; 
  $icecast_maxlisteners = $json_a['icestats']['source'][0]['listener_peak']; 
  $icecast_cursong = $json_a['icestats']['source'][0]['yp_currently_playing'];
  $last_song = "No Data Found";
  $write = "stream_typ:Icecast|strema_title:".$stream_title."|stream_status:Stream is up at ".$icecast_bitrate." kbps with ".$icecast_reportedlisteners." of ".$icecast_maxlisteners." listeners (".$icecast_reportedlisteners." unique)|listener_peak:".$icecast_reportedlisteners."|current_song:".$icecast_cursong."|last_song<".$last_song;
} 	
else 
{
  $write = "strema_title:Stream is Offline|stream_status:Stream is up at --- kbps with --- of -- listeners (-- unique)|listener_peak:0|current_song:---|last_song<---";
}

echo "$write";
?>