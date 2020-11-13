<?php
define('FS_ROOT', dirname(__FILE__) . '/');

require_once 'helpers.php';

do_autoload('interfaces');
do_autoload('classes');

set_error_handler(function ($severity, $message, $file, $line) {
	throw new \ErrorException($message, $severity, $severity, $file, $line);
});

$method = $_GET['m'] ?? false;

$dataStorage = new JSONFileStorage();
$gameField = new TTTField();
$game = new Game($dataStorage, $gameField);

$gameId = $_GET['gameId'] ?? false;

switch ($method) {

	case 'start': {
			$gameId = $game->startNewGame();
			respond(['gameId' => $gameId]);
		}
		break;
	case 'move': {
			$playerRole = $_POST['role'] ?? false;
			$coordinates = $_POST['coords'] ?? false;
			$coordinates = explode(',', $coordinates);

			$game->loadGame($gameId);

			if ($game->isFinished()) {
				$response = array(
					'error' => false,
					'finished' => true,
					'winner' => $game->getWinner() ?? false
				);
				respond($response);
			}

			list ($success, $message) = $game->proccessMove($playerRole, $coordinates);

			if (!$success) {
				$response = array(
					'error' => true,
					'message' => $message,
					'finished' => false
				);
				respond($response);
			}


			if ($winner = $game->getWinner()) {
				$response = array(
					'error' => false,
					'finished' => true,
					'winner' => $winner
				);
			} else {
				$response = array(
					'error' => false,
					'finished' => false,
				);
			}

			respond($response);
		}


		break;
	default:
		if ($gameId) {
			$game->loadGame($gameId);
			$field = $game->getField();

			if ($field) {
				for ($i = 0; $i < count($field); $i++) {
					for ($j = 0; $j < count($field); $j++) {
						echo '[&nbsp;&nbsp;' . (!empty($field[$i][$j]) ? $field[$i][$j] : '&nbsp;&nbsp;') . '&nbsp;&nbsp;]';
					}
					echo '<br>';
				}

				if ($game->isFinished()) {
					if ($winner = $game->getWinner()) {
						echo 'Game ended! Winnner is ' . $winner;
					} else {
						echo 'Game ended and there is no winner ';
					}
				} else {
					echo 'Next player: ' . $game->getNextPlayer();
				}
			}
		} else {
			echo 'Waiting for requests';
		}
}




