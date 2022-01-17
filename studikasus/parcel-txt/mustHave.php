<?php
    $mustHave = NULL;
    $raw_data = file('products.txt');
    foreach($raw_data as $val){
        if(isset($_POST[preg_replace('/\s+/', '', explode(',', $val)[0])]))
            $mustHave[] =  explode(',', $val)[0];
    }
    print_r($mustHave);
?>