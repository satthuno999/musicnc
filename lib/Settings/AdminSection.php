<?php
/**
 * Audio Player
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <audioplayer@scherello.de>
 * @copyright 2016-2021 S P A R K
 */

namespace OCA\musicnc\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection
{
    /** @var IURLGenerator */
    private $urlGenerator;
    /** @var IL10N */
    private $l;

    public function __construct(IURLGenerator $urlGenerator, IL10N $l)
    {
        $this->urlGenerator = $urlGenerator;
        $this->l = $l;
    }

    /**
     * returns the relative path to an 16*16 icon describing the section.
     *
     * @returns string
     */
    public function getIcon()
    {
        return $this->urlGenerator->imagePath('musicnc', 'app-dark.svg');
    }

    /**
     * returns the ID of the section. It is supposed to be a lower case string,
     *
     * @returns string
     */
    public function getID()
    {
        return 'musicnc';
    }

    /**
     * returns the translated name as it should be displayed
     *
     * @return string
     */
    public function getName()
    {
        return $this->l->t('Audio Player');
    }

    /**
     * returns priority for positioning
     *
     * @return int
     */
    public function getPriority()
    {
        return 10;
    }
}