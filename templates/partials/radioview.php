<?php
/**
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2022-2023 S P A R K
 */
$responseData = json_decode($data, true);
$station = $responseData[0];
?>
<div id="content-view">
    <?php echo $station["name"] ?>
</div>