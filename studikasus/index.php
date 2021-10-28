<?php

use Parameters as GlobalParameters;

class Parameters{
        const FILE_NAME  = ['mainboards', 'ram', 'vga', 'storage', 'processor'];
        const COLUMNS = ['item', 'price'];
        const POPULATION_SIZE = 10;
}

class Catalogue{
    //Change index name
    function createProductColumn($listOfData){
        foreach(array_keys($listOfData) as $listOfDataKeys){
            $listOfData[GlobalParameters::COLUMNS[$listOfDataKeys]] = $listOfData[$listOfDataKeys];
            unset($listOfData[$listOfDataKeys]);
        }
        // print_r($listOfData);
        return $listOfData;
    }

    //Assign each data to array
    function product(){
        $products = [];

        foreach(GlobalParameters::FILE_NAME as $data){
            foreach(file($data.".txt") as $listOfData){
                // echo $listOfData;
                $products[] = $this->createProductColumn(explode(',', $listOfData));
            }
        }
        return $products;
    }
}

class Individu{
    //Count total number of each product
    function countNumberOfGen($arrKey){
        foreach(GlobalParameters::FILE_NAME as $key){
            $ret[$key] = count(file($key.".txt")); 
        }
        return $ret[$arrKey];
    }
    function createRandomIndividu(){
        foreach(GlobalParameters::FILE_NAME as $key){
            $ret[$key] = rand(0, $this->countNumberOfGen($key));
        }
        return $ret;
    }
}

class Population{
    function createRandomPopulation(){
        $individu = new Individu;
        for($i=0; $i<GlobalParameters::POPULATION_SIZE; $i++){
                $ret[] = $individu->createRandomIndividu();
        }
        foreach($ret as $key => $val){
            echo "<br><br>Population " . $key . "<br>";
            print_r($val);
        }
        return $ret;
    }
}



$tes = new Catalogue;
$tes->product();
// echo "Total Gen : " . Individu::countNumberOfGen("mainboards");
$pop = new Population;
$pop->createRandomPopulation();
?>