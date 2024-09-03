<?php 
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class FileToStringTransformer implements DataTransformerInterface
{
    private $photoDir;

    public function __construct(string $photoDir)
    {
        $this->photoDir = $photoDir;
    }

    public function transform($filename)
    {
        if (null === $filename) {
            return null;
        }

        try {
            return new File($this->photoDir.'/'.$filename);
        } catch (FileNotFoundException $e) {
            return null;
        }
    }

    public function reverseTransform($file)
    {
        if ($file instanceof File) {
            return $file->getFilename();
        }

        return null;
    }
}
