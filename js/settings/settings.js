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
 * @namespace OCA.musicnc.Settings
 */
OCA.musicnc.Settings = {

    percentage: 0,

    openResetDialog: function () {
        OC.dialogs.confirm(
            t('musicnc', 'Are you sure?') + ' ' + t('musicnc', 'All library entries will be deleted!'),
            t('musicnc', 'Reset library'),
            function (e) {
                if (e === true) {
                    OCA.musicnc.Settings.resetLibrary();
                }
            },
            true
        );
    },

    resetLibrary: function () {
        if ($('.sm2-bar-ui').hasClass('playing')) {
            OCA.musicnc.Player.currentTrackIndex = 0;
            OCA.musicnc.Player.stop();
        }

        OCA.musicnc.UI.showInitScreen();

        $('#category_selector').val('');
        OCA.musicnc.Backend.setUserValue('category', OCA.musicnc.Core.CategorySelectors[0] + '-');
        $('#myCategory').html('');
        $('#alben').addClass('active');
        $('#individual-playlist').remove();
        $('#individual-playlist-info').hide();
        $('#individual-playlist-header').hide();
        $('.coverrow').remove();
        $('.songcontainer').remove();
        $('#activePlaylist').html('');
        $('.sm2-playlist-target').html('');
        $('.sm2-playlist-cover').css('background-color', '#ffffff').html('');

        $.ajax({
            type: 'GET',
            url: OC.generateUrl('apps/musicnc/resetmedialibrary'),
            success: function (jsondata) {
                if (jsondata.status === 'success') {
                    OCP.Toast.success(t('musicnc', 'Resetting finished!'));
                }
            }
        });
    },

    prepareScanDialog: function () {
        $('body').append('<div id="audios_import"></div>');
        $('#audios_import').load(OC.generateUrl('apps/musicnc/getimporttpl'), function () {
            OCA.musicnc.Settings.openScanDialog();
        });
    },

    openScanDialog: function () {

        $('#audios_import_dialog').ocdialog({
            width: 500,
            modal: true,
            resizable: false,
            close: function () {
                OCA.musicnc.Settings.stopScan();
                $('#audios_import_dialog').ocdialog('destroy');
                $('#audios_import').remove();
            }
        });

        $('#audios_import_done_close').click(function () {
            OCA.musicnc.Settings.percentage = 0;
            $('#audios_import_dialog').ocdialog('close');
        });

        $('#audios_import_progress_cancel').click(function () {
            OCA.musicnc.Settings.stopScan();
        });

        $('#audios_import_submit').click(function () {
            OCA.musicnc.Settings.processScan();
        });

        $('#audios_import_progressbar').progressbar({value: 0});
    },

    processScan: function () {
        $('#audios_import_form').css('display', 'none');
        $('#audios_import_process').css('display', 'block');
        OCA.musicnc.Settings.startScan();
    },

    startScan: function () {
        var scanUrl = OC.generateUrl('apps/musicnc/scanforaudiofiles');
        var source = new OC.EventSource(scanUrl);
        source.listen('progress', OCA.musicnc.Settings.updateScanProgress);
        source.listen('done', OCA.musicnc.Settings.scanDone);
        source.listen('error', OCA.musicnc.Settings.scanError);
    },

    stopScan: function () {
        OCA.musicnc.Settings.percentage = 0;
        $.ajax({
            type: 'GET',
            url: OC.generateUrl('apps/musicnc/scanforaudiofiles'),
            data: {
                'scanstop': true
            },
            success: function () {
            }
        });
    },

    updateScanProgress: function (message) {
        var data = JSON.parse(message);
        OCA.musicnc.Settings.percentage = data.filesProcessed / data.filesTotal * 100;
        $('#audios_import_progressbar').progressbar('option', 'value', OCA.musicnc.Settings.percentage);
        $('#audios_import_process_progress').text(`${data.filesProcessed}/${data.filesTotal}`);
        $('#audios_import_process_message').text(data.currentFile);
    },

    scanDone: function (message) {
        var data = JSON.parse(message);
        $('#audios_import_process').css('display', 'none');
        $('#audios_import_done').css('display', 'block');
        $('#audios_import_done_message').html(data.message);
        OCA.musicnc.Core.init();
    },

    scanError: function (message) {
        var data = JSON.parse(message);
        $('#audios_import_progressbar').progressbar('option', 'value', 100);
        $('#audios_import_done_message').text(data.message);
    },
};

document.addEventListener('DOMContentLoaded', function () {

    var settings_link;
    if (OC.config.versionstring.split('.')[0] <= 10) //ownCloud
    {
        settings_link = OC.generateUrl('settings/personal?sectionid=audioplayer');
    } else { //Nextcloud
        settings_link = OC.generateUrl('settings/user/musicnc');
    }

    $('#sonos').on('click', function () {
        document.location = settings_link;
    });

    $('#audioplayerSettings').on('click', function () {
        document.location = settings_link;
    });

    $(document).on('click', '#scanAudios, #scanAudiosFirst', function () {
        OCA.musicnc.Settings.prepareScanDialog();
    });

    $(document).on('click', '#resetAudios', function () {
        OCA.musicnc.Settings.openResetDialog();
    });
});