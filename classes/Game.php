<?php
class Game {
	private $gameName;
	private $gameId;
	private $storage;
	private $field;

	public function __construct(DataStorage $storage, GameField $field) {

		$this->storage = $storage;
		$this->field = $field;
		$this->gameName = 'Game';
	}

	public function proccessMove(string $role, array $coordinates) {
		try {
			$this->field->makeMove($role, $coordinates);
		} catch (GameFieldException $e) {
			return [false, $e->getMessage()];
		}
		$this->saveState();

		return [true, ''];
	}

	public function getNextPlayer() {
		return $this->field->getNextStepRole();
	}

	public function loadGame(string $gameId) {
		$this->setId($gameId);

		$data = $this->storage->read($this->gameName);

		if (!$data) {
			throw new GameException('Saved game not found!');
		}

		$this->field->loadSavedState($data['field']);
	}

	private function saveState() {
		$data['field'] = $this->field->returnCurrentState();

		$this->storage->write($this->gameName, $data);
	}

	public function getWinner() {
		return $this->field->hasWinner();
	}

	public function isFinished() {
		return ($this->getWinner() || $this->field->isFilled());
	}

	public function getField() {
		return $this->field->getFieldData();
	}

	private function setId($id) {
		$this->gameId = $id;
		$this->gameName .= $id;
	}

	public function startNewGame() {
		$gameId = substr(md5(time()), 0, 10);

		$this->setId($gameId);
		$this->saveState();

		return $gameId;
	}

}