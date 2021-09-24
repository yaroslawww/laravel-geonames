<?php

namespace LaraGeoData\Storage;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use ZipArchive;

/**
 * Stores collected data into files
 */
class FilesystemStorage
{

    /**
     * Provider.
     *
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Directory name.
     *
     * @var string
     */
    protected string $dirname;

    /**
     * @param Filesystem $files
     * @param string $dirname
     */
    public function __construct(Filesystem $files, string $dirname)
    {
        $this->files   = $files;
        $this->dirname = rtrim($dirname, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Create directory if not exists.
     *
     * @throws Exception
     */
    public function prepareDirectory(): void
    {
        if (!$this->files->isDirectory($this->dirname)) {
            if ($this->files->makeDirectory($this->dirname, 0777, true)) {
                $this->files->put($this->dirname . '.gitignore', "*\n!.gitignore\n");
            } else {
                throw new Exception("Cannot create directory '$this->dirname'.");
            }
        }
    }

    /**
     * Clear all stored files.
     *
     * @return bool
     * @throws Exception
     */
    public function truncate(): bool
    {
        $result = $this->files->deleteDirectory($this->dirname);
        $this->prepareDirectory();

        return $result;
    }

    /**
     * CheckIf File exists.
     *
     * @param string $filePath
     *
     * @return bool
     */
    public function exists(string $filePath): bool
    {
        return $this->files->exists($this->path($filePath));
    }

    /**
     * Get absolute file path.
     *
     * @param string $filePath
     *
     * @return string
     */
    public function path(string $filePath): string
    {
        return $this->dirname . ltrim($filePath, DIRECTORY_SEPARATOR);
    }

    /**
     * Delete file.
     *
     * @param string $filePath
     *
     * @return bool
     */
    public function delete(string $filePath): bool
    {
        return $this->files->delete($this->path($filePath));
    }

    /**
     * @param string $url
     * @param string|null $filePath
     * @param \Closure|null $progressCallback
     *
     * @return bool
     * @throws Exception
     */
    public function createFromUrl(string $url, ?string $filePath = null, ?\Closure $progressCallback = null, bool $force = false): bool
    {
        $this->prepareDirectory();
        if (!$filePath) {
            $filePath = Str::afterLast($url, DIRECTORY_SEPARATOR);
        }

        $exists = $this->exists($filePath);
        if ($exists && !$force) {
            return false;
        }

        if ($exists) {
            $this->delete($filePath);
        }

        $path = $this->path($filePath);

        $dir = Str::beforeLast($path, DIRECTORY_SEPARATOR);
        $this->files->ensureDirectoryExists($dir, 0777, true);

        switch (config('geonames.storage.download_provider')) {
            case 'curl_php':
                $result = $this->downloadViaCurlPhp($url, $path, $progressCallback);

                break;
            case 'wget':
                $result = $this->downloadViaWget($url, $path);

                break;
            default:
                throw new Exception('Current download provider not supported');
        }

        return $result;
    }

    /**
     * Extract zip file.
     *
     * @param string $file
     *
     * @return bool
     * @throws Exception
     */
    public function extractZipFile(string $file): bool
    {
        $zipArchive = new ZipArchive();
        $filePath   = $this->path($file);
        if (true === ($openResult = $zipArchive->open($filePath, ZipArchive::RDONLY))) {
            $extractTo     = Str::beforeLast($filePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $extractResult = $zipArchive->extractTo($extractTo);
            $zipArchive->close();
            if ($extractResult === true) {
                return true;
            }
        }

        if (is_numeric($openResult)) {
            throw new Exception("Zip open [{$filePath}] error: {$openResult}. See: https://php.net/manual/en/zip.constants.php");
        }

        return false;
    }

    /**
     * Download File using wget util.
     *
     * @param string $url
     * @param string $path
     *
     * @return bool
     */
    protected function downloadViaWget(string $url, string $path): bool
    {
        $process = new Process([
            'wget',
            //'-c',
            '-N',
            '-O',
            $path,
            $url,
        ]);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Download File using wget util.
     *
     * @param string $url
     * @param string $path
     *
     * @return bool
     * @throws Exception
     */
    protected function downloadViaCurlPhp(string $url, string $path, ?\Closure $progressCallback = null): bool
    {
        $fp = fopen($path, 'w');
        $ch = curl_init($url);
        if ($progressCallback) {
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $progressCallback);
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        }
        curl_setopt($ch, CURLOPT_FILE, $fp);

        $result = curl_exec($ch);

        if ($errorNumber = curl_errno($ch)) {
            throw new Exception("Error [{$errorNumber}]. See https://php.net/manual/en/function.curl-errno.php");
        }

        curl_close($ch);
        fclose($fp);

        return (bool) $result;
    }
}
