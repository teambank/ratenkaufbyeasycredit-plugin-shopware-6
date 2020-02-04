<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api;

use Symfony\Component\HttpFoundation\Session\Session;

class Storage implements \Netzkollektiv\EasyCreditApi\StorageInterface
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param null|string $value
     */
    public function set(string $key, ?string $value): self
    {
        $this->session->set('easycredit_' . $key, $value);

        return $this;
    }

    public function get(string $key): string
    {
        return $this->session->get('easycredit_' . $key);
    }

    public function all(): array
    {
        $session = [];
        foreach ($this->session as $key => $value) {
            if (mb_strpos($key, 'easycredit') === 0) {
                $session[$key] = $value;
            }
        }

        return $session;
    }

    public function clear(): self
    {
        foreach (array_keys($this->all()) as $key) {
            $this->session->remove($key);
        }

        return $this;
    }
}
