<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Resources\storefront\snippets\de_DE;

use Shopware\Core\Framework\Snippet\Files\SnippetFileInterface;

class SnippetFile_de_DE implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'easycredit.de-DE';
    }

    public function getPath(): string
    {
        return __DIR__ . '/easycredit.de-DE.json';
    }

    public function getIso(): string
    {
        return 'de-DE';
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
