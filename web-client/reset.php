<?php
include "header.php";
include "menu.php";
?>
<section id="reset">
<h2>Re-Entry antrian gara gara segmentasi error (gak ada ato cuma 1)</h2>
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
$kueri=mysql_query("SELECT A.ID_PROGRAM,B.INFO FROM 
TB_QUEUE A JOIN TB_HISTORY B
ON A.ID_PROGRAM = B.ID_PROGRAM 
WHERE INFO = 'Segmentasi tidak ada'
OR INFO = 'Segmentasi salah'
OR INFO = 'Jumlah Segmen hanya 1';");
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
	<th>INFO</th>
	<th></th></tr>
	</thead><tbody>
	<?php
	foreach($ambil as $key => $result)
	{
		echo"<tr><th>$result[ID_PROGRAM]</th>";
		echo"<th>$result[INFO]</th>";
		echo"<th><a href=\"del-segmen.php?p=$result[ID_PROGRAM]\">HAJAR</a></th></tr>";
	}
	?>
	</tbody></table>
	
<?php
}
else
{
	echo"Gak ada segmentasi yang error";
}
selesai:
mysql_close($konek);
?>
</article>

</section>

<?php
include "footer.php";
?>
