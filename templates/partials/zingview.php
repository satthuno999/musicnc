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
        <div class="category">Zing mp3</div>
        <ul>
            <?php
            foreach ($podcasts as $podcast["song"]) {
                echo '
                <li class="item">
                <a title="' . $podcast["name"] . '"
                    href="#" data-href="' . $podcast["url"] . '">
                    <div class=" card">
                        <div class="cover">
                            <div class="lazyload-wrapper ">
                                <img alt="' . $podcast["thumb"] . '" loading="lazy"
                                    onerror="this.onerror=null;this.src=' . "https://cloudkma.online/apps/musicnc/img/app.svg" . '"
                                    width="150" height="150" decoding="async" 
                                    src="https://photo-resize-zmp3.zmdcdn.me/w500_r1x1_webp' . $podcast["thumb"] . '"
                                    style="color: transparent; border-radius: 4px; width: 100%; height: 100%;">
                            </div>
                        </div>
                        <div class="title">
                            ' . $podcast["artist"] . '
                        </div>
                        <div class="locate">' . $podcast["name"] . '
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