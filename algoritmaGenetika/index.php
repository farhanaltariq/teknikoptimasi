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
                'product' => $collectionOfListProducts,
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
            $total = 0;
            for($h=0; $h<$parameter['population_size']; $h++){
                echo '<h4>Population : ' . $h+1 . '<h4><table><td>';
                for($i=0; $i<$gen_length; $i++){
                    if($gen[$h][$i] == 1)
                        $total += $itemList['product'][$i]['price'];
                    echo '<tr><td>' . $itemList['product'][$i]['item'] . '</td><td>' . $itemList['product'][$i]['price'] . '</td><td>' .  $gen[$h][$i] . '</tr>';
                }
                echo '<tr><td><strong>Total Price</td><td>' . $total . '</strong></td></tr>';
                echo '</td></table><hr>';
            }
        }
    }

    $parameter = [
        'file_name' => 'products.txt',
        'columns' => ['item', 'price'],
        'population_size' => 10
    ];

    $initialPopulation = new PopulationGenerator;
    $initialPopulation->createData($parameter);
?>