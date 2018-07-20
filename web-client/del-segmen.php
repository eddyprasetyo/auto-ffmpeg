<?php
include "header.php";
include "menu.php";
?>
<section id="reset">
<h2>Re-Entry antrian gara gara segmentasi error (gak ada ato cuma 1)</h2>
<article>
<?php
$idpg=$_GET['p'];
			$baris=file_get_contents("http://localhost/parsing/tes-segmen2.php?id=$idpg");
			if(!$baris)
			{
			    $info="Segmentasi $idpg tetep belum ada";
			    goto selesai2;
			}
			if(strlen($baris)<40)
			{
			    $info="Segmentasi $idpg masih salah";
			    goto selesai2;
			}
			$jumlahsegmen=round(strlen($baris)/40);
			if($jumlahsegmen<2)
			{
			    $info="Jumlah Segmen $idpg masih hanya 1";
				goto selesai2;
			}

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
$kueri=mysql_query("DELETE FROM TB_HISTORY WHERE ID_PROGRAM = '$idpg';");
if ($kueri)
{
	echo"Reset history $idpg sukses<br>";
	$kueri2=mysql_query("DELETE FROM TB_QUEUE WHERE ID_PROGRAM = '$idpg';");
	if ($kueri2)
	{
		echo"Reset antian $idpg sukses<br> cek antrian beberapa detik lagi, harusnya nongol paling akhir";
	}
	else
	{
		echo"Reset antrian $idpg gagal<br>Hubungi TOA";
	}
}
else
{
	echo"Gagal reset $idpg<br>Hubungo TOA";
}
selesai:
mysql_close($konek);
selesai2:
echo($info);
?>
</article>

</section>

<?php
include "footer.php";
?>
