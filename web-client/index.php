<?php
include "header.php";
include "menu.php";
?>
<section id="process">
<h2>Process yang sedang dilakukan</h2>

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
$kueri=mysql_query("SELECT ID_PROGRAM,STEP,PROGRESS_NAME,NUMBER_PROGRESS FROM TB_PROCESS WHERE ID_PROGRAM IN (SELECT ID_PROGRAM FROM TB_QUEUE WHERE ID_STATUS = '2') ORDER BY ID_PROGRAM,STEP;");
$ambil=array();
while($hasil=mysql_fetch_array($kueri,MSSQL_ASSOC))
{
	array_push($ambil,$hasil);
}
mysql_free_result($kueri);
if ($ambil)
{	?>
	<table><thead><tr>
	<th>CLIP ID</th>
	<th>STEP</th>
	<th>JOB</th>
	<th>PROGRESS</th></tr>
	</thead><tbody>
	<?php
	$clip="";
	foreach($ambil as $key => $result)
	{
		if($result[ID_PROGRAM]!=$clip)
		{
			echo"<tr><th>$result[ID_PROGRAM]</th>";
			$clip=$result[ID_PROGRAM];
		}
		else echo"<tr><th></th>";
		echo"<th>$result[STEP]</th>";
		echo"<th>$result[PROGRESS_NAME]</th>";
		echo"<th>$result[NUMBER_PROGRESS]</th></tr>";
	}
	?>
	</tbody></table>
	
<?php
}
else
{
	echo"Sedang tidak ada process";
}
selesai:
mysql_close($konek);
?>


</section>

<?php
include "footer.php";
?>