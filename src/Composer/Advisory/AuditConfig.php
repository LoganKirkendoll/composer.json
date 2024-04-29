<?php declare(strict_types=1);

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Advisory;

use Composer\Config;

class AuditConfig
{
    /**
     * @var array<string>|array<string,string> List of advisory IDs, remote IDs or CVE IDs that reported but not listed as vulnerabilities.
     */
    public $ignoreList;

    /**
     * @var Auditor::ABANDONED_*
     */
    public $abandoned;

    /**
     * @param array<string>|array<string,string> $ignoreList
     * @param Auditor::ABANDONED_* $abandoned
    */
    public function __construct(array $ignoreList, string $abandoned)
    {
        $this->ignoreList = $ignoreList;
        $this->abandoned = $abandoned;
    }

    public static function fromConfig(Config $config): self
    {
        $auditConfig = $config->get('audit');

        return new self(
            $auditConfig['ignore'] ?? [],
                $auditConfig['abandoned'] ?? Auditor::ABANDONED_FAIL
        );
    }
}
