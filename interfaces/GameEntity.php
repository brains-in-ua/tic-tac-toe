<?php 

interface IGameEntity {

    public function loadSavedState(string $data);
    
    public function returnCurrentState();

}