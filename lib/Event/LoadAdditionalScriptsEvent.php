<?php

declare(strict_types=1);

/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @author Sebastian Doell <sebastian@libasys.de>
 * @copyright 2012-2023 S P A R K
 * @copyright 2015 Sebastian Doell
 */
namespace OCA\musicnc\Event;

use OCP\EventDispatcher\Event;

class LoadAdditionalScriptsEvent extends Event {
    private $hiddenFields = [];

    public function addHiddenField(string $name, string $value): void {
        $this->hiddenFields[$name] = $value;
    }

    public function getHiddenFields(): array {
        return $this->hiddenFields;
    }
}