<?php
$konek=mysql_connect('toa.nettv.co.id','youtube','Y0utub3netmedia');
if($konek)echo"sukses<br>";
else
{
	echo "gagal<br>";
	goto selesai;
}

mysql_select_db("DB_YOUTUBE",$konek);
$test2=mysql_result(mysql_query("select VALUE_SETTING from TB_WORKER_SETTING where WORKER_SETTING = 'ffmpeg_option_filter';"),0);


echo"<br>";
var_dump($test2);

selesai:
mysql_close($konek);

?>