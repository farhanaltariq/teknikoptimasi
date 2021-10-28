<?php

use Parameters as GlobalParameters;

class Parameters{
        const FILE_NAME  = ['mainboards.txt', 'ram.txt', 'vga.txt', 'storage.txt', 'processor.txt'];
        const COLUMNS = ['item', 'price'];
}

class Catalogue{
    //Change index name
    function createProductColumn($listOfData){
        foreach(array_keys($listOfData) as $listOfDataKeys){
            $listOfData[GlobalParameters::COLUMNS[$listOfDataKeys]] = $listOfData[$listOfDataKeys];
            unset($listOfData[$listOfDataKeys]);
        }
        return $listOfData;
    }

    //Assert each data to array
    function product(){
        $products = [];

        foreach(GlobalParameters::FILE_NAME as $data){
            $datas = file($data);
            echo $data;
            if(!empty($datas))
                foreach($datas as $listOfData){
                    echo $listOfData;
                    $products[] = $this->createProductColumn(explode(',', $listOfData));
                }
        }
        return $products;
    }
}

class Individu{
    //Count total number
    public static function countNumberOfGen(){
        $catalogue = new Catalogue;
        return count($catalogue->product());
    }
}

$tes = new Catalogue;
$tes->product();
echo "Total Gen : " . Individu::countNumberOfGen();
?>