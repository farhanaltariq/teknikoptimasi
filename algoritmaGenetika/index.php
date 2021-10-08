<?php

    class Parameters{
        const FILE_NAME = 'products.txt';
        CONST COLUMNS = ['item', 'price'];
        const POPULATION_SIZE = 10;
        const BUDGET = 280000;
        const STOPPING_VALUE = 10000;
    }

    class Catalogue{
        private function createProductColumn($listOfRawProducts){
            foreach(array_keys($listOfRawProducts) as $listOfRawProductKey){
                // change index name
                $listOfRawProducts[parameters::COLUMNS[$listOfRawProductKey]] = $listOfRawProducts[$listOfRawProductKey];
                // clear list
                unset($listOfRawProducts[$listOfRawProductKey]);
            }
            return $listOfRawProducts;
        }

        function product(){
            $collectionOfListProducts = [];

            $raw_data = file(Parameters::FILE_NAME);
            
            // Split name and price index
            foreach($raw_data as $listOfRawProducts){
                $collectionOfListProducts[] = $this->createProductColumn(explode(',', $listOfRawProducts));
            }

            return $collectionOfListProducts;
        }
    }

    class Individu{
        //Check the total number of data
        function countNumberOfGen(){
            $catalogue = new Catalogue;
            return count($catalogue->product());
        }
        
        //Generate chromosome (Random number between 0 and 1)
        function createRandomIndividu(){
            for($i=0; $i<=$this->countNumberOfGen()-1; $i++){
                $ret[] = rand(0, 1);
            }
            return $ret;
        }
    }

    class Population{
        function createRandomPopulation(){
            $individu = new Individu;
            for($i=0; $i<=Parameters::POPULATION_SIZE; $i++){
                $ret[] = $individu->createRandomIndividu();
            }
            return $ret;
        }
    }

    class Fitness{
        function selectingItem($individu){
            $catalogue = new Catalogue;
            foreach($individu as $individuKey => $binaryGen){
                if($binaryGen == 1){
                    $ret[] = [
                        'selectedKey' => $individuKey,
                        'selectedPrice' => $catalogue->product()[$individuKey]['price']
                    ];
                }
            }
            return $ret;
        }

        //Count the selected product price
        function calculateFitnessValue($individu){
            return array_sum(array_column($this->selectingItem($individu), 'selectedPrice'));
        }

        //Count selected item
        function countSelectedItem($individu){
            return count($this->selectingItem($individu));
        }

        //Check if total selected price is less or equal than budget
        function isFit($fitnessValue){
            if($fitnessValue <= Parameters::BUDGET)
                return true;
        }

        function searchBestIndividu($fits, $maxItem, $numberOfIndividuMaxItem){
            if($numberOfIndividuMaxItem == 1){
                $index = array_search($maxItem, array_column($fits, 'numberOfSelectedItem'));
                echo '<br>';
                return $fits[$index];
                echo '<br>';
            } else{
                foreach($fits as $key => $val){
                    if($val['numberOfSelectedItem'] === $maxItem){
                        echo $key . ' ' . $val['fitnessValue'] . '<br>';
                        $ret[] = [
                            'individuKey' => $key,
                            'fitnessValue' => $val['fitnessValue']
                        ];
                    }
                }
                if(count(array_unique(array_column($ret, 'fitnessValue'))) === 1){
                    $index = rand(0, count($ret) - 1);
                } else{
                    $max = max(array_column($ret, 'fitnessValue'));
                    $index = array_search($max, array_column($ret, 'fitnessValue'));
                }
                echo 'Hasil ';
                return $ret[$index];
            }
        }

        function isFound($fits){
            $countedMaxItems = array_count_values(array_column($fits, 'numberOfSelectedItem'));
            print_r($countedMaxItems);
            echo '<br>';
            $maxItem =  max(array_keys($countedMaxItems));
            echo '<br>';
            echo $countedMaxItems[$maxItem];
            $numberOfIndividuMaxItem = $countedMaxItems[$maxItem];

            $bestFitnessValue = $this->searchBestIndividu($fits, $maxItem, $numberOfIndividuMaxItem)['fitnessValue'];
            echo '<br>Best fitness value : ' . $bestFitnessValue;

            $residual = Parameters::BUDGET - $bestFitnessValue;
            echo '<br>Residual : ' . $residual;
            if($residual <= Parameters::STOPPING_VALUE && $residual >= 0){
                return true;
            }
        }

        function fitnessEvaluation($population){
            $catalogue = new Catalogue;
            foreach($population as $listOfIndividuKey => $listOfIndividu){
                echo '<br>Individu-'.$listOfIndividuKey.'<br>';
                foreach($listOfIndividu as $individuKey => $binaryGen){
                     echo $binaryGen.'&nbsp;&nbsp;';
                    print_r($catalogue->product()[$individuKey]);
                    echo '<br>';
                }
                $fitnessValue = $this->calculateFitnessValue($listOfIndividu);
                $numberOfSelectedItem = $this->countSelectedItem($listOfIndividu);
                echo 'Max Item : ' . $numberOfSelectedItem . '<br>';
                echo 'Fitness Value :' . $fitnessValue . '<br>';
                if($this->isFit($fitnessValue)){
                    echo '(Fit)<br>';
                    //Collect the fits
                    $fits[] = [
                        'selectedIndividuKey' => $listOfIndividuKey,
                        'numberOfSelectedItem' => $numberOfSelectedItem,
                        'fitnessValue' =>  $fitnessValue
                    ];
                    // print_r($fits);
                }
                else
                    echo '(Not Fit)<br>';
            }
            if($this->isFound($fits)){
                echo '<br>Found';
            } else{
                echo '<br> >> Next Generation';
            }
        }
    }

    $initialPopulation = new Population;
    $population = $initialPopulation->createRandomPopulation();
    $fitness = new Fitness;
    $fitness->fitnessEvaluation($population);
    // $individu = new Individu;
    // print_r($individu->createRandomIndividu());
?>