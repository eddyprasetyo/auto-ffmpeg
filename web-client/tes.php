<?php
$id_program="PG001234";
$komen="ffmpeg -progress progress.txt -i $id_program.MXF -filter_complex \"[0:a:0][0:a:1]amerge[aout]\" -map 0:v:0 -map \"[aout]\" -c:v h264_qsv -preset veryfast -b:v 6M -c:a aac -b:a 384k -y X:\\$id_program.mp4";
echo($komen);
?>