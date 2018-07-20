<?php
        include "header.php";
        include "menu.php";
?>
<section>
        <h2>Preview</h2>

        <article>
                <h1>Masukan Nama Clip, minimal 6 karakter</h1>
                <form action="cekpreview.php" method="post">
                <input type="text" name="clip" autofocus><br/>
                <button type='submit'>Process</button>
                </form>
        </article>
        <article>
                <h1>50 ID PG terakhir(tanpa QC dan Revisi)</h1>
                <?php
                        require "setting.php";
                        $konek=mssql_connect($invenio_db_server,$invenio_db_user,$invenio_db_password);
                        if($konek){
                                $kueri=mssql_query("select top 50 CAST(ID AS TEXT) AS ID,CAST(Title AS TEXT) AS Title from IMotion.dbo.MEDIA_INFORMATIONS
                                WHERE DeviceAlias = 'NEWS_SAN' 
                                and ID LIKE 'PG0%'  
                                and ID NOT LIKE '%REV%' 
                                and ID NOT LIKE '%SEG%' 
                                and ID NOT LIKE '%Seg%'
                                and ID NOT LIKE '%QC%'
                                order by CreatedDate desc;");
                                if(mssql_num_rows($kueri)){
                                        ?>
                                        <table><thead><tr>
                                        <th>Clip ID</th>
                                        <th>Deskripsi</th>
                                        <th></th></tr>
                                        </thead><tbody>
                                        <?php
                                        while($row=mssql_fetch_array($kueri, MSSQL_NUM)){
                                                echo"<tr><th>$row[0]</th>";
                                                echo"<th>$row[1]</th>";
                                                ?>
                                                        <th>
                                                        <form action="preview.php" method="post">
                                                        <input type="hidden" name="clipprev" value="<?php echo($row[0]);?>"></input>
                                                        <button type="submit">Preview</button></form>
                                                        </th></tr>
                                                <?php
                                        }
                                        ?>
                                        </tbody></table>
                                        <?php
                                }
                                else {
                                        echo "SQL Error dengan pesan terakhir :<br>";
                                        echo(mssql_get_last_message());
                                }
                                mssql_free_result($kueri);
                        }
                        else {
                                echo "Gagal konek ke server invenio";
                        }
                ?>
        </article>
</section>

<?php
        include "footer.php";
?>