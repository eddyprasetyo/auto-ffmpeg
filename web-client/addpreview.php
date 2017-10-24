<?php
include "header.php";
include "menu.php";
?>
<section>
<h2>Preview</h2>

<article>
<form action="cekpreview.php" method="post">
        <label>Nama Clip, minimal 6 karakter</label><br><input type="text" name="clip" autofocus><br/>
	         <button type='submit'>Process</button>
</form>
</article>

</section>

<?php
include "footer.php";
?>