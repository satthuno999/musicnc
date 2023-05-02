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
'use strict';

var audioPlayer = {
    mime: null,
    file: null,
    location: null,
    player: null,
    dir: null
};

function playFile(file, data) {
    file = encodeURIComponent(file);
    musicnc.file = file;
    musicnc.dir = data.dir;
    var token = ($('#sharingToken').val() !== undefined) ? $('#sharingToken').val() : '';
    var dirLoad = data.dir.substr(1);
    if (dirLoad !== '') {
        dirLoad = dirLoad + '/';
    }
    if (token !== '') {
        musicnc.location = OC.generateUrl('apps/musicnc/getpublicaudiostream?token={token}&file={file}', {
            'token': token,
            'file': dirLoad + file
        }, {escape: false});
    } else {
        musicnc.location = OC.generateUrl('apps/musicnc/getaudiostream?file={file}', {'file': dirLoad + file}, {escape: true});
    }
    musicnc.mime = data.$file.attr('data-mime');
    data.$file.find('.thumbnail').html('<i class="ioc ioc-volume-up"  style="color:#fff;margin-left:5px; text-align:center;line-height:32px;text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;font-size: 24px;"></i>');

    if (musicnc.player === null) {
        musicnc.player = document.createElement('audio');
        musicnc.player.setAttribute('src', musicnc.location);
        musicnc.player.load();
        musicnc.player.play();
    } else {
        musicnc.player.pause();
        $('#filestable').find('.thumbnail i.ioc-volume-up').hide();
        musicnc.player = null;
    }
}

function registerFileActions() {
    var mimeTypes = ['audio/mpeg', 'audio/mp4', 'audio/m4b', 'audio/ogg', 'audio/wav', 'audio/flac', 'audio/x-aiff', 'audio/aac'];
    var icon_url = OC.imagePath('core', 'actions/sound');
    const audio = document.createElement('audio');

    mimeTypes.forEach((element) => {
        if (audio.canPlayType(element)) {
            OCA.Files.fileActions.registerAction({
                name: 'audio',
                displayName: 'Play',
                mime: element,
                permissions: OC.PERMISSION_READ,
                icon: icon_url,
                actionHandler: playFile
            });
            OCA.Files.fileActions.setDefault(element, 'audio');
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof OCA !== 'undefined' && typeof OCA.Files !== 'undefined' && typeof OCA.Files.fileActions !== 'undefined' && $('#header').hasClass('share-file') === false) {
        registerFileActions();
    }
    return true;
});