<?php
declare(strict_types=1);

function extension_root(string $type): string
{
    if (!in_array($type, ['plugin', 'theme'], true)) {
        throw new InvalidArgumentException('Invalid extension type.');
    }
    $root = dirname(__DIR__) . '/' . ($type === 'plugin' ? 'plugins' : 'themes');
    if (!is_dir($root) && !mkdir($root, 0750, true) && !is_dir($root)) {
        throw new RuntimeException('Cannot create extension directory.');
    }
    return $root;
}

function extension_slug(string $name): string
{
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
    $slug = trim($slug, '-');
    if ($slug === '') {
        throw new RuntimeException('The ZIP filename is not a valid extension name.');
    }
    return substr($slug, 0, 80);
}

function extension_files(string $type): array
{
    $root = extension_root($type);
    $items = [];
    foreach (scandir($root) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..' || $entry === '.gitkeep' || $entry === '.active') {
            continue;
        }
        $path = $root . '/' . $entry;
        if (is_dir($path)) {
            $items[] = ['slug' => $entry, 'path' => $path, 'modified' => filemtime($path) ?: 0];
        }
    }
    usort($items, static fn (array $a, array $b): int => strcmp($a['slug'], $b['slug']));
    return $items;
}

function active_extension(string $type): ?string
{
    $file = extension_root($type) . '/.active';
    if (!is_file($file)) {
        return null;
    }
    $value = trim((string) file_get_contents($file));
    return $value !== '' ? $value : null;
}

function activate_extension(string $type, string $slug): void
{
    $root = extension_root($type);
    $path = $root . '/' . $slug;
    if (!is_dir($path) || !preg_match('/^[a-z0-9-]+$/', $slug)) {
        throw new RuntimeException('Extension not found.');
    }
    if (file_put_contents($root . '/.active', $slug . PHP_EOL, LOCK_EX) === false) {
        throw new RuntimeException('Cannot activate extension.');
    }
}

function import_extension(string $type, array $upload): string
{
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('PHP ZipArchive extension is required to import ZIP files.');
    }
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The ZIP upload failed.');
    }
    if (($upload['size'] ?? 0) > 10 * 1024 * 1024) {
        throw new RuntimeException('The ZIP file must be 10 MB or smaller.');
    }
    if (strtolower(pathinfo((string) $upload['name'], PATHINFO_EXTENSION)) !== 'zip') {
        throw new RuntimeException('Only ZIP files are allowed.');
    }

    $archive = new ZipArchive();
    if ($archive->open((string) $upload['tmp_name']) !== true) {
        throw new RuntimeException('The ZIP file could not be opened.');
    }

    $slug = extension_slug(pathinfo((string) $upload['name'], PATHINFO_FILENAME));
    $root = extension_root($type);
    $temporary = $root . '/.' . $slug . '-' . bin2hex(random_bytes(8));
    if (!mkdir($temporary, 0750, true)) {
        $archive->close();
        throw new RuntimeException('Cannot create temporary extension directory.');
    }

    try {
        for ($index = 0; $index < $archive->numFiles; $index++) {
            $entry = $archive->getNameIndex($index);
            if ($entry === false) {
                throw new RuntimeException('Invalid ZIP entry.');
            }
            $normalized = str_replace('\\', '/', $entry);
            if ($normalized === '' || str_starts_with($normalized, '/') || preg_match('#(^|/)\.\.?(/|$)#', $normalized)) {
                throw new RuntimeException('Unsafe path found in ZIP archive.');
            }
            $target = $temporary . '/' . $normalized;
            $targetDir = dirname($target);
            if (!is_dir($targetDir) && !mkdir($targetDir, 0750, true) && !is_dir($targetDir)) {
                throw new RuntimeException('Cannot create ZIP directory.');
            }
            if (str_ends_with($normalized, '/')) {
                continue;
            }
            $stream = $archive->getStream($entry);
            if ($stream === false) {
                throw new RuntimeException('Cannot read ZIP entry.');
            }
            $contents = stream_get_contents($stream);
            fclose($stream);
            if ($contents === false || file_put_contents($target, $contents, LOCK_EX) === false) {
                throw new RuntimeException('Cannot extract ZIP entry.');
            }
        }
        $archive->close();
        $destination = $root . '/' . $slug;
        if (is_dir($destination)) {
            throw new RuntimeException('An extension with this name already exists.');
        }
        if (!rename($temporary, $destination)) {
            throw new RuntimeException('Cannot install extension.');
        }
    } catch (Throwable $exception) {
        $archive->close();
        remove_extension_directory($temporary);
        throw $exception;
    }

    return $slug;
}

function remove_extension_directory(string $directory): void
{
    if (!is_dir($directory)) {
        return;
    }
    foreach (scandir($directory) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $path = $directory . '/' . $entry;
        is_dir($path) ? remove_extension_directory($path) : unlink($path);
    }
    rmdir($directory);
}
