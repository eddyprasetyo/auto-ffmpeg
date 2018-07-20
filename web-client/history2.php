<?php
include "header.php";
include "menu.php";
?>
<section id="history">
<h2>History sukses tp segmen 1</h2>
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
$kueri=mysql_query("SELECT A.ID_PROGRAM,B.CONTENT AS JOB_NAME,A.START_TIME_JOB,A.FINISH_TIME_JOB,C.STATUS_PROGRESS,A.INFO
FROM 
TB_HISTORY A JOIN TB_JOB B
ON A.ID_JOB = B.ID_JOB
JOIN TB_STATUS C
ON A.STATUS_TRANSCODE = C.ID_STATUS
WHERE INFO = 'SUCCESS'
ORDER BY FINISH_TIME_JOB DESC;");
$ambil=array();
while($hasil=mysql_fetch_array($kueri,MSSQL_ASSOC))
{
	array_push($ambil,$hasil);
}
mysql_free_result($kueri);

if ($ambil)
{	?>
	<table><thead><tr>
	<th>Clip ID</th>
	<th>JOB</th>
	<th>Waktu Mulai</th>
	<th>Waktu Selesai</th>
	<th>STATUS</th>
	<th>INFO</th>
	<th>error</th></tr>
	</thead><tbody>
	<?php
	foreach($ambil as $key => $result)
	{
			$baris=file_get_contents("http://localhost/parsing/tes-segmen2.php?id=$result[ID_PROGRAM]");
			if(!$baris)
			{
			    $info="Segmentasi tetep belum ada";
			    goto selesai3;
			}
			if(strlen($baris)<40)
			{
			    $info="Segmentasi masih salah";
			    goto selesai3;
			}
			$jumlahsegmen=round(strlen($baris)/40);
			if($jumlahsegmen<2)
			{
			    $info="Jumlah Segmen hanya 1";
				goto selesai2;
			}
			else
			{
				goto selesai3;
			}
		selesai2:
		echo"<tr><th>$result[ID_PROGRAM]</th>";
		echo"<th>$result[JOB_NAME]</th>";
		echo"<th>$result[START_TIME_JOB]</th>";
		echo"<th>$result[FINISH_TIME_JOB]</th>";
		echo"<th>$result[STATUS_PROGRESS]</th>";
		echo"<th>$result[INFO]</th>";
		echo"<th>$info</th></tr>";
		selesai3:
	}
	?>
	</tbody></table>
	
<?php
}
else
{
	echo"History Kosong";
}
selesai:
mysql_close($konek);
?>
</article>

</section>

<?php
include "footer.php";
?>
