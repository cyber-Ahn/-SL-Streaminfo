<?php
include ("../TGN/gate/liberay/lib.php");
$server = "mp3channels.webradio.antenne.de"; //IP (x.x.x.x or domain name)
$iceport = "80/rockantenne"; //Port
$iceurl = "stream.mp3"; //Mountpoint






if($fp = @fsockopen($server, $iceport, $errno, $errstr, '1')) 
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
  echo '<pre>' . print_r($json_a, true) . '</pre>';
}
else 
{
  echo "<p><b>Stream Status:</b> Offline";
}

echo"<br> $write";
?>