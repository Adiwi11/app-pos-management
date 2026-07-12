<?php
function clean_comments_recursive($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        if (in_array($ext, ['php', 'js', 'css', 'html'])) {
            remove_comments($file->getPathname(), $ext);
        }
    }
}
function remove_comments($filepath, $ext) {
    $source = file_get_contents($filepath);
    $output = '';
    if ($ext === 'php') {
        $source = preg_replace('//is', '', $source);
        $tokens = token_get_all($source);
        foreach ($tokens as $token) {
            if (is_string($token)) {
                $output .= $token;
            } else {
                list($id, $text) = $token;
                if ($id == T_COMMENT || $id == T_DOC_COMMENT) {
                    $newlines = substr_count($text, "\n");
                    $output .= str_repeat("\n", $newlines); 
                    continue;
                }
                $output .= $text;
            }
        }
    } elseif ($ext === 'css') {
        $output = preg_replace('!/\*.*?\*/!s', '', $source);
    } else {
        if($ext === 'html') {
             $output = preg_replace('//is', '', $source);
        } else {
             $output = $source; 
        }
    }
    if ($output !== '' && $output !== $source) {
        $output = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $output);
        file_put_contents($filepath, $output);
    }
}
$target_dir = __DIR__;
clean_comments_recursive($target_dir);
echo "Semua Komentar Berhasil Dibersihkan.";
