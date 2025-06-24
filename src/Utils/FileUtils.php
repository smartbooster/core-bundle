<?php

namespace Smart\CoreBundle\Utils;

use Symfony\Component\String\Slugger\AsciiSlugger;

class FileUtils
{
    /**
     * Slugify and normalize filename
     */
    public static function slugifyFilename(string $fileName): string
    {
        $slugger = new AsciiSlugger('fr');

        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $slugifiedBase = $slugger->slug($baseName)->lower()->toString();

        if (!empty($extension)) {
            $downloadFileName = $slugifiedBase . '.' . strtolower($extension);
        } else {
            $downloadFileName = $slugifiedBase;
        }

        return $downloadFileName;
    }
}
