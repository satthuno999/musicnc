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
$countrys = json_decode($dataCountrys, true);
?>
<div id="content-view">
    <div class="list-stream">
        <div class="category">Lượt xem</div>
        <ul>
            <?php
            foreach ($podcasts as $podcast) {
                echo '
                <li class="item">
                <a title="' . $podcast["name"] . '"
                    href="#" data-href="' . $podcast["url"] . '">
                    <div class=" card">
                        <div class="cover">
                            <div class="lazyload-wrapper ">
                                <img  class="itemimg" alt="' . $podcast["name"] . '" loading="lazy"
                                    width="150" height="150" decoding="async" 
                                    src="' . $podcast["favicon"] . '"
                                    style="color: transparent; border-radius: 4px; width: 100%; height: 100%;">
                            </div>
                        </div>
                        <div class="title">
                            ' . $podcast["name"] . '
                        </div>
                        <div class="locate">' . $podcast["country"] . ', ' . $podcast["state"] . '
                        </div>
                    </div>
                </a>
            </li>
      ';
            }
            ?>

        </ul>
    </div>
    <div class="list-station">
        <div class="category">Languages</div>
        <input type="text" id="filterInput" placeholder="Filter items">
        <ul id="itemList">
            <?php
            foreach ($countrys as $country) {
                echo '
                <li class="item-language">
                <a title="' . $country["name"]. '"
                    href="#" data-href="' . $country["name"] . '">
                    <div class=" card">
                        <div class="cover">
                            <div class="lazyload-wrapper ">
                                <img  class="itemimg" alt="' . $country["name"] . '" loading="lazy"
                                    width="160" height="120" decoding="async" 
                                    src="https://flagcdn.com/160x120/' . $country["iso_639"] .'.png"
                                    style="color: transparent; border-radius: 4px">
                            </div>
                        </div>
                        <div class="title">
                            Số lượng trạm: ' . $country["stationcount"] . '
                        </div>
                        <div class="locate">'. $country["name"] . '
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