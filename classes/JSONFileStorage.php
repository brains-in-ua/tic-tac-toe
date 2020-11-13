<?php
class JSONFileStorage implements DataStorage {
	private $filename = '';
	private $fullpath = '';
	private $storageData = '';

	public function __construct($filename = 'storage.json') {
		if (!file_exists('storage')) {
			mkdir('storage');
			chmod('storage', '666');
		}

		$this->fullpath = FS_ROOT . 'storage/';
		$this->filename = $filename;
	}

	private function readStorage() {
		try {
			$this->storageData = json_decode(file_get_contents($this->fullpath . $this->filename), true) ;
			
			
		} catch (Exception $e) {
			if (!file_exists($this->fullpath . $this->filename)) {
				touch($this->fullpath . $this->filename);
				$this->storageData = [];
			} else {
				throw new JSONFileStorageException('Cant read from JSON storage! ' . $e->getMessage());
			}
		}
	}

	private function writeStorage() {
		try {
			file_put_contents($this->fullpath . $this->filename, json_encode($this->storageData));
		} catch (Exception $e) {
			throw new JSONFileStorageException('Cant write to JSON storage!' . $e->getMessage());
		}
	}

	public function write($key = '', $value = '') {
		$this->readStorage();
		$this->storageData[$key] = $value;
		$this->writeStorage();
	}

	public function read($key = null) {
		$this->readStorage();
		return $this->storageData[$key] ?? false;
	}

	public function delete($key = null) {
		$this->readStorage();
		unset($this->storageData[$key]);
		$this->writeStorage();
	}

}