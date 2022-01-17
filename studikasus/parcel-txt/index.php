<?php
    $raw_data = file('products.txt');
    echo "<h4>Parcel Hari Raya Menggunakan Algoritma Genetika</h4> <hr>";
?>
<html>
    <form action="mustHave.php" method="POST">
        <label for="budget">Budget Rp. </label>
        <input type="number" name="budget" placeholder="Budget">
        <hr>
        <strong>Yang harus ada</strong>
        (Kosongkan jika tidak ada)
        <br>
        <table><tr>

        <?php 
            $i = 1;
            foreach($raw_data as $listOfRawProducts){ ?>
                <td>
                <input type="checkbox"  name="<?php echo preg_replace('/\s+/', '', explode(',', $listOfRawProducts)[0]) ;?>">
                <label for="<?php echo explode(',', $listOfRawProducts)[0] ?>"> <?php echo explode(',', $listOfRawProducts)[0] ?></label></td>
            <?php 
                if($i % 4 == 0)
                    echo "</tr><tr>";
                    $i++;
                }
                echo "</tr></table>";
            ?>
        <br>
        <input type="submit" value="Cari">
    </form>
</html>