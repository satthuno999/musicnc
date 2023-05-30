<?php

namespace OCA\musicnc\AppInfo;

return [
    'resources' => [
        'favoriteradio' => ['url' => '/api/favoritesradio'],
        'recentradio' => ['url' => '/api/recentradio'],
        'exportradio' => ['url' => '/exportradio'],
        'stationradio' => ['url' => '/stationradio'],
    ],
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'playlist#addTrackToPlaylist', 'url' => '/addtracktoplaylist', 'verb' => 'POST'],
        ['name' => 'playlist#addPlaylist', 'url' => '/addplaylist', 'verb' => 'POST'],
        ['name' => 'playlist#updatePlaylist', 'url' => '/updateplaylist', 'verb' => 'POST'],
        ['name' => 'playlist#sortPlaylist', 'url' => '/sortplaylist', 'verb' => 'POST'],
        ['name' => 'playlist#removePlaylist', 'url' => '/removeplaylist', 'verb' => 'POST'],
        ['name' => 'playlist#removeTrackFromPlaylist', 'url' => '/removetrackfromplaylist', 'verb' => 'POST'],
        ['name' => 'scanner#getImportTpl', 'url' => '/getimporttpl', 'verb' => 'GET'],
        ['name' => 'scanner#scanForAudios', 'url' => '/scanforaudiofiles', 'verb' => 'GET'],
        ['name' => 'scanner#checkNewTracks', 'url' => '/checknewtracks', 'verb' => 'POST'],
        ['name' => 'music#getAudioStream', 'url' => '/getaudiostream', 'verb' => 'GET'],
        ['name' => 'music#getPublicAudioStream', 'url' => '/getpublicaudiostream', 'verb' => 'GET'],
        ['name' => 'db#resetMediaLibrary', 'url' => '/resetmedialibrary', 'verb' => 'GET'],
        ['name' => 'music#getPublicAudioInfo', 'url' => '/getpublicaudioinfo', 'verb' => 'GET'],
        ['name' => 'cover#getCover', 'url' => '/getcover/{album}', 'verb' => 'GET'],

        ['name' => 'setting#setValue', 'url' => '/setvalue', 'verb' => 'GET'],
        ['name' => 'setting#getValue', 'url' => '/getvalue', 'verb' => 'GET'],
        ['name' => 'setting#userPath', 'url' => '/userpath', 'verb' => 'POST'],
        ['name' => 'setting#setFavorite', 'url' => '/setfavorite', 'verb' => 'GET'],
        ['name' => 'setting#setStatistics', 'url' => '/setstatistics', 'verb' => 'GET'],
        ['name' => 'setting#admin', 'url' => '/admin', 'verb' => 'POST'],

        ['name' => 'category#getCategoryItems', 'url' => '/getcategoryitems', 'verb' => 'GET'],
        ['name' => 'category#getCategoryItemCovers', 'url' => '/getcategoryitemcovers', 'verb' => 'GET'],
        ['name' => 'category#getTracks', 'url' => '/gettracks', 'verb' => 'GET'],
        ['name' => 'sidebar#getAudioInfo', 'url' => '/getaudioinfo', 'verb' => 'GET'],
        ['name' => 'sidebar#getPlaylists', 'url' => '/getplaylists', 'verb' => 'POST'],

        //radio view
        // ['name' => 'radioapi#index', 'url' => '/radioview', 'verb' => 'GET'],
        ['name' => 'radio#getAllByApi', 'url' => '/getradioapi', 'verb' => 'GET'],
        ['name' => 'radio#getAllByLang', 'url' => '/getradiolang', 'verb' => 'GET'],

        //musicapi
        ['name' => 'zing#searchName', 'url' => '/getmusicapi', 'verb' => 'POST'],
        // radio API
        // ['name' => 'radioapi#getAllByApi', 'url' => '/api/getradioapi', 'verb' => 'GET'],
        // ['name' => 'radioApi#getAll', 'url' => '/api/radio', 'verb' => 'GET'],
        // ['name' => 'radioApi#create', 'url' => '/api/radio', 'verb' => 'POST'],
        // ['name' => 'radioApi#exportAllToFile', 'url' => '/api/radio/export', 'verb' => 'POST'],
        // ['name' => 'radioApi#importFromFile', 'url' => '/api/radio/import', 'verb' => 'POST'],
        // ['name' => 'radioApi#resetAll', 'url' => '/api/radio/reset', 'verb' => 'POST'],
        // ['name' => 'radioApi#resolveStreamUrl', 'url' => '/api/radio/streamurl', 'verb' => 'GET'],
        // ['name' => 'radioApi#hlsManifest', 'url' => '/api/radio/hls/manifest', 'verb' => 'GET'],
        // ['name' => 'radioApi#hlsSegment', 'url' => '/api/radio/hls/segment', 'verb' => 'GET'],
        // ['name' => 'radioApi#get', 'url' => '/api/radio/{id}', 'verb' => 'GET'],
        // ['name' => 'radioApi#delete', 'url' => '/api/radio/{id}', 'verb' => 'DELETE'],
        // ['name' => 'radioApi#update', 'url' => '/api/radio/{id}', 'verb' => 'PUT'],
        // ['name' => 'radioApi#getChannelInfo', 'url' => '/api/radio/{id}/info', 'verb' => 'GET'],
        // ['name' => 'radioApi#stationStreamUrl', 'url' => '/api/radio/{id}/streamurl', 'verb' => 'GET'],

        // podcast API
        ['name' => 'podcastApi#getAll', 'url' => '/api/podcasts', 'verb' => 'GET'],
        ['name' => 'podcastApi#subscribe', 'url' => '/api/podcasts', 'verb' => 'POST'],
        ['name' => 'podcastApi#get', 'url' => '/api/podcasts/{id}', 'verb' => 'GET'],
        ['name' => 'podcastApi#channelDetails', 'url' => '/api/podcasts/{id}/details', 'verb' => 'GET'],
        ['name' => 'podcastApi#episodeDetails', 'url' => '/api/podcasts/episodes/{id}/details', 'verb' => 'GET'],
        ['name' => 'podcastApi#unsubscribe', 'url' => '/api/podcasts/{id}', 'verb' => 'DELETE'],
        ['name' => 'podcastApi#updateChannel', 'url' => '/api/podcasts/{id}/update', 'verb' => 'POST'],
        ['name' => 'podcastApi#resetAll', 'url' => '/api/podcasts/reset', 'verb' => 'POST'],

        // whatsnew
        ['name' => 'whatsNew#get', 'url' => '/whatsnew', 'verb' => 'GET'],
        ['name' => 'whatsNew#dismiss', 'url' => '/whatsnew', 'verb' => 'POST'],
    ]
];