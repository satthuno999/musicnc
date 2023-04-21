/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2021 S P A R K
 */
/** global: OC */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
    OCA.musicnc.Dashboard.init();
})

if (!OCA.musicnc) {
    /**
     * @namespace
     */
    OCA.musicnc = {};
}
OCA.musicnc.Player = {
    html5Audio: null,
    currentTrackIndex: 0,   // the index of the <source> list to be played
    currentPlaylist: 0,     // ID of the current playlist. Needed to recognize UI list changes
    repeatMode: null,       // repeat mode null/single/list
    shuffleHistory: [],     // array to store the track ids which were already played. Avoid multi playback in shuffle
    shuffle: false,         // shuffle mode false/true
    trackStartPosition: 0,  // start position of a track when the player is reopened and the playback should continue
    lastSavedSecond: 0,     // last autosaved second

    /**
     * set track and play it
     */
    play: function () {
        OCA.musicnc.Player.setTrack();
    },

    /**
     * stop the playback and update the UI with the paused track
     */
    stop: function () {
        this.html5Audio.pause();
        document.getElementById('playerPlay').classList.remove('playing');
        document.getElementById('audioplayerTitle').innerHTML = '';
    },

    /**
     * select the next track and play it
     * it is dependent on shuffle mode, repeat mode and possible end of playlist
     */
    next: function () {
        OCA.musicnc.Player.trackStartPosition = 0;
        OCA.musicnc.Player.lastSavedSecond = 0;
        var numberOfTracks = OCA.musicnc.Player.html5Audio.childElementCount - 1; // index stats counting at 0
        if (OCA.musicnc.Player.shuffle === true) {
            // shuffle => get random track index
            var minimum = 0;
            var maximum = numberOfTracks;
            var randomIndex = 0;
            var foundPlayedTrack = false;

            if (OCA.musicnc.Player.shuffleHistory.length === OCA.musicnc.Player.html5Audio.childElementCount) {
                OCA.musicnc.Player.stop();
                OCA.musicnc.Player.shuffleHistory = [];
                return;
            }

            do {
                randomIndex = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
                foundPlayedTrack = OCA.musicnc.Player.shuffleHistory.includes(randomIndex);
            } while (foundPlayedTrack === true);

            OCA.musicnc.Player.currentTrackIndex = randomIndex;
            OCA.musicnc.Player.shuffleHistory.push(randomIndex);
            OCA.musicnc.Player.setTrack();
        } else if (OCA.musicnc.Player.currentTrackIndex === numberOfTracks) {
            // if end is reached, either stop or restart the list
            if (OCA.musicnc.Player.repeatMode === 'list') {
                OCA.musicnc.Player.currentTrackIndex = 0;
                OCA.musicnc.Player.setTrack();
            } else {
                OCA.musicnc.Player.stop();
            }
        } else {
            OCA.musicnc.Player.currentTrackIndex++;
            OCA.musicnc.Player.setTrack();
        }
    },

    /**
     * select the previous track and play it
     */
    prev: function () {
        OCA.musicnc.Player.trackStartPosition = 0;
        OCA.musicnc.Player.lastSavedSecond = 0;
        OCA.musicnc.Player.currentTrackIndex--;
        OCA.musicnc.Player.setTrack();
    },

    /**
     * set the track to the selected track index and check if it can be played at all
     * play/pause when the same track is selected or get a new one
     */
    setTrack: function () {
        var trackToPlay = this.html5Audio.children[this.currentTrackIndex];
        if (trackToPlay.dataset.canPlayMime === 'false') {
            this.next();
            return;
        }
        // new track to be played
        if (trackToPlay.src !== this.html5Audio.getAttribute('src')) {
            document.getElementById('playerPlay').classList.replace('APplay-pause', 'icon-loading')
            this.lastSavedSecond = 0;
            this.html5Audio.setAttribute('src', trackToPlay.src);
            this.html5Audio.load();
        } else if (!this.html5Audio.paused) {
            OCA.musicnc.Player.stop();
            return;
        }

        let playPromise = this.html5Audio.play();
        if (playPromise !== undefined) {
            playPromise.then(_ => {
                document.getElementById('playerPlay').classList.replace('icon-loading', 'APplay-pause');
                document.getElementById('playerPlay').classList.add('playing');
                OCA.musicnc.Player.indicateCurrentPlayingTrack();
            })
                .catch(error => {
                    OCA.musicnc.Player.stop();
                    document.getElementById('playerPlay').classList.replace('icon-loading','icon-loading');
                    //document.getElementById('playerPlay').classList.replace('APplay-pause','play');
                });
        }

    },

    indicateCurrentPlayingTrack: function () {
        //in every case, update the playbar and medaservices
        var coverUrl = OC.generateUrl('apps/musicnc/getcover/');
        var currentTrack = this.html5Audio.children[this.currentTrackIndex];

        if (currentTrack) {
            var addCss;
            var addDescr;
            var coverID = currentTrack.dataset.cover;
            if (coverID === 'null') {
                addCss = 'background-color: #D3D3D3;color: #333333;';
                addDescr = currentTrack.dataset.title[0];
                if ('mediaSession' in navigator) {
                    navigator.mediaSession.metadata = new MediaMetadata({
                        title: currentTrack.dataset.title,
                        artist: currentTrack.dataset.artist,
                        album: currentTrack.dataset.album,
                    });
                }
            } else {
                addCss = 'background-image:url(' + coverUrl + coverID + ');height: 180px;';
                addDescr = '';
                if ('mediaSession' in navigator) {
                    navigator.mediaSession.metadata = new MediaMetadata({
                        title: currentTrack.dataset.title,
                        artist: currentTrack.dataset.artist,
                        album: currentTrack.dataset.album,
                        artwork: [
                            {src: coverUrl + coverID, sizes: '192x192', type: 'image/png'},
                        ]
                    });
                }
            }
            document.getElementById('audioplayerCover').setAttribute('style', addCss);
            document.getElementById('audioplayerCover').innerText = addDescr;

            let currentCount = this.currentTrackIndex+1 + '/' + this.html5Audio.childElementCount + ': ';
            document.getElementById('audioplayerTitle').innerHTML = currentCount + currentTrack.dataset.title;
        }
    },


}

/**
 * @namespace OCA.musicnc.Dashboard
 */
OCA.musicnc.Dashboard = {
    AjaxCallStatus: null,
    canPlayMimeType: [],

    init: function () {
        if (typeof OCA.Dashboard === 'object') {
            OCA.Dashboard.register('musicnc', (el) => {
                //el.innerHTML = '<ul id="ulAudioplayer"></ul>';
                el.innerHTML = OCA.musicnc.Dashboard.buildPlayer() +
                    OCA.musicnc.Dashboard.buildCategoryDropdown() +
                    OCA.musicnc.Dashboard.buildItemDropdown() +
                    OCA.musicnc.Dashboard.buildCurrentTitle() +
                    OCA.musicnc.Dashboard.buildItemCover();
                OCA.musicnc.Dashboard.initActions();
            });
        }
    },

    initActions: function () {
        document.getElementById('audiplayerCategory').addEventListener('change', OCA.musicnc.Dashboard.loadCategory);
        document.getElementById('audioplayerItem').addEventListener('change', OCA.musicnc.Dashboard.getTracks);
        document.getElementById('playerPrev').addEventListener('click', OCA.musicnc.Player.prev);
        document.getElementById('playerNext').addEventListener('click', OCA.musicnc.Player.next);
        document.getElementById('playerPlay').addEventListener('click', OCA.musicnc.Player.play);
        OCA.musicnc.Player.html5Audio = document.getElementById('html5Audio');
        OCA.musicnc.Player.html5Audio.addEventListener('ended', OCA.musicnc.Player.next, true);

        // mediaSession currently use for Chrome already to support hardware keys
        if ('mediaSession' in navigator) {
            navigator.mediaSession.setActionHandler('play', function () {
                OCA.musicnc.Player.play();
            });
            navigator.mediaSession.setActionHandler('pause', function () {
                OCA.musicnc.Player.stop();
            });
            navigator.mediaSession.setActionHandler('stop', function () {
                OCA.musicnc.Player.stop();
            });
            navigator.mediaSession.setActionHandler('previoustrack', function () {
                OCA.musicnc.Player.prev();
            });
            navigator.mediaSession.setActionHandler('nexttrack', function () {
                OCA.musicnc.Player.next();
            });
        }

        // evaluate if browser can play the mimetypes
        let mimeTypes = ['audio/mpeg', 'audio/mp4', 'audio/ogg', 'audio/wav', 'audio/flac', 'audio/x-aiff', 'audio/aac'];
        let mimeTypeAudio = document.createElement('audio');
        mimeTypes.forEach((element) => {
            if (mimeTypeAudio.canPlayType(element)) {
                OCA.musicnc.Dashboard.canPlayMimeType.push(element);
            }
        });
        // add playlist mimetypes
        OCA.musicnc.Dashboard.canPlayMimeType.push('audio/mpegurl', 'audio/x-scpls', 'application/xspf+xml');
    },

    buildPlayer: function () {
        return '<div id="" class="APplayerBar">'
            + '<div class="APplayerButton" title="' + t('musicnc', 'Previous track') + '">'
            + '<div id="playerPrev" class="APbutton button APprevious"></div></div>'
            + '<div class="APplayerButton" title="' + t('musicnc', 'Play/Pause') + '">'
            + '<div id="playerPlay" class="APbutton button APplay-pause"></div></div>'
            + '<div class="APplayerButton" title="' + t('musicnc', 'Next track') + '">'
            + '<div id="playerNext" class="APbutton button APnext"></div></div><audio id="html5Audio" hidden=""></audio></div>';
    },

    buildCategoryDropdown: function () {
        return '<div class="APcategoryBar">\n' +
            '<select id="audiplayerCategory" style="width: 180px;">\n' +
            '<option value="" selected>' + t('musicnc', 'Selection') + '</option>\n' +
            '<option value="Playlist">' + t('musicnc', 'Playlists') + '</option>\n' +
            '<option value="Album">' + t('musicnc', 'Albums') + '</option>\n' +
            '<option value="Album Artist">' + t('musicnc', 'Album Artists') + '</option>\n' +
            '<option value="Artist">' + t('musicnc', 'Artists') + '</option>\n' +
            '<option value="Folder">' + t('musicnc', 'Folders') + '</option>\n' +
            '<option value="Genre">' + t('musicnc', 'Genres') + '</option>\n' +
            '<option value="Title">' + t('musicnc', 'Titles') + '</option>\n' +
            '<option value="Tags">' + t('musicnc', 'Tags') + '</option>' +
            '<option value="Year">' + t('musicnc', 'Years') + '</option>\n' +
            '</select>\n' +
            '</div>\n'
    },

    buildItemDropdown: function () {
        return '<div  class="APitemBar">\n' +
            '<select id="audioplayerItem" style="width: 180px;">\n' +
            '</select>\n' +
            '</div>\n'
    },

    buildItemCover: function () {
        return '<div class="APcoverBar">\n' +
            '<div id="audioplayerLoading" style="text-align:center; padding-top:100px" class="icon-loading" hidden></div>' +
            '<div id="audioplayerCover" class="cover"></div>' +
            '</div>\n'
    },

    buildCurrentTitle: function () {
        return '<div class="APtitleBar">\n' +
            '<div id="audioplayerTitle" style="width: 180px;">\n' +
            '</div>\n' +
            '</div>\n'
    },

    showElement: function (element) {
        if (document.getElementById(element)) {
            document.getElementById(element).hidden = false;
        }
    },

    hideElement: function (element) {
        if (document.getElementById(element)) {
            document.getElementById(element).hidden = true;
        }
    },

    loadCategory: function () {
        var category = document.getElementById('audiplayerCategory').value;
        OCA.musicnc.Dashboard.showElement('audioplayerLoading');

        $.ajax({
            type: 'GET',
            url: OC.generateUrl('apps/musicnc/getcategoryitems'),
            data: {category: category},
            success: function (jsondata) {
                if (jsondata.status === 'success') {
                    let select = document.getElementById('audioplayerItem')
                    select.innerHTML = '<option value="" selected>' + t('musicnc', 'Selection') + '</option>';

                    for (var categoryData of jsondata.data) {
                        var optionElement = document.createElement('option');
                        optionElement.value = categoryData.id;
                        optionElement.innerHTML = categoryData.name;
                        select.appendChild(optionElement);
                    }
                    OCA.musicnc.Dashboard.hideElement('audioplayerLoading');
                }
            }
        });
        return true;
    },

    getTracks: function (callback, covers, albumDirectPlay) {

        OCA.musicnc.Dashboard.showElement('audioplayerLoading');
        if (OCA.musicnc.Dashboard.AjaxCallStatus !== null) {
            OCA.musicnc.Dashboard.AjaxCallStatus.abort();
        }

        let category = document.getElementById('audiplayerCategory').value;
        let categoryItem = document.getElementById('audioplayerItem').value;
        let player = document.getElementById('html5Audio');
        let canPlayMimeType = OCA.musicnc.Dashboard.canPlayMimeType;

        OCA.musicnc.Dashboard.AjaxCallStatus = $.ajax({
            type: 'GET',
            url: OC.generateUrl('apps/musicnc/gettracks'),
            data: {category: category, categoryId: categoryItem},
            success: function (jsondata) {
                //document.getElementById('loading').style.display = 'none';
                if (jsondata.status === 'success') {

                    player.innerHTML = '';
                    for (let itemData of jsondata.data) {

                        let streamUrl;
                        if (itemData['mim'] === 'audio/mpegurl' || itemData['mim'] === 'audio/x-scpls' || itemData['mim'] === 'application/xspf+xml') {
                            streamUrl = itemData['lin'];
                            jsondata.data = [];
                            break;
                        } else {
                            streamUrl = OC.generateUrl('apps/musicnc/getaudiostream') + '?t=' + itemData['id'];
                        }

                        let canPlayMime
                        if (canPlayMimeType.includes(itemData['mim'])) {
                            canPlayMime = true;
                        } else {
                            canPlayMime = 'false';
                        }

                        let audioSource = document.createElement('source');
                        audioSource.src = streamUrl;
                        audioSource.dataset.trackid = itemData['id'];
                        audioSource.dataset.title = itemData['cl1'];
                        audioSource.dataset.artist = itemData['cl2'];
                        audioSource.dataset.album = itemData['cl3'];
                        audioSource.dataset.cover = itemData['cid'];
                        audioSource.dataset.canPlayMime = canPlayMime;
                        player.appendChild(audioSource);
                    }
                    document.getElementById('audioplayerTitle').innerHTML = jsondata.data.length + ' ' + t('musicnc', 'Titles');
                } else {
                    document.getElementById('audioplayerTitle').innerHTML = t('musicnc', 'No data');
                }
                OCA.musicnc.Dashboard.hideElement('audioplayerLoading');
                document.getElementById('audioplayerCover').removeAttribute('style');
                document.getElementById('audioplayerCover').innerText = '';
                OCA.musicnc.Player.currentTrackIndex = 0;
            }
        });
    },
}