<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface XlsxServiceInterface
{
    public function generate(): BinaryFileResponse;
}
