<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class CSVFileEntity {
    /**
     * @Assert\All({
     *     @Assert\File(
     *         maxSize = "1M",
     *         mimeTypes = {"text/csv", "application/csv", "text/x-csv"},
     *         mimeTypesMessage = "Please upload a valid CSV file"
     *     )
     * })
     */
    private $files;

    public function getFiles(): array {
        return $this->files;
    }

    public function setFiles(array $files): void {
        $this->files = $files;
    }
}