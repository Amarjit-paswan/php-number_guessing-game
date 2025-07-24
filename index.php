<?php 

// Include the game logic from a another file
require_once('game.php');

// Create a new instance of the Game Class
$game = new Game();

// Start a loop that continues untill the user chooses to exit
do{
    // Show the main game menu (option 1 to 5)
    $game->showMenu();

    // Read user input form the terminal and cast it to integer
    $option = (int) fgets(STDIN);

    // If the user selects the option 5 -> Exit the game
    if($option == 5){
        echo "Game has ended successfully";
        break;
    }

    // If the user selects the option 4 -> Show high scores
    if($option == 4){
        $game->showHighScore();

        // Wait for any key to be processed before returning to menu
        echo " Do you want to go to menu?";
        fgets(STDIN);
        continue; // Go back to start of the loop;
    }

    // Handle options 1 (Easy), 2 (Medium), 3 (Hard)
    $output = match($option){
        1 => "Great! You have selected the Easy difficulty level.",
        2 => "Great! You have selected the Medium difficulty level.",
        3 => "Great! You have selected the Hard difficulty level.",
        default => 'Invalid option. Please try again',
    };

    // Show the selected difficulty level or error message
    echo "\n". $output . "\n". "Let's start the game!\n";

    // Ask the player if they want to use hints
    echo "\n(Show hints? (y/n)): ";
    $showHints = trim(fgets(STDIN)); // Read and trim input (remove newline);

    // Ask for the user's guess number
    echo "\n Enter your guess: ";
    $guess = (int) fgets(STDIN); // Read input and cast to integer

    // Randomly generate a number to guess (based on difficulty)
    $number = $game->generate_number();

    $game->playGame($option,$number,$guess,$showHints);

    // Ask the user if they want to play again
    echo "\n Do you want to play again? (y/n): ";
    $playAgain = trim(fgets(STDIN));

    // If user types 'y' start again; otherwise, end the game
    if($playAgain === 'y' || $playAgain === 'Y' || $playAgain === 'yes'){
        continue; // Restart the loop
    }else{
        echo "\n Thank you for playing: Goodbye! \n";
        break; // Exit the loop
    }
}while(true); // Repeat untill 'break' is called




?>