<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Resources\app\storefront\src\snippets\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_de_DE implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'easycredit.de-DE';
    }

    public function getPath(): string
    {
        return __DIR__ . '/../../../../../snippet/de_DE/easycredit.de-DE.json';
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
