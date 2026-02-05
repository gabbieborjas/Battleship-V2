<?php
include 'game_logic.php';

if ($_SESSION['state'] !== 'PLAYER_TURN') {
    echo json_encode(['error' => 'Not your turn']); exit;
}

$idx = (int)$_GET['idx'];

// 1. PLAYER MOVE
$shipHit = $_SESSION['comp_board'][$idx];
if ($shipHit !== null) {
    $_SESSION['history']['p'][$idx] = 'H';
    $_SESSION['player_hits']++;
    $_SESSION['status'] = "DIRECT HIT ON ENEMY!";
    
    // Check if sunk
    $sunk = true;
    foreach($_SESSION['comp_board'] as $i => $name) {
        if ($name === $shipHit && ($_SESSION['history']['p'][$i] ?? '') !== 'H') {
            $sunk = false;
        }
    }
    if ($sunk) $_SESSION['status'] = "BOOM! YOU SUNK THEIR " . strtoupper($shipHit) . "!";
} else {
    $_SESSION['history']['p'][$idx] = 'M';
    $_SESSION['status'] = "YOU MISSED!";
}

// Check Win
if ($_SESSION['player_hits'] >= 17) {
    $_SESSION['state'] = 'GAME_OVER';
    $_SESSION['status'] = "VICTORY! ALL ENEMY SHIPS SUNK!";
} else {
    // 2. COMPUTER MOVE
    $_SESSION['state'] = 'COMPUTER_TURN';
    do { $cIdx = rand(0, 99); } while (isset($_SESSION['history']['c'][$cIdx]));
    
    $compShipHit = $_SESSION['player_board'][$cIdx];
    if ($compShipHit !== null) {
        $_SESSION['history']['c'][$cIdx] = 'H';
        $_SESSION['comp_hits']++;
        
        $cSunk = true;
        foreach($_SESSION['player_board'] as $i => $name) {
            if ($name === $compShipHit && ($_SESSION['history']['c'][$i] ?? '') !== 'H') {
                $cSunk = false;
            }
        }
        if ($cSunk) $_SESSION['status'] = "ALARM! THEY SUNK YOUR " . strtoupper($compShipHit) . "!";
    } else {
        $_SESSION['history']['c'][$cIdx] = 'M';
    }

    if ($_SESSION['comp_hits'] >= 17) {
        $_SESSION['state'] = 'GAME_OVER';
        $_SESSION['status'] = "DEFEAT! YOUR FLEET IS GONE!";
    } else {
        $_SESSION['state'] = 'PLAYER_TURN';
    }
}

echo json_encode(['ok' => true]);