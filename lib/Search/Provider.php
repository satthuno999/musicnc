<?php
/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2020 S P A R K
 */

declare(strict_types=1);

namespace OCA\musicnc\Search;

use OCA\musicnc\Controller\DbController;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Search\IProvider;
use OCP\Search\ISearchQuery;
use OCP\Search\SearchResult;
use OCP\Search\SearchResultEntry;

class Provider implements IProvider
{

    /** @var IL10N */
    private $l10n;

    /** @var IURLGenerator */
    private $urlGenerator;

    private $DBController;

    public function __construct(IL10N $l10n,
                                IURLGenerator $urlGenerator,
                                DBController $DBController)
    {
        $this->l10n = $l10n;
        $this->urlGenerator = $urlGenerator;
        $this->DBController = $DBController;
    }

    public function getId(): string
    {
        return 'musicnc';
    }

    public function search(IUser $user, ISearchQuery $query): SearchResult
    {
        $datasets = $this->DBController->search($query->getTerm());
        $result = [];

        foreach ($datasets as $dataset) {
            $result[] = new SearchResultEntry(
                '',
                $this->l10n->t('Audio Player') . ' - ' . $dataset['name'],
                '',
                $this->urlGenerator->linkToRoute('musicnc.page.index') . '#' . $dataset['id'],
                $this->urlGenerator->imagePath('musicnc', 'app-dark.svg')
            );
        }

        return SearchResult::complete(
            $this->l10n->t('musicnc'),
            $result
        );
    }

    public function getName(): string
    {
        return $this->l10n->t('musicnc');
    }

    public function getOrder(string $route, array $routeParameters): int
    {
        return 10;
    }
}