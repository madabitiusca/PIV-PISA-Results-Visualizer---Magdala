<?php

class Cache
{
    private string $cacheDir;
    private int $ttl;

    public function __construct(string $cacheDir, int $ttl = 300)
    {
        $this->cacheDir = $cacheDir;
        $this->ttl = $ttl;

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    private function getPath(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.json';
    }

    public function get(string $key): ?string
    {
        $file = $this->getPath($key);

        if (!file_exists($file)) {
            return null;
        }

        if (time() - filemtime($file) > $this->ttl) {
            unlink($file);
            return null;
        }

        return file_get_contents($file);
    }

    public function set(string $key, string $data): void
    {
        file_put_contents($this->getPath($key), $data);
    }

    public function clear(): void
    {
        foreach (glob($this->cacheDir . '/*.json') as $file) {
            unlink($file);
        }
    }

    public function count(): int
    {
        return count(glob($this->cacheDir . '/*.json'));
    }
}