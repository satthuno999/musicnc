/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2012-2023 S P A R K
 */

'use strict';

if (!OCA.musicnc) {
    /**
     * @namespace
     */
    OCA.musicnc = {};
}

/**
 * @namespace OCA.musicnc.Player
 */
OCA.musicnc.Player = {
    html5Audio: document.getElementById('html5Audio'), // the <audio> element
    currentTrackIndex: 0,   // the index of the <source> list to be played
    currentPlaylist: 0,     // ID of the current playlist. Needed to recognize UI list changes
    currentTrackId: 0,      // current playing track id. Needed to recognize the current playing track in the playlist
    repeatMode: null,       // repeat mode null/single/list
    trackStartPosition: 0,  // start position of a track when the player is reopened and the playback should continue
    lastSavedSecond: 0,     // last autosaved second

    /**
     * set the track to the selected track index and check if it can be played at all
     * play/pause when the same track is selected or get a new one
     */
    setTrack: function () {
        let trackToPlay = this.html5Audio.children[this.currentTrackIndex];
        if (trackToPlay.dataset.canPlayMime === 'false') {
            this.next();
            return;
        }
        // new track to be played
        if (trackToPlay.src !== this.html5Audio.getAttribute('src')) {
            document.getElementById('playerPlay').classList.replace('play-pause', 'icon-loading')
            this.currentTrackId = trackToPlay.dataset.trackid;
            OCA.musicnc.Core.CategorySelectors[2] = trackToPlay.dataset.trackid;
            this.lastSavedSecond = 0;
            this.html5Audio.setAttribute('src', trackToPlay.src);
            this.html5Audio.load();
        } else if (!OCA.musicnc.Player.isPaused()) {
            OCA.musicnc.Player.stop();
            return;
        }
        let playPromise = this.html5Audio.play();
        if (playPromise !== undefined) {
            playPromise.then(function() {
                document.getElementById('playerPlay').classList.replace('icon-loading', 'play-pause');
                document.getElementById('sm2-bar-ui').classList.add('playing');
                OCA.musicnc.UI.indicateCurrentPlayingTrack();
            }).catch(function(error) {
                document.getElementById('playerPlay').classList.replace('icon-loading','play-pause');
                OCP.Toast.error(t('musicnc', 'Playback error'));
            });
        }
    },

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
        document.getElementById('playerPlay').classList.replace('icon-loading','play-pause');
        document.getElementById('playerPlay').classList.replace('play','play-pause');
        document.getElementById('sm2-bar-ui').classList.remove('playing');
        OCA.musicnc.UI.indicateCurrentPlayingTrack();
    },

    /**
     * pause => stop the playback
     */
    pause: function () {
        this.stop();
    },

    /**
     * select the next track and play it
     * it is dependent on repeat mode and possible end of playlist
     */
    next: function () {
        OCA.musicnc.Player.trackStartPosition = 0;
        OCA.musicnc.Player.lastSavedSecond = 0;
        let numberOfTracks = OCA.musicnc.Player.html5Audio.childElementCount - 1; // index stats counting at 0
        if (OCA.musicnc.Player.currentTrackIndex === numberOfTracks) {
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
     * toggle the repeat mode off->single->list->off
     */
    setRepeat: function (overwrite) {
        let repeatIcon = document.getElementById('playerRepeat');

        if (overwrite === 'single') {
            OCA.musicnc.Player.repeatMode = null;
        } else if (overwrite === 'list') {
            OCA.musicnc.Player.repeatMode = 'single';
        }

        if (OCA.musicnc.Player.repeatMode === null) {
            OCA.musicnc.Player.html5Audio.loop = true;
            OCA.musicnc.Player.repeatMode = 'single';
            repeatIcon.classList.remove('repeat');
            repeatIcon.classList.add('repeat-single');
            repeatIcon.style.opacity = '1';
            OCA.musicnc.Backend.setUserValue('repeat', 'single');
        } else if (OCA.musicnc.Player.repeatMode === 'single') {
            OCA.musicnc.Player.html5Audio.loop = false;
            OCA.musicnc.Player.repeatMode = 'list';
            repeatIcon.classList.add('repeat');
            repeatIcon.classList.remove('repeat-single');
            repeatIcon.style.opacity = '1';
            OCA.musicnc.Backend.setUserValue('repeat', 'list');
        } else {
            OCA.musicnc.Player.repeatMode = null;
            repeatIcon.style.removeProperty('opacity');
            OCA.musicnc.Backend.setUserValue('repeat', 'none');
        }
    },

    /**
     * toggle the shuffle mode true->false->true
     */
    shuffleTitles: function () {
        let playlist = document.getElementById('individual-playlist');

        let classes = document.getElementById('view-toggle').classList;
        if (classes.contains('icon-toggle-pictures')) {
            return;
        }

            let children = [].slice.call(playlist.childNodes);
        [].sort.call(children, function () {
            return 0.5 - Math.random();
        });
        children.forEach(function (child) {
            playlist.appendChild(child);
        });

        let playlistItems = document.querySelectorAll('.albumwrapper li');
        OCA.musicnc.Player.addTracksToSourceList(playlistItems);
    },

    /**
     * set the playback volume
     */
    setVolume: function () {
        OCA.musicnc.Player.html5Audio.volume = document.getElementById('playerVolume').value;
        OCA.musicnc.Backend.setUserValue('volume', document.getElementById('playerVolume').value   );
    },

    /**
     * get the playback volume
     */
    getVolume: function () {
        return OCA.musicnc.Player.html5Audio.volume;
    },

    /**
     * check, if the audio element is currently paused
     */
    isPaused: function () {
        return this.html5Audio.paused;
    },

    /**
     * take the playlist from the frontend and add then as source-elements to the audio tag
     * @param playlistItems
     */
    addTracksToSourceList: function (playlistItems) {
        OCA.musicnc.Player.html5Audio.innerHTML = '';
        for (let i = 0; i < playlistItems.length; ++i) {
            let audioSource = document.createElement('source');
            audioSource.src = playlistItems[i].firstChild.href;
            audioSource.dataset.trackid = playlistItems[i].dataset.trackid;
            audioSource.dataset.canPlayMime = playlistItems[i].dataset.canPlayMime;
            audioSource.dataset.title = playlistItems[i].dataset.title;
            audioSource.dataset.artist = playlistItems[i].dataset.artist;
            audioSource.dataset.album = playlistItems[i].dataset.album;
            audioSource.dataset.cover = playlistItems[i].dataset.cover;
            OCA.musicnc.Player.html5Audio.appendChild(audioSource);
        }
    },

    /**
     * Set the progress bar to the current playtime
     */
    initProgressBar: function () {
        let player = OCA.musicnc.Player.html5Audio;
        let canvas = document.getElementById('progressBar');
        if (player.currentTime !== 0) {
            document.getElementById('startTime').innerHTML = OCA.musicnc.Player.formatSecondsToTime(player.currentTime) + '&nbsp;/&nbsp;';
            document.getElementById('endTime').innerHTML = OCA.musicnc.Player.formatSecondsToTime(player.duration) + '&nbsp;&nbsp;';
        } else {
            // document.getElementById('startTime').innerHTML = t('musicnc', 'loading');
            // document.getElementById('endTime').innerHTML = '';
        }

        let elapsedTime = Math.round(player.currentTime);
        if (canvas.getContext) {
            let ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.clientWidth, canvas.clientHeight);
            ctx.fillStyle = 'rgb(0,130,201)';
            let progressValue = (elapsedTime / player.duration);
            let fWidth = progressValue * canvas.clientWidth;
            if (fWidth > 0) {
                ctx.fillRect(0, 0, fWidth, canvas.clientHeight);
            }
        }

        // save position every 10 seconds
        let positionCalc = Math.round(player.currentTime) / 10;
        if (Math.round(positionCalc) === positionCalc && positionCalc !== 0 && this.lastSavedSecond !== positionCalc) {
            this.lastSavedSecond = Math.round(positionCalc);
            OCA.musicnc.Backend.setUserValue('category',
                OCA.musicnc.Core.CategorySelectors[0]
                + '-' + OCA.musicnc.Core.CategorySelectors[1]
                + '-' + OCA.musicnc.Core.CategorySelectors[2]
                + '-' + Math.round(player.currentTime)
            );
        }

    },

    /**
     * set the tracktime when the progressbar is moved
     * @param evt
     */
    seek: function (evt) {
        let progressbar = document.getElementById('progressBar');
        let player = OCA.musicnc.Player.html5Audio;
        player.currentTime = player.duration * (evt.offsetX / progressbar.clientWidth);
    },

    /**
     * calculate a time in the format of 00:00 for the progress
     * @param value
     * @return string
     */
    formatSecondsToTime: function (value) {
        if (value <= 0 || isNaN(value)) {
            return '0:00';
        }
        value = Math.floor(value);
        let hours = Math.floor(value / 3600),
            minutes = Math.floor(value / 60 % 60),
            seconds = (value % 60);
        return (hours !== 0 ? String(hours) + ':' : '') + (hours !== 0 ? String(minutes).padStart(2, '0') : String(minutes)) + ':' + String(seconds).padStart(2, '0');
    },

    /**
     * get the currently playing track and provide its data (dataset) to e.g. playbar or sidebar
     * @return Element
     */
    getCurrentPlayingTrackInfo: function () {
        return this.html5Audio.children[this.currentTrackIndex];
    },
};

document.addEventListener('DOMContentLoaded', function () {
    OCA.musicnc.Player.html5Audio.addEventListener('ended', OCA.musicnc.Player.next, true);
    OCA.musicnc.Player.html5Audio.addEventListener('timeupdate', OCA.musicnc.Player.initProgressBar, true);
    OCA.musicnc.Player.html5Audio.addEventListener('canplay', function () {
        if (parseInt(OCA.musicnc.Player.trackStartPosition) !== 0 && OCA.musicnc.Player.html5Audio.currentTime !== parseInt(OCA.musicnc.Player.trackStartPosition)) {
            OCA.musicnc.Player.html5Audio.pause();
            OCA.musicnc.Player.html5Audio.currentTime = parseInt(OCA.musicnc.Player.trackStartPosition);
            OCA.musicnc.Player.html5Audio.play();
            OCA.musicnc.Player.trackStartPosition = 0; // reset the time to avoid that is being set again and again when seeking
        }
    });

    document.getElementById('progressBar').addEventListener('click', OCA.musicnc.Player.seek, true);
    document.getElementById('playerPrev').addEventListener('click', OCA.musicnc.Player.prev);
    document.getElementById('playerNext').addEventListener('click', OCA.musicnc.Player.next);
    document.getElementById('playerPlay').addEventListener('click', OCA.musicnc.Player.play);
    document.getElementById('playerRepeat').addEventListener('click', OCA.musicnc.Player.setRepeat);
    document.getElementById('playerShuffle').addEventListener('click', OCA.musicnc.Player.shuffleTitles);
    document.getElementById('playerVolume').addEventListener('input', OCA.musicnc.Player.setVolume);
    document.getElementById('playerVolume').value = document.getElementById('musicnc_volume').value;

    let repeat = document.getElementById('musicnc_repeat').value;
    if (repeat !== 'none') {
        OCA.musicnc.Player.setRepeat(repeat);
    }
    OCA.musicnc.Player.setVolume();
});
