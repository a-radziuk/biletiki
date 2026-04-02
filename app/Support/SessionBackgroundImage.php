<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class SessionBackgroundImage
{
    /** @var list<string> */
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

    public static function assetUrl(?string $sessionId = null): ?string
    {
        $dir = public_path('backgrounds');
        if (! is_dir($dir)) {
            return null;
        }

        $files = [];
        foreach (File::files($dir) as $file) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, self::EXTENSIONS, true)) {
                $files[] = $file->getPathname();
            }
        }

        if ($files === []) {
            return null;
        }

        sort($files);

        $sessionId ??= session()->getId();
        if ($sessionId === '') {
            return null;
        }

        $index = hexdec(substr(hash('sha256', $sessionId), 0, 8)) % count($files);
        $basename = basename($files[$index]);

        return asset('backgrounds/'.$basename);
    }
}
