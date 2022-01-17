<h2>Parcel Hari Raya menggunakan Algoritma Genetika</h2>
<form method="post" action="<?= $_SERVER["PHP_SELF"]; ?>">

    Budget Rp. <input type='text' name="inputBudget" autofocus>
    &nbsp;
    <hr>
   <strong>Yang harus ada</strong> (Kosongkan jika tidak ada)
    <br>
    <table><tr>
    <?php 
        require 'main.php';
        $catalogue = new Catalogue;
        $raw_data = $catalogue->getAllProducts();
        // print_r($raw_data);
        $i = 1;
        foreach($raw_data as $val){ ?>
            <td>
            <input type="checkbox"  name="<?php echo preg_replace('/\s+/', '', $val['item']) ;?>" id="<?php echo preg_replace('/\s+/', '', $val['item']) ;?>">
            <label for="<?php echo $val['item'] ?>"> <?php echo $val['item'] ?></label></td>
        <?php 
            if($i % 4 == 0)
                echo "</tr><tr>";
                $i++;
            }
            echo "</tr></table>";
        ?>
   <input type="submit" name="submit" value="Submit" style="width: 50%">
    <hr>
</form>

<?php
$maxBudget = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mustHave = NULL;
    foreach($raw_data as $val){
        if(isset($_POST[preg_replace('/\s+/', '', $val['item'])]))
            $mustHave[] =  $val['item'];
    }
    // print_r($mustHave);

    $maxBudget = $_POST["inputBudget"];

    if ($maxBudget === '') {
        echo '<font color =red>Enter your budget.</font>';
        die;
    }

    $main = new Main;
    $main->maxBudget = $maxBudget;
    $main->popSize = 10;
    $main->crossoverRate = 0.8;
    $main->maxGen = 250;
    $main->selectionType = 'elitism';
    $main->stoppingValue = 100;
    $main->numOfLastResult = 10;
    $main->mustHave = $mustHave;

    $result = $main->runMain();

    if (empty($result)) {
        echo 'Optimum solution was not found. Try again, or add more budget.';
    } else {
        echo "<table>";
        echo "<tr><td>Your budget</td><td>: <b>Rp. " . number_format($main->maxBudget) . "</b></td></tr>";
        echo "<tr><td>Optimum amount</td><td>: <b>Rp. " . number_format($result['amount'])  . "</b></td></tr>";
        echo "<tr><td>Number of items</td><td>: <b> " . $result['numOfItems'] . "</b></td></tr>";
        echo "</table>";

        echo "<br>List of items: <br>";
        echo "<table><tr><td>No.</td><td>Item</td><td>Price (Rp)</td></tr>";
        
        foreach ($result['items'] as $key => $item) {
            echo "<tr><td>" . ($key + 1) . "</td><td>" . $item[0] . "</td><td  style=align:right'>" . number_format($item[1]) . "</td></tr>";
        }
        echo "</table>";
    }
}

?>