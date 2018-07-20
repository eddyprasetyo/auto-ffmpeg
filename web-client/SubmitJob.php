<?php
include "header.php";
include "menu.php";
?>
<section>
<h2>Add Job</h2>
<article>
<?php
//echo($_POST['idjob']);
//echo"<br>";
//echo($_POST['clip']);
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
$clip=$_POST['clip'];
$idjob=$_POST['idjob'];
mysql_query("CALL INSERT_JOB('$clip','$idjob',@A);");
$ambil=mysql_fetch_array(mysql_query("SELECT @A AS MESSAGE;"));
echo($ambil['MESSAGE']);
selesai:
mysql_close($konek);
?>
</article>

</section>

<?php
include "footer.php";
?>