<?php
declare(strict_types=1);
/**
 * Audioplayer
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 * @copyright 2020 S P A R K
 */

namespace OCA\musicnc\WhatsNew;

use OCP\AppFramework\Db\Entity;

/**
 * Class ChangesResult
 *
 * @package OC\Updater
 * @method string getVersion()= 1
 * @method void setVersion(string $version)
 * @method string getEtag()
 * @method void setEtag(string $etag)
 * @method int getLastCheck()
 * @method void setLastCheck(int $lastCheck)
 * @method string getData()
 * @method void setData(string $data)
 */
class WhatsNewResult extends Entity
{
    /** @var string */
    protected $version = '';

    /** @var string */
    protected $etag = '';

    /** @var int */
    protected $lastCheck = 0;

    /** @var string */
    protected $data = '';

    public function __construct()
    {
        $this->addType('version', 'string');
        $this->addType('etag', 'string');
        $this->addType('lastCheck', 'int');
        $this->addType('data', 'string');
    }
}