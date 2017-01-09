<?php
$WorkerID=3;

//source of ftp transfer
$ftp_server_mediabase="172.16.113.19";
$ftp_user_mediabase="anonymous";
$ftp_pass_mediabase="anonymous";


//destination of ftp transfer
$ftp_server_youtube="172.16.108.63";
$ftp_user_youtube_rw="webdev";
$ftp_pass_youtube_rw="W3bD3v";
$ftp_user_youtube_ro="webdev";
$ftp_pass_youtube_ro="W3bD3v";

//parameter ftp transfer
$ftp_transfer_timeout=1800;//in second
$dest_folder="";

$ftp_server_segment="172.16.108.71";
$ftp_user_segment="imotion";
$ftp_pass_segment="IM0ti0n";
$ftp_folder_segment="/ifs/data/Traffic";
$segmentation_file="segment.txt";

$mysql_server="toa.nettv.co.id";
$mysql_user="youtube";
$mysql_pass="Y0utub3netmedia";
$mysql_db="DB_YOUTUBE";

$binary_ffmpeg="C:\\ffmpeg\\ffmpeg.exe";
$binary_ffprobe="C:\\ffmpeg\ffprobe.exe";
$ffmpeg_progress_file="progress.txt";
$ffmpeg_list_segment_file="list.txt";
$ffmpeg_option_filter="-filter_complex \"[0:a:0][0:a:1]amerge[aout]\" -map 0:v:0 -map \"[aout]\" -vf yadif=0:1:0 -g 12 -bf 2";
$ffmpeg_option_video="-c:v h264_qsv -b:v 4M";
$ffmpeg_option_audio="-c:a aac -b:a 384k";
$ott_destination_folder="x:";
$youtube_destination_folder="y:";
$ott_queue_folder="s:";
$command_file="command.bat";
$logo="fullNETHD.png";
$ffmpeg_option_filter_logo = "-filter_complex overlay";

//loging
$logfile="worker.log";
$Size2Rotate=1048576;
$maxlogfile=10;
?>
