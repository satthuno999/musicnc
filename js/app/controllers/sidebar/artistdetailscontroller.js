/**
 * ownCloud - Music app
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Pauli Järvinen <pauli.jarvinen@gmail.com>
 * @copyright Pauli Järvinen 2020
 */


angular.module('Musicnc').controller('ArtistDetailsController', [
	'$rootScope', '$scope', 'Restangular', 'gettextCatalog', 'libraryService',
	function ($rootScope, $scope, Restangular, gettextCatalog, libraryService) {

		function resetContents() {
			$scope.artist = null;
			$scope.artistAlbumTrackCount = 0;
			$scope.artistTrackCount = 0;
			$scope.loading = true;
			$scope.artAvailable = false;
			$scope.lastfmInfo = null;
			$scope.artistBio = null;
			$scope.artistTags = null;
			$scope.similarArtistsInLib = null;
			$scope.similarArtistsNotInLib = null;
			$scope.allSimilarShown = false;
			$scope.allSimilarLoading = false;
		}
		resetContents();

		function showDetails(artistId) {
			if (!$scope.artist || artistId != $scope.artist.id) {
				resetContents();

				$scope.artist = libraryService.getArtist(artistId);
				$scope.artistAlbumTrackCount = _($scope.artist.albums).map('tracks').flatten().size();
				$scope.artistTrackCount = libraryService.findTracksByArtist(artistId).length;

				var art = $('#app-sidebar .albumart');
				art.css('background-image', '');

				// Because of the asynchronous nature of teh REST queries, it is possible that the
				// current artist has already changed again by the time we get the result. If that has
				// happened, then the result should be ignored.
				Restangular.one('artist', artistId).one('cover').get().then(
					function(_result) {
						if ($scope.artist && $scope.artist.id == artistId) {
							$scope.artAvailable = true;
							$scope.loading = false;

							var url = OC.generateUrl('apps/music/api/artist/') + artistId + '/cover?originalSize=true';
							art.css('background-image', 'url("' + url + '")');
						}
					},
					function(_error) {
						// error handling
						if ($scope.artist && $scope.artist.id == artistId) {
							$scope.artAvailable = false;
							$scope.loading = false;
							$scope.noImageHint = gettextCatalog.getString(
								'Upload image named "{{name}}" to anywhere within your library path to see it here.',
								{ name: $scope.artist.name.replaceAll(/[<>:"/\\|?*]/g, '_') + '.*' }
							);
						}
					}
				);

				Restangular.one('artist', artistId).one('details').get().then(
					function(result) {
						if ($scope.artist && $scope.artist.id == artistId) {
							$scope.lastfmInfo = result;

							if ('artist' in result) {
								$scope.artistBio = result.artist.bio.content || result.artist.bio.summary;
								// modify all links in the biography so that they will open to a new tab
								$scope.artistBio = $scope.artistBio.replace(/<a href=/g, '<a target="_blank" href=');
								$scope.artistTags = $scope.formatLastfmTags(result.artist.tags.tag);

								setSimilarArtists(result.artist.similar.artist);
							}

							$scope.$parent.adjustFixedPositions();
						}
					}
				);
			}
		}

		$scope.onShowAllSimilar = function() {
			$scope.allSimilarShown = true;
			$scope.allSimilarLoading = true;
			Restangular.one('artist', $scope.artist.id).one('similar').get().then(
				function(result) {
					setSimilarArtists(result);
					$scope.allSimilarLoading = false;
					$scope.$parent.adjustFixedPositions();
				}
			);
		};

		function setSimilarArtists(artists) {
			// siliar artists are divided to those within the library and the rest
			var artistIsInLib = function(artist) {
				return 'id' in artist && artist.id !== null;
			};
			$scope.similarArtistsInLib = _.filter(artists, artistIsInLib);
			$scope.similarArtistsNotInLib = $scope.formatLinkList( 
				_.reject(artists, artistIsInLib)
			);
		}

		$scope.onClickKnownArtist = function(id) {
			$rootScope.$emit('showArtistDetails', id);
		};

		$scope.$watch('contentId', function(newId) {
			if (newId !== null) {
				showDetails(newId);
			} else {
				resetContents();
			}
		});
	}
]);
