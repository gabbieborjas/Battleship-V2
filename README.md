# Battleship-V2

 # Battleship V2+: State-Managed Edition
This version of Battleship moves away from simple client-side logic to a robust, server-controlled architecture using PHP Sessions. The game state is persistent, turn-based, and enforced by the server to prevent "cheating" or state loss upon browser refresh.

# Major Iterations
1. Server-Side State Management (Persistence)
I moved the game logic from JavaScript to the server using PHP Sessions.

What changed: Ship placement, hit/miss tracking, and scores are now stored in $_SESSION.

Result: The game no longer resets if the user refreshes the browser. The "Board Color" preference also persists through server-side storage.

2. Turn-Based Logic (Computer Fires Back)
I implemented a coordinated turn sequence where the computer is a reactive opponent.

What changed: Every time the player submits a move, the server processes that hit/miss and then immediately executes the Computer's turn as part of the same transaction.

Result: This creates a true "fire back" mechanic. The player cannot move again until the computer has finished its counter-attack, ensuring a fair, turn-based experience.

3. Explicit Game State Machine
I implemented a formal state machine to control the flow of the game.

What changed: Added states: SETUP, PLAYER_TURN, COMPUTER_TURN, and GAME_OVER.

Result: All transitions are enforced on the server via api.php. A player cannot make multiple moves at once, and the computer only fires back once the state transitions to its turn.

# Architecture Snapshot
Responsibility Breakdown
    Client (Browser):

      Renders the HTML/CSS grid.

      Captures user clicks and sends coordinates to the server using the Fetch API.

      Updates the UI (Color Picker) locally for immediate feedback.

    Server (XAMPP/PHP):

      Logic Engine: Randomly generates ship placements with a "no-touching" buffer rule.

      State Authority: Validates every shot. Determines if a ship is "Sunk" by checking the remaining coordinates of that specific boat.

      Turn Logic: Triggers the computer's turn automatically after a valid player move.

    State & Transitions
    Where state lives: All game data resides in the $_SESSION superglobal on the server.

    Transition List:

      SETUP -> Game initializes, boards are built.

      PLAYER_TURN -> Server waits for an AJAX request from the client.

      COMPUTER_TURN -> Server processes the computer's move immediately after the player's move.

      GAME_OVER -> Triggered when either player_hits or comp_hits reaches 17.

# AI Prompt Log
For this project, I used AI intentionally to help architect the server-side logic and ensure a smooth user experience. My first major prompt focused on moving the game from a "client-only" setup to a persistent state; I asked, "How do I make a Battleship game stay saved when I refresh the page using PHP?" I accepted the suggestion to use PHP Sessions because it effectively satisfied the "no refresh hacks" requirement.

Next, I focused on the visual logic, asking the AI to "Modify my ship placement logic so ships never touch each other, even diagonally." I accepted this logic because it made the game board look more professional and prevented ships from "clumping" together. To handle the rules, I prompted the AI to "Create a state machine in PHP with PLAYER_TURN and COMPUTER_TURN." I accepted this approach as it allowed me to enforce game rules on the server, ensuring the computer always fires back exactly once per player move.

I also asked, "How can I check if a specific ship is sunk, not just if a coordinate is hit?" I rejected a simpler method in favor of a more detailed check that tracks ship names, which allowed me to create the "Sunk Ship" notifications. Finally, I prompted the AI to "Make a function to save the user's color picker choice to the PHP session." I implemented this via a small fetch call so the user's aesthetic "vibe" would stay consistent throughout the entire play session.
