<?php
class TTTField extends GameField {
	/*
	 * @var field size (width x width)
	 */
	private $width = 0;

	/*
	 * @var array of players`s roles - 'x', or 'o' 	 
	 */
	private $roles = [];

	/*
	 * @var who's turn to move next (one of the role)
	 */
	private $nextStepRole = '';
	private $winnerRole = false;

	public function __construct(int $width = 3) {
		$this->roles = ['x', 'o'];
		$this->width = $width;
		$this->nextStepRole = reset($this->roles);

		for ($i = 0; $i < $width; $i++) {
			for ($j = 0; $j < $width; $j++) {
				$this->fieldData[$i][$j] = '';
			}
		}
	}

	public function getPlayersRoles() {
		return $this->roles;
	}

	public function makeMove(string $role, array $coords) {

		if (!in_array($role, $this->roles)) {
			throw new GameFieldException("This player`s role is not allowed on this field!");
		}

		if ($this->nextStepRole != $role) {
			throw new GameFieldException("It's not your turn!");
		}
		if ($coords[0] > $this->width - 1 || $coords[1] > $this->width - 1) {
			throw new GameFieldException('Out of bounds');
		}

		if (!empty($this->fieldData[$coords[0]][$coords[1]])) {
			throw new GameFieldException('This cell already occupied!');
		}

		$this->fieldData[$coords[0]][$coords[1]] = $role;

		$this->setNextStepRole();

		return true;
	}

	public function loadSavedState($data) {
		$this->fieldData = $data['fieldData'];

		$this->nextStepRole = $data['nextStepRole'];

		while (current($this->roles) != $this->nextStepRole) {
			next($this->roles);
		}
	}

	public function returnCurrentState() {
		$data['fieldData'] = $this->fieldData;
		$data['nextStepRole'] = $this->nextStepRole;
		return $data;
	}

	public function getNextStepRole() {
		return $this->nextStepRole;
	}

	/*
	 *  Define which user can make a move next time
	 */
	private function setNextStepRole() {
		$now_step_idx = key($this->roles);
		next($this->roles);
		$next_step_idx = key($this->roles);

		if ($next_step_idx == NULL || $next_step_idx == $now_step_idx) { //if we are at the end of `players` list - move to the start
			$this->nextStepRole = reset($this->roles);
		} else {
			$this->nextStepRole = $this->roles[$next_step_idx];
		}
	}

	public function getFieldData() {
		return $this->fieldData;
	}

	public function hasWinner() {
		if ($winner = $this->checkDiagonals()) {
			return $winner;
		}

		if ($winner = $this->checkVerticals()) {
			return $winner;
		}

		if ($winner = $this->checkHorizontals()) {
			return $winner;
		}

		return false;
	}

	private function checkDiagonals() {

		for ($i = 1; $i < $this->width; $i++) { //From top left to bottom right
			$is_winner = true;
			if (empty($this->fieldData[$i][$i]) || ($this->fieldData[$i][$i] != $this->fieldData[0][0])) {
				$is_winner = false;
				break;
			} else {
				$is_winner = true;
			}
		}

		if ($is_winner) {
			$this->winnerRole = $this->fieldData[0][0];
			return $this->winnerRole;
		}


		for ($i = 1; $i < $this->width; $i++) { // From bottom left to top right
			$is_winner = true;

			if (empty($this->fieldData[$this->width - $i - 1][$i]) || ($this->fieldData[$this->width - $i - 1][$i] != $this->fieldData[$this->width - 1][0])) {
				$is_winner = false;
				break;
			} else {
				$is_winner = true;
			}
		}

		if ($is_winner) {
			$this->winnerRole = $this->fieldData[$this->width - 1][0];
			return $this->winnerRole;
		}

		return false;
	}

	public function isFilled() {

		for ($i = 0; $i < $this->width; $i++) {
			for ($j = 0; $j < $this->width; $j++) {
				if (empty($this->fieldData[$i][$j])) return false;
			}
		}

		return true;
	}

	private function checkVerticals() {
		for ($i = 0; $i < $this->width; $i++) {
			$is_winner = true;

			for ($j = 1; $j < $this->width; $j++) {

				if (empty($this->fieldData[$j][$i]) || ($this->fieldData[$j][$i] != $this->fieldData[0][$i])) {
					$is_winner = false;
					break;
				} else {
					$is_winner = true;
				}
			}

			if ($is_winner) {
				$this->winnerRole = $this->fieldData[0][$i];
				return $this->winnerRole;
			}
		}

		return false;
	}

	private function checkHorizontals() {
		for ($i = 0; $i < $this->width; $i++) {
			$is_winner = true;
			for ($j = 1; $j < $this->width; $j++) {

				if (empty($this->fieldData[$i][$j]) || ($this->fieldData[$i][$j] != $this->fieldData[$i][0])) {
					$is_winner = false;
					break;
				} else {
					$is_winner = true;
				}
			}
			if ($is_winner) {
				$this->winnerRole = $this->fieldData[$i][0];
				return $this->winnerRole;
			}
		}

		return false;
	}

}