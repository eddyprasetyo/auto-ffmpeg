<?php
include "header.php";
include "menu.php";
?>
<section id="queue">
<h2>Antrian</h2>
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
$kueri=mysql_query("SELECT A.NUM_QUEUE,A.ID_PROGRAM,B.CONTENT AS JOB_NAME ,A.INPUT_DATE FROM 
TB_QUEUE A JOIN TB_JOB B
ON A.ID_JOB = B.ID_JOB 
WHERE 
NUM_QUEUE > 0  ORDER BY NUM_QUEUE ASC");
$ambil=array();
while($hasil=mysql_fetch_array($kueri,MSSQL_ASSOC))
{
	array_push($ambil,$hasil);
}
mysql_free_result($kueri);
if ($ambil)
{	?>
	<table><thead><tr>
	<th>No</th>
	<th>Clip ID</th>
	<th>Job</th>
	<th>Waktu Masuk</th>
	<th></th></tr>
	</thead><tbody>
	<?php
	foreach($ambil as $key => $result)
	{
		echo"<tr><th>$result[NUM_QUEUE]</th>";
		echo"<th>$result[ID_PROGRAM]</th>";
		echo"<th>$result[JOB_NAME]</th>";
		echo"<th>$result[INPUT_DATE]</th>";
		echo"<th><a href=\"del-antrian.php?p=$result[ID_PROGRAM]\">x</a></th></tr>";
	}
	?>
	</tbody></table>
	
<?php
}
else
{
	echo"Antrian Kosong";
}
selesai:
mysql_close($konek);
?>
</article>

</section>

<?php
include "footer.php";
?>
