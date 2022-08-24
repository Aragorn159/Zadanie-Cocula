<?php

$GLOBALS["file_repository"] = "/server";
$GLOBALS["serverPath"] = $_SERVER["DOCUMENT_ROOT"].$GLOBALS["file_repository"];

class galleriesObjImage
{
    public $name;
    public $path;
    public $image;
}

class galleriesObj
{
    public $name;
    public $path;
}


class imageObj
{
    public $path;
    public $fullPath;
    public $name;
    public $modified;
}


function createServerRepoIfNotExisting(): void
{
    global $file_repository;
    if (!file_exists($_SERVER['DOCUMENT_ROOT'].$file_repository)) {
        mkdir($_SERVER['DOCUMENT_ROOT'].$file_repository, 0777, true);

    }
}

