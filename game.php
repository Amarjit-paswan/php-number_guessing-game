<?php 

//-----------------------
// Intialize a Game Class
//-----------------------

class Game{

    //Store json file
    private $file = 'game_records.json';
    
    // Intailize array to store records
    private $records = [];

    // Constructor : store reocrds in json file into memory
    public function __construct()
    {
        //Check if file exists
        if(file_exists($this->file)){
            $json = file_get_contents($this->file);
            $this->records = json_decode($json, true);
        }
    }


    //Generate number between 1 to 100 for guessing
    public function generate_number(){
      return rand(1,100);
    }

    // Show menu to user to choose difficulty level
    public function showMenu(){
        echo "\n Welcome to the Number Guessing Game!\n";
        echo " I'm thinking of number between 1 and 100.\n";
        echo " You have X chance to guess the correct number depending of difficulty level  you choose. \n";

        echo "\n Please Select the difficulty level \n";
        echo "1. Easy (10 chances) \n";
        echo "2. Medium (5 chances) \n";
        echo "3. Hard (3 chances) \n";

        echo "\n Other options: \n";
        echo "4. List high score \n";
        echo "5. Exit \n";

        echo "\n Enter Your Choice: ";
    }

    // Play Game function 
    public function playGame($option, $number, $guess, $showHints){

        // Check which difficulty level user have chosen
        $chances = ($option == 1) ? 10 : (($option == 2) ? 5 : 3);

        $lowRange = 1; // Minimum value
        $highRange = 100; // Maximum value

        $startTime = microtime(true); // Start Counting the time

        $hintUsed = 0; // Check how many hint used
        $hintGiven = []; // Store the hint

        $totalAttempts = 0; // Count total attemps of game

        // Run the loop untill chance has over
        for($i = 0; $i < $chances; $i++){

            // Guess matchs to the number
            if($guess == $number){
                $endTime = microtime(true); // Store the end time 
                $totalTime = $endTime - $startTime; // Calculate the total time

                $totalAttempts += $i; // Add the attempts

                $highScore = $this->getHighScore($option); // Store high score 

                // Check total attempts is over highest attemps
                if($highScore == null || $totalAttempts < $highScore['attempts']){
                    $this->updateHighScore($option, $totalAttempts, $totalTime);
                }

                echo "\n Congratulations! You guessed the correct number in ". ($i). "attempts. \n";
                echo "It took you ". round($totalTime, 2). " seconds\n";
                break;

            }else if($guess > $number){ // Check if guess bigger than number
                echo "\n Incorrect! The number is less than your guess $guess. Try again\n";
                $highRange = $guess - 1; // Update the high range

            }else{
                echo "\n Incorrect! The number is greater than your guess $guess. Try gain. \n";
                $lowRange = $guess + 1; // Update the low range
            }

            // Check if user want to see hint
            if($showHints == 'y' || $showHints == 'Y' || $showHints = 'yes'){
                $hintUsed++; // Increase the hint
                // Store the hint
                $hint = $this->provideHint($number, $guess, $hintUsed, $lowRange, $highRange, $hintGiven);
                echo $hint; // Print the hint
            }

            echo "\n Enter Your guess: ";
            $guess = (int) fgets(STDIN);
        }

        // Print alert message if chaces has over
        if($i == $chances){
            echo "\Sorry, you ran out of chances. The correct number was $number. \n";
        }

    }

    //Provide a hint
    public function provideHint($number,$guess,$hintUsed,$lowRange,$highRange,$hintGiven){

        // If range hint hasn't been given
        if(!in_array('range', $hintGiven)){
            echo "Hint: The number is between $lowRange and $highRange.\n";
            $hintGiven[] = 'range';
        }
        // Else if even/odd hint has not given
        elseif(!in_array('parity',$hintGiven)){
            if($number % 2 === 0){
                echo "Hint: The number is even.\n";
            }else{
                echo "Hint: The number is odd.\n";
            }
            $hintGiven[] = 'parity';
        }
        // Else if close-range hint hasn't been given
        elseif(!in_array('close',$hintGiven)){
            if(abs($guess - $number) <= 5){
                echo "Hint: You are very close!\n";
            }else{
                echo "Hint: You are far from the number. \n";
            }

            $hintGiven[] = 'close';
        }else{
            echo "No more hints avaiable. \n";
        }
        return $hintGiven;
    }

    // Get High Score of Game
    public function getHighScore($option){

        // Check Records is empty
        if(empty($this->records)){
            return null;
        }

        $bestScore = null;

        //Check level you chosed
        $difficuly = match ($option){
            1 => 'easy',
            2 => 'medium',
            3 => 'hard',
            default => null,
        };

        // Print records 
        foreach($this->records as $record){
            if($record['difficulty'] === $difficuly){
                if($bestScore === null || $record['attempts'] < $bestScore['attempts']){
                    $bestScore = $record;
                }
            }
        }

        // Print the best score
        return $bestScore ?? null;
    }

    // Save the records into JSON
    public function saveToJson($records){
        $json = json_encode($records, JSON_PRETTY_PRINT);
        file_put_contents($this->file, $json);
    }

    // Update high_score
    public function updateHighScore($option, $attempts, $time){
        $difficuly = match($option){
            1 => 'easy',
            2 => 'medium',
            3 => 'hard',
            default => null,
        };

        $updated = false;

        foreach($this->records as $key => $record){
            if($record['difficulty'] === $difficuly){
                if($attempts < $record['attempts']){
                    $this->records[$key]['attempts'] = $attempts;
                    $this->records[$key]['time'] = round($time,2);
                    $updated = true;
                }
                break;
            }
        }

        if(!$updated){
            $this->records[] = [
                'difficulty' => $difficuly,
                'attempts' => $attempts,
                'time' => round($time,2),
            ];
        }

        // Save the updated value
        $this->saveToJson($this->records);
    }

    // Show high score
    public function showHighScore(){
        if(empty($this->records)){
            return "No high scores found. \n";
        }

        $scores = [
            'Easy' => $this->getHighScore(1),
            'Medium' => $this->getHighScore(2),
            'Hard' => $this->getHighScore(3),
        ];

        echo str_repeat("=",30). "\n";
        echo "      HIGH SCORES     \n";
        echo str_repeat("=",30). "\n";

        foreach($scores as $difficulty => $score){
            if($score == null){
                echo "$difficulty: No high score yet. \n";
            }else{
                echo "$difficulty: Attempts: {$score['attempts']} | Time: {$score['time']} seconds\n";
            }
        }

        echo str_repeat("=",30). "\n";
    }



}



?>