<?php
abstract class GameField implements IGameEntity {
	/*
	 * @var fieldData - array of cells
	 */
	protected $fieldData;

	abstract protected function hasWinner();
}