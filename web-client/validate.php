<?php
include "header.php";
include "menu.php";
?>
<section>
<h2>Add Job</h2>

<article>
<?php
require "setting.php";
$pan=strlen($_POST['clip']);
if($pan<7)
{
	echo"Jumlah karakter kurang dari 7<br>";
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
where  DeviceAlias = 'NEWS_SAN'
and ID = '$clip';");
if(!mssql_num_rows($kueri))
{
	echo"Clip tidak ada di Mediabase<br>";
	goto end2;
}
mssql_free_result($kueri);
$kueri2=mssql_query("select * from hss_inv1.dbo.GetSegmentTimecodes('$clip','NEWS_SAN');");
if(mssql_num_rows($kueri2)>1)
{
	echo"Clip ada di Mediabase dan ada segmentasinya<br>Untuk ID $clip Silahkan Pilih :<br>";
	?>
	<form action="SubmitJob.php" method="post">
	 <input type="radio" name="idjob" value="1" checked>Single ID Single Segment<br>
	 <input type="radio" name="idjob" value="2">Single ID Multi Segment<br>
 	 <input type="radio" name="idjob" value="3">Just Transcode it<br><br>
 	 <input type="hidden" value="<?php echo($clip); ?>" name="clip">
	  <button type="submit">Transcode</button>
	</form> 		
	<?php
}
else if (mssql_num_rows($kueri2)==1)
{
	echo"Clip ada di Mediabase tetapi segmentasi hanya satu :<br>";
	?>
	<form action="SubmitJob.php" method="post">
		 <input type="radio" name="idjob" value="3" checked>Transcode aja<br><br>
		 <input type="hidden" value="<?php echo($clip); ?>" name="clip">
	  <button type="submit">Transcode</button>
	</form> 
	<?php
}
else
{
	echo"Segmentasi tidak ditemukan<br>";
	?>
	<form action="SubmitJob.php" method="post">
		 <input type="radio" name="idjob" value="3" checked>Just Transcode it<br><br>
		 <input type="hidden" value="<?php echo($clip); ?>" name="clip">
	  <button type="submit">Transcode</button>
	</form> 
	<?php
}
mssql_free_result($kueri2);
end2:
mssql_close($konek);
end1:
?>
</article>

</section>

<?php
include "footer.php";
?>