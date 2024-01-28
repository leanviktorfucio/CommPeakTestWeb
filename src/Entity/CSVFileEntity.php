<?php

namespace App\Entity;

class CSVFileEntity {
    private $files;

    public function getFiles(): array {
        return $this->files;
    }

    public function setFiles(array $files): void {
        $this->files = $files;
    }
}
