#!/bin/bash
#
# ownCloud Music
#
# @author Pauli Järvinen
# @copyright 2021, 2022 Pauli Järvinen <pauli.jarvinen@gmail.com>
#

# Create the base package from the files stored in git
cd ..
git archive HEAD --format=zip --prefix=music/ > musicnc.zip

# Add the generated webpack files to the previously created package
cd ..
zip -g music/musicnc.zip music/dist/*.js
zip -g music/musicnc.zip music/dist/*.css
zip -g music/musicnc.zip music/dist/*.json
zip -g music/musicnc.zip music/dist/img/**

# Remove the front-end source files from the package as those are not needed to run the app
zip -d music/musicnc.zip "music/build/*"
zip -d music/musicnc.zip "music/css/*.css"
zip -d music/musicnc.zip "music/css/*/"
zip -d music/musicnc.zip "music/img/*.svg"
zip -d music/musicnc.zip "music/img/*/*"
zip -d music/musicnc.zip "music/js/*.js*"
zip -d music/musicnc.zip "music/js/*/*"
zip -d music/musicnc.zip "music/l10n/*/*"

# Add the application icon back to the zip as that is still needed by the cloud core
zip -g music/musicnc.zip music/img/musicnc.svg

# Remove also files related to testing and code analysis
zip -d music/musicnc.zip "music/tests/*"
zip -d music/musicnc.zip "music/composer.*"
zip -d music/musicnc.zip "music/phpstan.neon"
zip -d music/musicnc.zip "music/stubs/*"

# Fork the package to own versions for Nextcloud and ownCloud.
# Different mechanism is used on each cloud to define the database schema.
cp music/musicnc.zip music/music-nc.zip
mv music/musicnc.zip music/music-oc.zip
zip -d music/music-nc.zip "music/appinfo/database.xml"
zip -d music/music-oc.zip "music/lib/Migration/Version*Date*.php"
