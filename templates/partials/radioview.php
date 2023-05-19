<?php
/**
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2022-2023 S P A R K
 */
$podcasts = json_decode($data, true);
?>
<div id="content-view">
    <div class="list-stream">
        <ul>
            <?php
            foreach ($podcasts as $podcast) {
                echo '
                <li class="item">
                <a title="Listen to the station WFAN 66 AM - 101.9 FM online now" id="station_top100_0"
                    href="' . $podcast->url . '">
                    <div class="sc-jd7lhi-4 cteXZw card">
                        <div class="sc-jd7lhi-1 hTzxHt cover">
                            <div class="lazyload-wrapper ">
                                <img alt="' . $podcast->name . '" loading="lazy"
                                    width="150" height="150" decoding="async" 
                                    src="' . $podcast->favicon . '"
                                    style="color: transparent; border-radius: 4px; width: 100%; height: 100%;">
                            </div>
                        </div>
                        <div class="title">
                            ' . $podcast->name . '
                        </div>
                        <div class="locate">' . $podcast->country . ' ' . $podcast->state . '
                        </div>
                    </div>
                </a>
            </li>
      ';
            }
            ?>

        </ul>
    </div>
</div>