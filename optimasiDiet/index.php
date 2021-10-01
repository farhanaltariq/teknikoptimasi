<?php
    class Catalogue{
        private function createProductColumn($columns, $listOfRawProducts){
            foreach(array_keys($listOfRawProducts) as $listOfRawProductKey){
                // change index name
                $listOfRawProducts[$columns[$listOfRawProductKey]] = $listOfRawProducts[$listOfRawProductKey];
                // clear list
                unset($listOfRawProducts[$listOfRawProductKey]);
            }
            
            return $listOfRawProducts;
        }

        function product($parameter){
            $collectionOfListProducts = [];

            $raw_data = file($parameter['file_name']);
            
            // Split name and price index
            foreach($raw_data as $listOfRawProducts){
                $collectionOfListProducts[] = $this->createProductColumn($parameter['columns'], explode(',', $listOfRawProducts));
            }

            return [
                'foods' => $collectionOfListProducts,
                'gen_length' => count($collectionOfListProducts),
            ];
        }
    }

    class PopulationGenerator{
        // Generate random between 0 and 1 for each product
        private function createIndividu($parameter){
            $catalogue = new Catalogue;
            $gen_length = $catalogue->product($parameter)['gen_length']; 
            for($i=0; $i<$gen_length; $i++){
                $ret[] = rand(0, 1);
            }
            return $ret;
        }

        function createPopulation($parameter){
            for($i=0; $i<$parameter['population_size']; $i++){
                $ret[] = $this->createIndividu($parameter);
            }
            return $ret;
        }

        public function createData($parameter){
            $catalogue = new Catalogue;
            $itemList = $catalogue->product($parameter);
            $gen = $this->createPopulation($parameter);
            $gen_length = $catalogue->product($parameter)['gen_length'];
            // Print output result
            for($h=0; $h<$parameter['population_size']; $h++){
                echo '<h4>Population : ' . $h+1 . '<h4><table>';
                echo '<th>Food</th><th>Protein</th><th>Calories</th><th>Fat</th><th>Fiber</th><th>Chromosome</th>';
                for($i=0; $i<$gen_length; $i++){
                    echo '<tr><td>' . $itemList['foods'][$i]['food'] . '</td><td>' . 
                          $itemList['foods'][$i]['protein'] . '</td><td>' .  
                          $itemList['foods'][$i]['calories'] . '</td><td>' .  
                          $itemList['foods'][$i]['fat'] . '</td><td>' .  
                          $itemList['foods'][$i]['fiber'] . '</td><td>' .  
                          $gen[$h][$i] . '</tr>';
                }
                echo '</table>';
            }
        }
    }

    $parameter = [
        'file_name' => 'foods.txt',
        'columns' => ['food', 'protein', 'calories', 'fat', 'fiber'],
        'population_size' => 10
    ];

    $initialPopulation = new PopulationGenerator;
    $initialPopulation->createData($parameter);
?>

<html>
    <style>
        table td, table th{
            border-right: 1px solid black;
            text-align: left;
        }
    </style>
</html>