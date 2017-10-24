<?php
include "header.php";
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
$kueri2=mssql_query("select ID from IMotion.dbo.MEDIA_INFORMATIONS
where  DeviceAlias = 'ISI_LR_ONLINE'
and ID = '$clip.mp4';");
if(!mssql_num_rows($kueri2))
{
	echo"Clip tidak ada file Previewnya<br>";
	goto end2;
}
mssql_free_result($kueri2);
?>
<script src="segmen.js"></script>
<div style="text-align:center">
  <video id="video1" controls>
    <source src="./LowRes/<?php echo($clip);?>.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
  </video>
  <h3><?php echo($clip);?></h3>
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