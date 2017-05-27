<?php

namespace Hanson\Vbot\Support;

class File
{
    public static function saveTo(string $file, $data)
    {
        $path = dirname($file);

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents($file, $data);
    }
}
