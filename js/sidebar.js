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
if (!OCA.musicnc.Sidebar) {
    /**
     * @namespace
     */
    OCA.musicnc.Sidebar = {};
}

/**
 * @namespace OCA.musicnc.Sidebar
 */
OCA.musicnc.Sidebar = {
    sidebar_tabs: {},

    showSidebar: function (evt, trkid) {
        if (typeof trkid !== 'undefined') {
            var trackid = trkid;
        } else {
            var targetPlaylistItem = evt.target.closest('li');
            var trackid = targetPlaylistItem.getAttribute('data-trackid');
        }

        var appsidebar = document.getElementById('app-sidebar');

        if (appsidebar.dataset.trackid === trackid) {
            OCA.musicnc.Sidebar.hideSidebar();
        } else {
            var getcoverUrl = OC.generateUrl('apps/musicnc/getcover/');
            var trackData = $('li[data-trackid=\'' + trackid + '\']');
            var cover = trackData.attr('data-cover');
            var sidebarThumbnail = $('#sidebarThumbnail');
            $('.thumbnailContainer').addClass('portrait large');

            if (cover !== '') {
                sidebarThumbnail.attr({
                    'style': 'background-image:url(' + getcoverUrl + cover + ')'
                });
            } else {
                sidebarThumbnail.attr({
                    'style': 'display: none;'
                });
            }

            document.getElementById('sidebarTitle').innerHTML = decodeURIComponent(trackData.attr('data-title'));
            document.getElementById('sidebarMime').innerHTML = trackData.attr('data-mimetype');

            var starIcon = $('#sidebarFavorite').attr({'data-trackid': trackid});
            starIcon.off();
            starIcon.on('click', OCA.musicnc.Core.toggleFavorite);

            if (appsidebar.dataset.trackid === '') {
                $('#sidebarClose').on('click', OCA.musicnc.Sidebar.hideSidebar);

                OCA.musicnc.Sidebar.constructTabs();
                $('#tabHeaderMetadata').addClass('selected');
                OC.Apps.showAppSidebar();
            }

            appsidebar.dataset.trackid = trackid;
            $('.tabHeader.selected').trigger('click');
            OCA.musicnc.UI.resizePlaylist();
        }
    },

    registerSidebarTab: function (tab) {
        var id = tab.id;
        this.sidebar_tabs[id] = tab;
    },

    constructTabs: function () {
        var tab = {};

        document.querySelector('.tabHeaders').innerHTML = '';
        document.querySelector('.tabsContainer').innerHTML = '';

        OCA.musicnc.Sidebar.registerSidebarTab({
            id: 'tabHeaderAddons',
            class: 'addonsTabView',
            tabindex: '9',
            name: t('musicnc', 'Add-ons'),
            action: OCA.musicnc.Sidebar.addonsTabView,
        });

        OCA.musicnc.Sidebar.registerSidebarTab({
            id: 'tabHeaderMetadata',
            class: 'metadataTabView',
            tabindex: '1',
            name: t('musicnc', 'Metadata'),
            action: OCA.musicnc.Sidebar.metadataTabView,
        });

        OCA.musicnc.Sidebar.registerSidebarTab({
            id: 'tabHeaderPlaylists',
            class: 'playlistsTabView',
            tabindex: '2',
            name: t('musicnc', 'Playlists'),
            action: OCA.musicnc.Sidebar.playlistsTabView,
        });

        var items = _.map(OCA.musicnc.Sidebar.sidebar_tabs, function (item) {
            return item;
        });
        items.sort(OCA.musicnc.Sidebar.sortByName);

        for (tab in items) {
            var li = $('<li/>').addClass('tabHeader')
                .attr({
                    'id': items[tab].id,
                    'tabindex': items[tab].tabindex
                });
            var atag = $('<a/>').text(items[tab].name);
            atag.prop('title', items[tab].name);
            li.append(atag);
            $('.tabHeaders').append(li);

            var div = $('<div/>').addClass('tab ' + items[tab].class)
                .attr({
                    'id': items[tab].class
                });
            $('.tabsContainer').append(div);
            $('#' + items[tab].id).on('click', items[tab].action);
        }
    },

    hideSidebar: function () {
        document.getElementById('app-sidebar').dataset.trackid = '';
        OC.Apps.hideAppSidebar();
        document.querySelector('.tabHeaders').innerHTML = '';
        document.querySelector('.tabsContainer').innerHTML = '';
        OCA.musicnc.UI.resizePlaylist();
    },

    metadataTabView: function () {
        var trackid = document.getElementById('app-sidebar').dataset.trackid;

        OCA.musicnc.Sidebar.resetView();
        $('#tabHeaderMetadata').addClass('selected');
        $('#metadataTabView').removeClass('hidden').html('<div style="text-align:center; word-wrap:break-word;" class="get-metadata"><p><img src="' + OC.imagePath('core', 'loading.gif') + '"><br><br></p><p>' + t('musicnc', 'Reading data') + '</p></div>');

        $.ajax({
            type: 'GET',
            url: OC.generateUrl('apps/musicnc/getaudioinfo'),
            data: {trackid: trackid},
            success: function (jsondata) {
                var table;
                if (jsondata.status === 'success') {

                    table = $('<div>').css('display', 'table').addClass('table');
                    var tablerow;
                    var m;
                    var tablekey;
                    var tablevalue;
                    var tablevalueDownload;

                    var audioinfo = jsondata.data;
                    for (m in audioinfo) {
                        tablerow = $('<div>').css('display', 'table-row');
                        tablekey = $('<div>').addClass('key').text(t('musicnc', m));
                        tablevalue = $('<div>').addClass('value')
                            .text(audioinfo[m]);
                        if (m === 'Path') {
                            tablevalue.text('');
                            tablevalueDownload = $('<a>').attr('href', OC.linkToRemote('webdav' + audioinfo[m])).text(audioinfo[m]);
                            tablevalue.append(tablevalueDownload);
                        }
                        tablerow.append(tablekey).append(tablevalue);

                        if (m === 'fav' && audioinfo[m] === 't') {
                            $('#sidebarFavorite').removeClass('icon-star')
                                .addClass('icon-starred')
                                .prop('title', t('files', 'Favorited'));
                            audioinfo[m] = '';
                        } else if (m === 'fav') {
                            $('#sidebarFavorite').removeClass('icon-starred')
                                .addClass('icon-star')
                                .prop('title', t('files', 'Favorite'));
                            audioinfo[m] = '';
                        }

                        if (audioinfo[m] !== '' && audioinfo[m] !== null) {
                            table.append(tablerow);
                        }
                    }
                } else {
                    table = '<div style="margin-left: 2em;" class="get-metadata"><p>' + t('musicnc', 'No data') + '</p></div>';
                }

                $('#metadataTabView').html(table);
            }
        });
    },

    playlistsTabView: function () {
        var trackid = document.getElementById('app-sidebar').dataset.trackid;

        OCA.musicnc.Sidebar.resetView();
        $('#tabHeaderPlaylists').addClass('selected');
        $('#playlistsTabView').removeClass('hidden').html('<div style="text-align:center; word-wrap:break-word;" class="get-metadata"><p><img src="' + OC.imagePath('core', 'loading.gif') + '"><br><br></p><p>' + t('musicnc', 'Reading data') + '</p></div>');

        $.ajax({
            type: 'POST',
            url: OC.generateUrl('apps/musicnc/getplaylists'),
            data: {trackid: trackid},
            success: function (jsondata) {
                var table;
                if (jsondata.status === 'success') {

                    table = $('<div>').css('display', 'table').addClass('table');
                    var tablerow;
                    var m;
                    var tablekey;
                    var tablevalue;

                    var audioinfo = jsondata.data;
                    for (m in audioinfo) {
                        var spanDelete = $('<a/>').attr({
                            'class': 'icon icon-delete toolTip',
                            'data-listid': audioinfo[m].playlist_id,
                            'data-trackid': trackid,
                            'title': t('musicnc', 'Remove')
                        }).on('click', OCA.musicnc.Playlists.removeSongFromPlaylist);

                        tablerow = $('<div>').css('display', 'table-row').attr({'data-id': audioinfo[m].playlist_id});
                        tablekey = $('<div>').addClass('key').append(spanDelete);

                        tablevalue = $('<div>').addClass('value')
                            .text(audioinfo[m].name);
                        tablerow.append(tablekey).append(tablevalue);
                        table.append(tablerow);
                    }
                } else {
                    table = '<div style="margin-left: 2em;" class="get-metadata"><p>' + t('musicnc', 'No playlist entry') + '</p></div>';
                }

                $('#playlistsTabView').html(table);
            }
        });

    },

    addonsTabView: function () {
        OCA.musicnc.Sidebar.resetView();
        $('#tabHeaderAddons').addClass('selected');
        var html = '<div style="margin-left: 2em; background-position: initial;" class="icon-info">';
        html += '<p style="margin-left: 2em;">' + t('musicnc', 'Available Audio Player Add-Ons:') + '</p>';
        html += '<p style="margin-left: 2em;"><br></p>';
        html += '<a href="https://github.com/rello/musicnc_sonos"  target="_blank" >';
        html += '<p style="margin-left: 2em;">- ' + t('musicnc', 'SONOS playback') + '</p>';
        html += '</a></div>';
        $('#addonsTabView').removeClass('hidden').html(html);
    },

    resetView: function () {
        $('.tabHeader.selected').removeClass('selected');
        $('.tab').addClass('hidden');
    },

    sortByName: function (a, b) {
        var aName = a.tabindex;
        var bName = b.tabindex;
        return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
    },
};