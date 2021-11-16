<?php

    //Static class in Java
    class Parameters{
        const FILE_NAME = 'products.txt';
        CONST COLUMNS = ['item', 'price'];
        const POPULATION_SIZE = 5;
        const BUDGET = 5000;
        const STOPPING_VALUE = 500;
        const CROSSOVER_RATE = 0.8;
    }

    class Catalogue{
        private function createProductColumn($listOfRawProducts){
            foreach(array_keys($listOfRawProducts) as $listOfRawProductKey){
                // change index name
                $listOfRawProducts[Parameters::COLUMNS[$listOfRawProductKey]] = $listOfRawProducts[$listOfRawProductKey];
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
        //Generate random number between 0 and 1
        //(Call function create random individu)
        //Then insert it into new array
        function createRandomPopulation(){
            $individu = new Individu;
            for($i=0; $i<Parameters::POPULATION_SIZE; $i++){
                $ret[] = $individu->createRandomIndividu();
            }
            return $ret;
        }
    }

    class Fitness{
        //Count the price of the selected item only
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
            // print_r($countedMaxItems);
            // echo '<br>';
            $maxItem =  max(array_keys($countedMaxItems));
            // echo '<br>';
            // echo $countedMaxItems[$maxItem];
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
                echo '<br>Max Item : ' . $numberOfSelectedItem;
                echo '<br>Fitness Value :' . $fitnessValue . "<br>";
                if($this->isFit($fitnessValue)){
                    echo ' (Fit)<br>';
                    //Collect the fits
                    $fits[] = [
                        'selectedIndividuKey' => $listOfIndividuKey,
                        'numberOfSelectedItem' => $numberOfSelectedItem,
                        'fitnessValue' =>  $fitnessValue
                    ];
                    // print_r($fits);
                }
                else
                    echo ' (Not Fit)<br>';
            }
            if($this->isFound($fits)){
                echo '<br>Found<br>';
            } else{
                echo '<br> >> Next Generation<br>';
            }
        }
    }

    class Crossover{
        public $population;

        function __construct($population)
        {
            $this->population = $population;
        }

        function randomZeroToOne(){
            return (float) rand() / (float) getrandmax();
        }

        function generateCrossover(){
            for ($i = 0; $i<Parameters::POPULATION_SIZE; $i++){
                $randomZeroToOne = $this->randomZeroToOne();
                if($randomZeroToOne < Parameters::CROSSOVER_RATE){
                    $parents[$i] = $randomZeroToOne;
                }
            }
            foreach(array_keys($parents) as $key){
                foreach(array_keys($parents) as $subkey){
                    if($key !== $subkey){
                        $ret[] = [$key, $subkey];
                    }
                }
                array_shift($parents);
            }
            return $ret;
        }

        function offspring($parent1, $parent2, $cutPointIndex, $offspring){
            $lengthOfGen = new Individu;
            if($offspring === 1){
                for($i=0; $i<$lengthOfGen->countNumberOfGen(); $i++){
                    if($i <= $cutPointIndex)
                        $ret[] = $parent1[$i];
                    if($i > $cutPointIndex){
                        $ret[] = $parent2[$i];
                    }
                }
            }
            if($offspring === 2){
                for($i=0; $i<$lengthOfGen->countNumberOfGen(); $i++){
                    if($i <= $cutPointIndex)
                        $ret[] = $parent2[$i];
                    if($i > $cutPointIndex){
                        $ret[] = $parent1[$i];
                    }
                }
            }
            return $ret;
        }

        //Generate random number below number of gen
        function cutPointRandom(){
            $lengthOfGen = new Individu;
            return rand(0, $lengthOfGen->countNumberOfGen()-1);
        }

        function crossover(){
            //Used as cutpoint
            $cutPointIndex = $this->cutPointRandom();
            echo '<br><br>Cut Point Index : ' . $cutPointIndex;
            
            foreach($this->generateCrossover() as $listOfCrossover){
                $parents1 = $this->population[$listOfCrossover[0]];
                $parents2 = $this->population[$listOfCrossover[1]];
                echo '<br><br>Parents : <br>';
                foreach($parents1 as $gen){
                    echo $gen;
                }
                echo ' >< ';
                foreach($parents2 as $gen){
                    echo $gen;
                }
                echo '<br>';
                echo 'Offspring<br>';
                $offspring1 = $this->offspring($parents1, $parents2, $cutPointIndex, 1);
                $offspring2 = $this->offspring($parents1, $parents2, $cutPointIndex, 2);
                foreach($offspring1 as $gen){
                    echo $gen;
                }
                echo ' >< ';
                foreach($offspring2 as $gen){
                    echo $gen;
                }
                $offsprings[] = $offspring1;
                $offsprings[] = $offspring2;
            }
            return $offsprings;
        }
    }

    class Randomizer{
        static function getRandomIndexOfGen(){
            return rand(0, (new Individu())->countNumberOfGen());
        }

        static function getRandomIndexOfIndividu(){
            return rand(0, Parameters::POPULATION_SIZE-1);
        }
    }
    class Mutation{
        function __construct($population)
        {
            $this->population = $population;
        }

        function calculateMutationRate(){
            return 0.2;
        }

        function calculateMutation(){
            return round($this->calculateMutationRate() * Parameters::POPULATION_SIZE);
        }

        function isMutation(){
            if($this->calculateMutation() > 0){
                return true;
            }
        }

        function generateMutation($valueOfGen){
            if($valueOfGen === 0 )
                return 1;
            else
                return 0;
        }
        function mutation(){
            // echo $this->calculateMutation();
            if($this->isMutation()){
                for($i=0; $i<$this->calculateMutation(); $i++){
                    $indexOfIndividu = Randomizer::getRandomIndexOfIndividu();
                    $indexOfGen = Randomizer::getRandomIndexOfGen();
                    $selectedIndividu = $this->population[$indexOfIndividu];

                    echo "Before Mutation <br>";
                    print_r($selectedIndividu);
                    echo "<br>";

                    $valueOfGen = $selectedIndividu[$indexOfGen];
                    $mutatedGen = $this->generateMutation($valueOfGen);

                    $selectedIndividu[$indexOfGen] = $mutatedGen;
                    $ret[] = $selectedIndividu;
                    
                    echo "After Mutation <br>";
                    print_r($selectedIndividu);
                    echo "<br>";
                }
                return $ret;
            }
        }
    }

    class Selection{
        function __construct($population, $combinedOffsprings){
            $this->population = $population;
            $this->combinedOffsprings = $combinedOffsprings;
        }

        function createTemporaryPopulation(){
            echo "base population : " . count($this->population).'&nbsp;';
            foreach($this->combinedOffsprings as $offspring){
                $this->population[] = $offspring;
            }
            echo 'offspring : ' . count($this->combinedOffsprings) . ' Temporary : ' . count($this->population) . '<br>';
            return $this->population;
        }

        function getVariableValue($basePopulation, $fitTemporaryPopulation){
            foreach($fitTemporaryPopulation as $val){
                $ret[] = $basePopulation[$val[1]];
            }
            return $ret;
        }

        function sortFitTemporaryPopulation(){
            $tempPopulation = $this->createTemporaryPopulation();
            $fitness = new Fitness();
            foreach($tempPopulation as $key => $individu){
                $fitnessValue = $fitness->calculateFitnessValue($individu);
                if($fitness->isFit($fitnessValue)){
                    echo $fitnessValue . " " . $key .  " <br>";
                    $fitTemporaryPopulation[] = [
                        $fitnessValue,
                        $key
                    ];
                }
            }
            rsort($fitTemporaryPopulation);
            $fitTemporaryPopulation = array_slice($fitTemporaryPopulation, 0 , Parameters::POPULATION_SIZE);
            // echo '<pre> Tes : ' .  count($fitTemporaryPopulation);
            // print_r($fitTemporaryPopulation);
            return $this->getVariableValue($tempPopulation, $fitTemporaryPopulation);
            // foreach($fitTemporaryPopulation as $val){
            //     print_r($val);
            // }
        }

        function selectingIndividu(){
            $selected = $this->sortFitTemporaryPopulation();
            echo "<pre>";
            echo "Individu terpilih<br>";
            print_r($selected);
            echo "</pre>";
            // print_r($this->createTemporaryPopulation());
        }
    }

    for($i=1; $i<=3; $i++){
        echo "Populasi - " . $i;
        $initialPopulation = new Population;
        echo "<h5>Pembangkitan Populasi, Hitung Fitness Value</h5>";
        $population = $initialPopulation->createRandomPopulation();
        
        $fitness = new Fitness;
        $fitness->fitnessEvaluation($population);
        echo '<br><br>';
        echo "Crossover";        
        $crossover = new Crossover($population);
        $crossoverOffsprings = $crossover->crossover();

        echo '<br><br>Crossover Offsprings : <br>';
        print_r($crossoverOffsprings);
        echo "<br><br><br>";

        $mutation = new Mutation($population);
        if($mutation->mutation()){
            $mutationOffSprings = $mutation->mutation();
            echo "<br>Mutation offspring<br>";
            print_r($mutationOffSprings);
            echo "<br>";
            foreach($mutationOffSprings as $mutationOffspring){
                $crossoverOffsprings[] = $mutationOffspring;
            }
        }
        
        $selection = new Selection($population, $crossoverOffsprings);
        $selection->selectingIndividu();
    }
?>