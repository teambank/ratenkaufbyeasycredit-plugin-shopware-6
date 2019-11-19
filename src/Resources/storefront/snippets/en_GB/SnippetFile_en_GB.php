<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Resources\storefront\snippets\en_GB;

use Shopware\Core\Framework\Snippet\Files\SnippetFileInterface;

class SnippetFile_en_GB implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'easycredit.en-GB';
    }

    public function getPath(): string
    {
        return __DIR__ . '/easycredit.en-GB.json';
    }

    public function getIso(): string
    {
        return 'en-GB';
    }

    public function getAuthor(): string
    {
        return 'NETZKOLLEKTIV GmbH';
    }

    public function isBase(): bool
    {
        return false;
    }
}
