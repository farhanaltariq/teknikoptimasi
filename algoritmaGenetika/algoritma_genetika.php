<?php
    class Catalogue{
        function createProductColumn($columns, $listOfRawProducts){
            foreach(array_keys($listOfRawProducts) as $listOfRawProductKey){
                $listOfRawProducts[$columns[$listOfRawProductKey]] = $listOfRawProducts[$listOfRawProductKey];
                unset($listOfRawProducts[$listOfRawProductKey]);
            }
            return $listOfRawProducts;
        }

        function product($parameter){
            $collectionOfListProducts = [];

            $raw_data = file($parameter['file_name']);
            foreach($raw_data as $listOfRawProducts){
                $collectionOfListProducts[] = $this->createProductColumn($parameter['columns'], explode(',', $listOfRawProducts));
            }

            foreach($collectionOfListProducts as $listOfRawProduct){
                print_r($listOfRawProduct);
                echo '<br>';
            }

            return $collectionOfListProducts;
        }
    }

    $parameter = [
        'file_name' => 'products.txt',
        'columns' => ['item', 'price']
    ];

    $Katalog = new Catalogue;
    $Katalog->product($parameter);
?>