/**
 * ownCloud - Music app
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Morris Jobke <morris.jobke@gmail.com>
 * @author Pauli Järvinen <pauli.jarvinen@gmail.com>
 * @copyright Morris Jobke 2013
 * @copyrigth Pauli Järvinen 2017 - 2022
 */

angular.module('Musicnc').factory('Audio', [function () {
	return new OCA.musicnc.GaplessPlayer();
}]);
