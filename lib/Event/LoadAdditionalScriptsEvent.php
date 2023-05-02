<?php

declare(strict_types=1);

/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2022-2023 S P A R K
 * 
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