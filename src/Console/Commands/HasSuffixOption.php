<?php

namespace LaraGeoData\Console\Commands;

use Illuminate\Support\Str;

trait HasSuffixOption
{
    /**
     * Get formatted suffix.
     *
     * @return string|null
     */
    protected function getSuffix(): ?string
    {
        $suffix = $this->option('suffix');
        if ($suffix) {
            $suffix = Str::snake($suffix);
        }

        return $suffix;
    }
}
