<?php

namespace OCA\MusicNC\App;

use \OCP\AppFramework\IAppContainer;

$app = \OC::$server->query(Music::class);

$c = $app->getContainer();
$appName = $c->query('AppName');

/**
 * add navigation
 */
\OC::$server->getNavigationManager()->add(function () use ($c, $appName) {
	return [
		'id' => $appName,
		'order' => 10,
		'name' => $c->query('L10N')->t('Music'),
		'href' => $c->query('URLGenerator')->linkToRoute('music.page.index'),
		'icon' => \OCA\MusicNC\Utility\HtmlUtil::getSvgPath('music')
	];
});

/**
 * register regular tasks
 */
\OC::$server->getJobList()->add('OC\BackgroundJob\Legacy\RegularJob', ['OCA\MusicNC\Backgroundjob\Cleanup', 'run']);
\OC::$server->getJobList()->add('OC\BackgroundJob\Legacy\RegularJob', ['OCA\MusicNC\Backgroundjob\PodcastUpdateCheck', 'run']);

/**
 * register hooks
 */
$c->query('FileHooks')->register();
$c->query('ShareHooks')->register();
$c->query('UserHooks')->register();

/**
 * register search provider
 */
$c->getServer()->getSearch()->registerProvider(
		'OCA\MusicNC\Search\Provider',
		['app' => $appName, 'apps' => ['files']]
);

/**
 * Set content security policy to allow streaming media from the configured external sources
 */
function adjustCsp(IAppContainer $container) {
	/** @var \OCP\IConfig $config */
	$config = $container->query('Config');
	$radioSources = $config->getSystemValue('music.allowed_radio_src', ['http://*:*', 'https://*:*']);
	$enableHls = $config->getSystemValue('music.enable_radio_hls', true);

	if (\is_string($radioSources)) {
		$radioSources = [$radioSources];
	}

	$policy = new \OCP\AppFramework\Http\ContentSecurityPolicy();

	foreach ($radioSources as $source) {
		$policy->addAllowedMediaDomain($source);
		$policy->addAllowedImageDomain($source); // for podcast images
	}

	// Also the media sources 'data:' and 'blob:' are needed for HLS streaming
	if ($enableHls) {
		$policy->addAllowedMediaDomain('data:');
		$policy->addAllowedMediaDomain('blob:');
	}

	$container->getServer()->getContentSecurityPolicyManager()->addDefaultPolicy($policy);
}

/**
 * Load embedded music player for Files and Sharing apps
 *
 * The nice way to do this would be
 * \OC::$server->getEventDispatcher()->addListener('OCA\Files::loadAdditionalScripts', $loadEmbeddedMusicPlayer);
 * \OC::$server->getEventDispatcher()->addListener('OCA\Files_Sharing::loadAdditionalScripts', $loadEmbeddedMusicPlayer);
 * ... but this doesn't work for shared files on ownCloud 10.0, at least. Hence, we load the scripts
 * directly if the requested URL seems to be for Files or Sharing.
 */
function loadEmbeddedMusicPlayer() {
	\OCA\MusicNC\Utility\HtmlUtil::addWebpackScript('files_music_player');
	\OCA\MusicNC\Utility\HtmlUtil::addWebpackStyle('files_music_player');
}

function isFilesUrl($url) {
	return \preg_match('%/apps/files/?$%', $url);
}

function isShareUrl($url) {
	return \preg_match('%/s/[^/]+$%', $url) && !\preg_match('%/apps/.*%', $url);
}

function isMusicUrl($url) {
	return \preg_match('%/apps/music/?$%', $url);
}

$request = \OC::$server->getRequest();
if (isset($request->server['REQUEST_URI'])) {
	$url = $request->server['REQUEST_URI'];
	$url = \explode('?', $url)[0]; // get rid of any query args
	$url = \explode('#', $url)[0]; // get rid of any hash part

	if (isFilesUrl($url) || isShareUrl($url)) {
		adjustCsp($c);
		loadEmbeddedMusicPlayer();
	} elseif (isMusicUrl($url)) {
		adjustCsp($c);
	}
}
