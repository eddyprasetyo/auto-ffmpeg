<!DOCTYPE html>
<html lang="en">
<title>Youtube & OTT Transcoder</title>
<meta charset="utf-8">
<script src="segmen.js"></script>
<!--[if lt IE 9]>
<script src="html5.js">
</script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="css.css">
<body>
<header>
<h1>Youtube & OTT Transcoder</h1>
</header>
<?php
include "menu.php";
?>
<section>
<h2>Preview</h2>

<article>
<?php
require "setting.php";
$pan=strlen($_POST['clipprev']);
if($pan<7)
{
	echo"Jumlah karakter kurang dari 7<br>";
	goto end1;
}
$clip=$_POST['clipprev'];
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
if(!file_exists("LowRes/$clip.mp4")){
  echo"Clip tidak ada file lowres nya<br>";
	goto end2;
}

/*
$kueri2=mssql_query("select ID from IMotion.dbo.MEDIA_INFORMATIONS
where  DeviceAlias = 'ISI_LR_ONLINE'
and ID = '$clip.mp4';");
if(!mssql_num_rows($kueri2))
{
	echo"Clip tidak ada file Previewnya<br>";
	goto end2;
}
mssql_free_result($kueri2);
*/
?>

<div style="text-align:center">
  <video id="video1" ontimeupdate="onTimeUpdate(this)">
    <source src="./LowRes/<?php echo($clip);?>.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
  </video>
  <h3><?php echo($clip);?></h3> 
    <p id="currentTimeCode" style="text-allign:center"></p> 
  <br>
  <button title="go to start" onclick="tostart()">|<</button>
  <button title="mundur 1 menit" onclick="reserve60s()"><<<</button>
  <button title="mundur 10 detik" onclick="reserve10s()"><<</button>
  <button title="mundur 1 detik" onclick="reserve1s()"><</button>
  <button title="pause" onclick="pause()">||</button>
  <button title="play" onclick="play()">|></button>
  <button title="maju 1 detik" onclick="forward1s()">></button>
  <button title="maju 10 detik" onclick="forward10s()">>></button>
  <button title="maju 1 menit" onclick="forward60s()">>>></button>
  <button title="go to end" onclick="toend()">>|</button>
<br><br>
  <button title="start segmen" id="buttontcin" onclick="tcin()" disabled>[</button>
  <button title="end segmen" id="buttontcout" onclick="tcout()" disabled>]</button>
<br><br>
  <table id="segtab">
    <th>
	  <tr>
	    <td>Start Timecode</td>
		<td>Stop Timecode</td>
		<td>Durasi</td>
		<td>Segmen</td>
	  </tr>
	</th>
  </table>
  <form action="SubMitJob2.php" method="post" name="form1" onSubmit="kirim()">
  <input type="hidden" name="clip" value="<?php echo($clip);?>"></input>
	<input type="hidden" name="insegmentasimanual"></input>
	<input type="hidden" name="dursegmentasimanual"></input>
	<button type="submit" id="buttonsegmentasimanual" disabled>Transcode</button>
  </form>
</div>
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