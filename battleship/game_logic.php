<?php
session_start();

function initGame() {
    $fleet = [
        ['name' => 'Destroyer', 'size' => 2],
        ['name' => 'Submarine', 'size' => 3],
        ['name' => 'Cruiser', 'size' => 3],
        ['name' => 'Battleship', 'size' => 4],
        ['name' => 'Carrier', 'size' => 5]
    ];

    $_SESSION['player_board'] = generateBoard($fleet);
    $_SESSION['comp_board'] = generateBoard($fleet);
    $_SESSION['player_hits'] = 0;
    $_SESSION['comp_hits'] = 0;
    $_SESSION['history'] = ['p' => [], 'c' => []]; 
    $_SESSION['state'] = 'PLAYER_TURN';
    $_SESSION['status'] = "COMMANDER, DEPLOY SHIPS!";
    // Keep color if it already exists, otherwise set white
    if (!isset($_SESSION['board_color'])) $_SESSION['board_color'] = '#ffffff';
}

function generateBoard($fleet) {
    $board = array_fill(0, 100, null);
    foreach ($fleet as $ship) {
        $placed = false;
        while (!$placed) {
            $horiz = rand(0, 1);
            $start = rand(0, 99);
            $step = $horiz ? 1 : 10;
            
            // Boundary checks
            if ($start + ($ship['size'] - 1) * $step >= 100) continue;
            if ($horiz && floor($start/10) != floor(($start + $ship['size']-1)/10)) continue;
            
            // "No Touching" Check (checks ship cells + surrounding 8 squares)
            $canPlace = true;
            for ($i = 0; $i < $ship['size']; $i++) {
                $pos = $start + ($i * $step);
                $row = floor($pos / 10);
                $col = $pos % 10;

                for ($r = $row - 1; $r <= $row + 1; $r++) {
                    for ($c = $col - 1; $c <= $col + 1; $c++) {
                        if ($r >= 0 && $r < 10 && $c >= 0 && $c < 10) {
                            if ($board[$r * 10 + $c] !== null) $canPlace = false;
                        }
                    }
                }
            }
            
            if ($canPlace) {
                for ($i = 0; $i < $ship['size']; $i++) {
                    $board[$start + ($i * $step)] = $ship['name'];
                }
                $placed = true;
            }
        }
    }
    return $board;
}

if (!isset($_SESSION['state']) || isset($_GET['reset'])) {
    initGame();
    if(isset($_GET['reset'])) { header("Location: index.php"); exit; }
}

// Handle color change requests
if (isset($_GET['newColor'])) {
    $_SESSION['board_color'] = $_GET['newColor'];
    echo json_encode(['status' => 'saved']);
    exit;
}