<?php
include "header.php";
include "menu.php";
?>
<section id="reset">
<h2>Re-Entry antrian gara gara segmen 1 error (restore belum lengkap)</h2>
<article>
<?php
require "setting.php";
$konek=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
if(!$konek)
	{
		echo "gagal konek db server<br>";
		goto selesai;
	}
if(!mysql_select_db($mysql_db,$konek))
	{
		echo "gagal select db";
		goto selesai;
	}
$kueri=mysql_query("DELETE FROM TB_QUEUE WHERE ID_PROGRAM IN (SELECT ID_PROGRAM FROM TB_HISTORY WHERE STATUS_TRANSCODE = '4' AND INFO = 'Gagal Transcode Segmen1 -');");
if ($kueri)
{
	echo"Reset antrian sukses<br>";
	$kueri2=mysql_query("DELETE FROM TB_HISTORY WHERE STATUS_TRANSCODE = '4' AND INFO = 'Gagal Transcode Segmen1 -';");
	if ($kueri2)
	{
		echo"Reset history sukses<br> cek antrian beberapa detik lagi, harusnya nongol paling akhir";
	}
	else
	{
		echo"Reset history gagal<br>Hubungi TOA";
	}
}
else
{
	echo"Gagal reset antrian<br>Hubungo TOA";
}

selesai:
mysql_close($konek);
?>
</article>

</section>

<?php
include "footer.php";
?>
