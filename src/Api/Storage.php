<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Api;

use Symfony\Component\HttpFoundation\Session\Session;
use Monolog\Logger;

class Storage implements \Teambank\RatenkaufByEasyCreditApiV3\Integration\StorageInterface
{
    protected $session;

    protected $logger;

    public function __construct(
        Session $session,
        Logger $logger
    ) {
        $this->session = $session;
        $this->logger = $logger;
    }

    public function set($key, $value): self
    {
        $this->logger->debug('storage::set '.$key.' = '.$value);
        $this->session->set('easycredit_' . $key, $value);

        return $this;
    }

    public function get($key): string
    {
        $value = (string) $this->session->get('easycredit_' . $key);
        $this->logger->debug('storage::get '.$key.' = '.$value);
        return $value;
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
        $backtrace = debug_backtrace();
        $this->logger->info('storage::clear from ' .$backtrace[1]['class'].':'.$backtrace[1]['function']);

        foreach (array_keys($this->all()) as $key) {
            $this->session->remove($key);
        }

        return $this;
    }
}
