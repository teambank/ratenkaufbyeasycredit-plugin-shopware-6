<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Api;

use Symfony\Component\HttpFoundation\Session\Session;

class Storage implements \Netzkollektiv\EasyCreditApi\StorageInterface
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function set($key, $value)
    {
        file_put_contents('/tmp/bla', 'set:' . $key . ' => ' . $value . PHP_EOL, FILE_APPEND);
        $this->session->set('easycredit_' . $key, $value);

        return $this;
    }

    public function get($key)
    {
        file_put_contents('/tmp/bla', 'get:' . $key . ' => ' . $this->session->get($key) . PHP_EOL, FILE_APPEND);

        return $this->session->get('easycredit_' . $key);
    }

    public function all()
    {
        $session = [];
        foreach ($this->session as $key => $value) {
            if (strpos($key, 'easycredit') === 0) {
                $session[$key] = $value;
            }
        }

        return $session;
    }

    public function clear()
    {
        foreach ($this->all() as $key => $value) {
            file_put_contents('/tmp/bla', 'clear:' . $key . PHP_EOL, FILE_APPEND);
            $this->session->remove($key);
        }

        return $this;
    }
}
