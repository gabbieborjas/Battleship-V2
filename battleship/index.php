<?php include 'game_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BATTLESHIP COMMAND</title>
    <style>
        :root { 
            --primary-color: #2c3e50; 
            --bg-color: #ecf0f1; 
            --cell-color: <?php echo $_SESSION['board_color']; ?>; 
        }
        body { font-family: 'Arial Black', sans-serif; text-align: center; background-color: var(--bg-color); color: var(--primary-color); margin: 0; padding: 20px; }
        h1 { font-size: 4rem; margin: 10px; text-transform: uppercase; border-bottom: 5px solid; }
        .controls { margin: 20px; padding: 20px; background: rgba(0,0,0,0.1); border-radius: 15px; }
        .game-container { display: flex; justify-content: center; gap: 50px; flex-wrap: wrap; margin-top: 20px; }
        .grid-label { font-size: 1.8rem; margin-bottom: 10px; background: #333; color: white; padding: 5px; }
        .grid { display: grid; grid-template-columns: repeat(10, 55px); grid-template-rows: repeat(10, 55px); gap: 2px; background: #222; padding: 8px; border: 8px solid #444; box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
        .cell { width: 55px; height: 55px; background: var(--cell-color); display: flex; align-items: center; justify-content: center; font-size: 2rem; cursor: pointer; position: relative; }
        .cell.ship-hidden { box-shadow: inset 0 0 0 4px rgba(46, 204, 113, 0.5); }
        .cell.ship-hidden::after { content: "🚢"; font-size: 1rem; opacity: 0.3; }
        .cell.hit { background: #e74c3c !important; animation: explode 0.5s forwards; }
        .cell.miss { background: #3498db !important; }
        #status-bar { font-size: 2.5rem; margin: 20px; min-height: 120px; color: #d35400; font-weight: bold; }
        #score-board { font-size: 2.5rem; background: #222; color: #00ff00; display: inline-block; padding: 15px 50px; border-radius: 10px; border: 4px solid #444; }
        button { padding: 20px 40px; font-size: 1.8rem; font-weight: bold; cursor: pointer; border-radius: 10px; border: none; background: #27ae60; color: white; box-shadow: 0 4px #1e8449; }
        @keyframes explode { 0% { transform: scale(1); background: #f1c40f; } 50% { transform: scale(1.4); background: #e67e22; border-radius: 50%; } 100% { transform: scale(1); background: #e74c3c; } }
    </style>
</head>
<body>
    <h1>BATTLESHIP</h1>
    <div class="controls">
        <label style="font-size: 1.5rem;">Board Color: </label>
        <input type="color" id="colorPicker" value="<?php echo $_SESSION['board_color']; ?>" onchange="updateColor(this.value)">
        &nbsp;&nbsp;
        <button onclick="location.href='?reset=1'">RESTART GAME</button>
    </div>

    <div id="score-board">
        PLAYER: <?php echo $_SESSION['player_hits']; ?>/17 | COMP: <?php echo $_SESSION['comp_hits']; ?>/17
    </div>

    <div id="status-bar"><?php echo $_SESSION['status']; ?></div>

    <div class="game-container">
        <div>
            <div class="grid-label">ENEMY WATERS</div>
            <div id="computer-grid" class="grid">
                <?php for($i=0; $i<100; $i++): 
                    $res = $_SESSION['history']['p'][$i] ?? '';
                    $class = ($res == 'H') ? 'hit' : (($res == 'M') ? 'miss' : '');
                ?>
                    <div class="cell <?php echo $class; ?>" onclick="playerShoot(<?php echo $i; ?>)">
                        <?php echo ($res == 'H' ? '💥' : ($res == 'M' ? '🌊' : '')); ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div>
            <div class="grid-label">YOUR FLEET</div>
            <div id="player-grid" class="grid">
                <?php for($i=0; $i<100; $i++): 
                    $res = $_SESSION['history']['c'][$i] ?? '';
                    $shipName = $_SESSION['player_board'][$i];
                    $class = ($shipName !== null) ? 'ship-hidden ' : '';
                    $class .= ($res == 'H') ? 'hit' : (($res == 'M') ? 'miss' : '');
                ?>
                    <div class="cell <?php echo $class; ?>">
                        <?php echo ($res == 'H' ? '💥' : ($res == 'M' ? '🌊' : '')); ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script>
        function playerShoot(idx) {
            if ("<?php echo $_SESSION['state']; ?>" !== 'PLAYER_TURN') return;
            fetch('api.php?idx=' + idx).then(() => location.reload());
        }

        function updateColor(val) {
            // Update UI immediately
            document.documentElement.style.setProperty('--cell-color', val);
            // Save to server session so it survives refresh
            fetch('game_logic.php?newColor=' + encodeURIComponent(val));
        }
    </script>
</body>
</html>