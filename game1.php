<?php 

//----------------------
//Intailize a game class 
//----------------------

class Game{

    // Store the file
    private $file = 'game_records.json';

    // Store the records
    private $records = [];

    public function __construct()
    {
        // Check file exists
        if(file_exists($this->file)){
            $json = file_get_contents($this->file);
            $records = json_decode($json,true);

        }
    }

    // Show the menu
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

    // Generate the number
    public function generate_number(){
        $number = rand(1,100);
        return $number;
    }

    // Play the game
    public function playGame($option,$number,$guess,$showHints){

        // Check the difficulty level to find chances
        $chances = ($option == 1) ? 10 : (($option == 2) ? 5 : 3);

        $lowRanges = 1; // Mininum range
        $highRanges = 100; // Maximum range

        // Start the time
        $startTime = microtime(true);

        $hintUsed = 0; // hint used

        $hintsGiven = []; // Hints given

        $totalAttempts = 0; // Count total attempts

        // Run the loop untill the chance over
        for($i = 0; $i < $chances; $i++){

            // Guess matches to the number
            if($guess == $number){

                $endTime = microtime(true); // End the time

                $totalTime = $endTime - $startTime; // Total time
                $totalAttempts += $i; // Add the attempts

                $highScores = $this->getHighScore($option); // Store high score

                // Check total attemps is over highest attempts
                if($highScores == null || $totalAttempts < $highScores['attempts']){
                    $this->updateHighScore($option, $totalAttempts, $totalTime);
                }

                echo "\n Congrastulation! You guessed the correct number in ($i) attempts\n";
                echo "\n It took around ". round($totalTime,2). " seconds";
                break;
            }else if($guess > $number){ // Check if guess number is bigger than number
                echo "Incorrect! The number is less than your $guess . Try again \n";
                $highRanges = $guess - 1;
            }else{ // Check if guess number is smaller than number
                echo "Incorrect! The number is bigger than your $guess . Try again \n";
                $lowRanges = $guess + 1;
            }

            // Check if user want to see the hint
            if($showHints == 'y' || $showHints == 'Y' || $showHints == 'yes'){
                $hintUsed++; // Increse the hint
                // Store the unit
                $hint = $this->provideHint($number,$guess,$hintUsed,$lowRanges,$highRanges,$hintsGiven);
                echo $hint; // Print the hint 
            }

            echo "\n Enter the guess: \n";
            $guess = (int) fgets(STDIN);

            // Print the alert message if chance has over
            if($i == $chances){
                echo "\n Sorry, you ran out of chances. The correct number was $number. \n";
            }
        }
    }

    // Provide the hint
    public function provideHint($number, $guess, $hintUsed, $lowRanges, $highRanges, &$hintsGiven){
      // If range hint hasn't been given
      if(!in_array('range',$hintsGiven)){
        echo "Hint: The number is between $lowRanges and $highRanges. \n";
        $hintsGiven[] = 'range';
      }
      // Else if even/odd hint hasn't been given
      elseif (!in_array('parity',$hintsGiven)){
            if($number % 2 === 0){
                echo "Hint: The number is even. \n";
            }else{
                echo "Hint: The number is odd. \n";
            }
            $hintsGiven[] = 'party';
      }
      //Else if close-range hint hasn't been given
      elseif(!in_array('close',$hintsGiven)){
        if(abs($guess - $number) <= 5){
            echo "Hint: You are very close!\n";
        }else{
            echo "Hint: You are far from the number. \n";
        }
        $hintsGiven[] = 'close';
      }else{
        echo "No more hints available. \n";
      }

      return $hintsGiven;


    }

}




?>