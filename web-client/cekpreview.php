<?php
include "header.php";
include "menu.php";
?>
<section>
<h2>Preview</h2>

<article>
<?php
require "setting.php";
$pan=strlen($_POST['clip']);
if($pan<6)
{
	echo"Jumlah karakter kurang dari 6<br>";
	goto end1;
}
$clip=$_POST['clip'];
$konek=mssql_connect($invenio_db_server,$invenio_db_user,$invenio_db_password);
if(!$konek)
{
	echo"gagal konek ke server invenio<br>";
	goto end1;
}
$kueri=mssql_query("select ID from IMotion.dbo.MEDIA_INFORMATIONS
where  DeviceAlias = 'NEWS_SAN' and ID LIKE '$clip%';");
if(!mssql_num_rows($kueri))
{
	echo"Clip tidak ada di Mediabase<br>";
	goto end2;
}
mssql_free_result($kueri);

$kueri2=mssql_query("select ID, Title from IMotion.dbo.MEDIA_INFORMATIONS
where  DeviceAlias = 'ISI_LR_ONLINE' and ID LIKE '$clip%.mp4';");
if(!mssql_num_rows($kueri2))
{
	echo"Clip tidak ada file Previewnya<br>";
	goto end2;
}
$ambil=array();
while($hasil=mssql_fetch_array($kueri2,MSSQL_ASSOC))
{
	array_push($ambil,$hasil);
}
mssql_free_result($kueri2);
?>
<table><thead><tr>
<th>Clip ID</th>
<th>Title</th>
<th>Action</th>
</thead><tbody>
<?php
foreach($ambil as $key => $result)
{
	$IDWoExt=substr($result[ID],0,-4);
	echo"<tr><th>$IDWoExt</th>";
	echo"<th>$result[Title]</th>";
	?>
	<th>
	<form action="preview.php" method="post">
	<input type="hidden" name="clipprev" value="<?php echo($IDWoExt);?>"></input>
	<button type="submit">Preview</button></form>
	</th></tr>
	<?php
}
?>
</tbody></table>
<?php
end2:
mssql_close($konek);
end1:
?>
</article>

</section>

<?php
include "footer.php";
?>