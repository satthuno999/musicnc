<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Vũ Xuân Bình <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
?>
<link rel="icon" href="/public/favicon.ico" />
        <link
            rel="shortcut icon"
            href="/public/favicon.ico"
            type="image/x-icon"
        />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="author" content="https://github.com/satthuno999" />
        <meta
            name="description"
            content="Enjoy relaxing tunes after stressful working hours."
        />
        <meta
            name="keywords"
            content="HTML, CSS, JavaScript, Vuejs, Vite, WindiCSS, Typescript, ESlint, router, Cypress, music"
        />
        <title>Bee music</title>
        <!-- lazy for webpack -->
        <link rel="prefetch" href="path-to-chunk-with-modal-window" />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500&display=swap"
            rel="stylesheet"
        />
        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
        ></script>
        <script>
            if (
                localStorage.getItem("color-theme") === "dark" ||
                (!("color-theme" in localStorage) &&
                    window.matchMedia("(prefers-color-scheme: dark)").matches)
            ) {
                document.documentElement.classList.add("dark")
            } else {
                document.documentElement.classList.remove("dark")
            }
        </script>
        <style>
            body{
                position: absolute;
                height: unset;
                background-color: var(--color-main-background);
            }
        </style>
<div id="content"></div>
