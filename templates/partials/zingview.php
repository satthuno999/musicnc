<?php
/**
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2022-2023 S P A R K
 */
$podcasts = json_decode($data,true)["tracks"]["hits"];
$artists = json_decode($data, true)["artists"]["hits"];
$errors = json_decode($error, true);
// $name = json_decode($name, true);
?>
<div id="content-view">
    <div class="list-stream">
        <div class="category">Bài hát - <?php echo $name?></div>
        <ul>
            <?php
            foreach ($podcasts as $podcast) {
                $track = $podcast["track"];

                $uri = null;
                foreach ($track['hub']['actions'] as $action) {
                    if ($action['type'] === 'uri') {
                        $uri = $action['uri'];
                        break;
                    }
                }
                
                echo '
                <li class="item">
                <a title="' . $track["title"] . '"
                    href="#" data-href="' . $uri . '">
                    <div class=" card">
                        <div class="cover">
                            <div class="lazyload-wrapper ">
                                <img alt="' . $track["share"]["subject"] . '" loading="lazy"
                                    onerror="this.onerror=null;this.src=' . "https://cloudkma.online/apps/musicnc/img/app.svg" . '"
                                    width="150" height="150" decoding="async" 
                                    src="' . $track["images"]["coverart"] . '"
                                    style="color: transparent; border-radius: 4px; width: 100%; height: 100%;">
                            </div>
                        </div>
                        <div class="title">
                            ' . $track["subtitle"] . '
                        </div>
                        <div class="locate">' . $track["title"] . '
                        </div>
                    </div>
                </a>
            </li>
      ';
            }
            ?>

        </ul>
    </div>
    <div class="list-stream">
        <div class="category">
             Nghệ sĩ
        </div>
        <ul>
            <?php
            foreach ($artists as $artist) {
                $item = $artist["artist"];
                echo '
                <li class="item">
                <a title="' . $item["name"] . '"
                    href="#" data-verified="' . $item["verified"] .'" data-href="' . $item["weburl"] . '">
                    <div class=" card">
                        <div class="cover">
                            <div class="lazyload-wrapper ">
                                <img alt="' . $item["name"] . '" loading="lazy"
                                    onerror="this.onerror=null;this.src=' . "https://cloudkma.online/apps/musicnc/img/app.svg" . '"
                                    width="150" height="150" decoding="async" 
                                    src="'.$item["avatar"].'"
                                    style="color: transparent; border-radius: 4px; width: 100%; height: 100%;">
                            </div>
                        </div>
                        <div class="title">
                            ' . $item["name"] . '
                        </div>
                        <div class="locate">
                            <a href="' . $item["weburl"] . '">WEB URL</a>
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