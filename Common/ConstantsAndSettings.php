<?php

define(ORIGINAL_PICTURES_DIR, "Pictures/OriginalPictures");
define(ALBUM_PICTURES_DIR, "Pictures/AlbumPictures");
define(ALBUM_THUMBNAILS_DIR, "Pictures/AlbumThumbnails");

define(IMAGE_MAX_WIDTH, 800);
define(IMAGE_MAX_HEIGHT, 600 );

define(THUMB_MAX_WIDTH, 100);
define(THUMB_MAX_HEIGHT, 100);

$supportedImageTypes = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
date_default_timezone_get("America/Toronto");