<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Compatibility;

class Capabilities {
    private $version;

    public function __construct($version) {
       $this->version = $version;
    }

    public function hasFlowBuilder() {
        return \version_compare($this->version, '6.4.6.0') >= 0;
    }
}