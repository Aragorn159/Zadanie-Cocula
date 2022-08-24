<?php
require ("APIHeader.php");
function getGalleries(): void
{
    if (isset($_GET)) {
        $response_content = array();
        createServerRepoIfNotExisting();
        $fd = opendir($GLOBALS["serverPath"]);
        $entry = readdir($fd);
        chdir($GLOBALS["serverPath"]);
        while ($entry !== false) {
            if ($entry == "." || $entry == ".." || !is_dir($entry)) {
                $entry = readdir($fd);
                continue;
            }
            $obj = new galleriesObjImage();
            $obj->name = $entry;
            $obj->path = rawurlencode($entry);
            $fdChildContent = scandir($entry);
            if (count($fdChildContent) >= 3) {
                $imageParts = pathinfo($fdChildContent[2]);
                $image = new imageObj();
                $image->fullPath = $entry.'\\'.$fdChildContent[2];
                $image->path = rawurlencode($imageParts['basename']);
                $image->name = rawurlencode($imageParts['filename']);
                $image->modified = date("F d Y H:i:s", filemtime(realpath($image->fullPath)));
                $obj->image = $image;
            }
            $response_content[] = array($obj);
            $entry = readdir($fd);
        }
        header("Content-type:application/json", true, 200);
        echo json_encode(array( "Gallery list schema" => $response_content), JSON_FORCE_OBJECT);
        closedir($fd);
        return;
    }
    http_response_code(500);
}

function postGalleries(): void
{
    if (isset($_POST)) {
        $name = $_POST["name"];
        if ($name!=null && strlen($name)>1) {
            if (strpos($name, '/')) {
                $payload = array(
                    "name" => "INVALID_SCHEMA",
                    "description" => "Bad JSON object, 'name' contains forbidden characters"
                );
                header("Content-type:application/json", true, 400);
                echo json_encode($payload);
                return;
            }
            $name = htmlspecialchars($name, ENT_QUOTES);
            createServerRepoIfNotExisting();
            chdir($GLOBALS["serverPath"]);
            if (file_exists(getcwd().'/'.$name)) {
                http_response_code(409);
                return;
            }
            if (!mkdir(getcwd().'/'.$name)) {
                http_response_code(500);
                return;
            }
            $scanList = scandir(getcwd());
            $scan = $scanList[array_search($name, $scanList, true)];
            $responseSuccess = array(
                "path" => rawurlencode($scan),
                "name" => $scan
            );
            header("Content-type:application/json", true, 201);
            echo json_encode($responseSuccess);
        } else {
            $payload = array(
                "name" => "INVALID_SCHEMA",
                "description" => "Bad JSON object, 'name' is a required property"
            );
            header("Content-type:application/json", true, 400);
            echo json_encode($payload);
        }
        return;
    }
    http_response_code(500);
}

function GetSingleGallery(): void
{
    if (isset($_GET)) {
        $urlSections = explode('/', strval($_SERVER["REQUEST_URI"]));
        $query = rawurldecode(end($urlSections));
        chdir($GLOBALS["serverPath"]);
        $fd = opendir(getcwd() . '\\' . $query);
        if (!$fd)
        {
            http_response_code(404);
            return;
        }
        $images = array();
        $entry = readdir($fd);
        while ($entry!=false)
        {
            $imageParts = pathinfo($entry);
            if ($imageParts["extension"]=='jpg'|| $imageParts["extension"]=='jpeg')
            {
                $imageInfo = new imageObj();
                $imageInfo->fullPath = $query.'\\'.$entry;
                $imageInfo->path = rawurlencode($imageParts['basename']);
                $imageInfo->name = rawurlencode($imageParts['filename']);
                $imageInfo->modified = date("F d Y H:i:s", filemtime(realpath($imageInfo->fullPath)));
                $images[] = $imageInfo;
            }
            $entry = readdir($fd);
        }
        $gallery= new galleriesObj();
        $gallery->name = $query;
        $gallery->path = rawurlencode($query);
        header("Content-type:application/json", true, 200);
        echo json_encode(array(
            "Gallery detail schema" => array(
                $gallery, $images
            ),
        ));
        closedir($fd);
        return;
    }
    http_response_code(500);
}

function DeleteSingleGallery() : void
{
    if ($_SERVER["REQUEST_METHOD"]=="DELETE")
    {
        $urlSections = explode('/', strval($_SERVER["REQUEST_URI"]));
        $query = rawurldecode(end($urlSections));
        chdir($GLOBALS["serverPath"]);
        $dirName = getcwd() . '\\' . $query;
        if (!file_exists($dirName))
        {
            http_response_code(404);
            return;
        }
        array_map('unlink', glob("$dirName/*"));
        rmdir($dirName);
        http_response_code(200);
    }
    http_response_code(500);
}

function PostToGallery() : void
{
    if (isset($_POST))
    {
        $urlSections = explode('/', strval($_SERVER["REQUEST_URI"]));
        $path = rawurldecode(end($urlSections));
        $boundary = '--boundary';
        $headers = getallheaders();
        if (!isset($headers["Content-Type"]) || $headers["Content-Type"] != "multipart/form-data" || !isset($headers["boundary"]) || $headers["boundary"] != $boundary) {
            http_response_code(400);
        }
        $input = file_get_contents('php://input');
        /*var_dump($input);
        $result = array();
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];
        $body = preg_split("/-+$boundary/", $input);
        array_pop($body);
        $data = array();
        foreach ($body as $id=>$block)
        {
            if (empty($block))
                continue;
            if (strpos($block, 'image/jpeg') !== FALSE)
            {
                preg_match("/name=\"([^\"]*)\".*jpeg[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            }
            else
            {
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }
            $data[$matches[1]] = $matches[2];
        }*/
        /*$rawfields = explode($boundary,$data);
        array_pop($rawfields); // drop the last -- piece
        foreach ( $rawfields as $id=>$block )
        {
            list($mime,$content) = explode("\r\n\r\n",$block,2); // I think it's <cr><lf> by standards, maybe check!
            if ( preg_match('/name="([^"]*)"/i',$mime,$match) )
            {
                $result[$match[1]] = $content;
            } else {
                $result[] = $content; // just in case...
            }
        }*/

       /* if(empty($input) || empty($boundary)) return;
        $sections = array_map("trim", explode("--$boundary", $input));
        $parts = [];
       foreach($sections as $section) {
            if($section === "" || $section === "--") continue;
            $fields = explode("\r\n\r\n", $section);
            if(preg_match_all("/([a-z0-9-_]+)\s*:\s*([^\r\n]+)/iu", $fields[0] ?? "", $matches, PREG_SET_ORDER) === 2) {
                $headers = [];
                foreach($matches as $match) $headers[$match[1]] = $match[2];
            } else $headers = null;
            $parts[] = ["headers" => $headers, "value"   => $fields[1] ?? null];
        }*/
        header("Content-Type: application/json", true, 200);
        echo json_encode(array($input));
        return;


    }
    http_response_code(500);
}

function imageGet (){
    if (isset($_GET))
    {
        $urlSections = explode('/', strval($_SERVER["REQUEST_URI"]));
        $dimensions = explode('x', $urlSections[count($urlSections)-2]);
        $path = $GLOBALS["serverPath"].'/'.$urlSections[count($urlSections)-3].'/'.rawurldecode(end($urlSections));
        if (!file_exists($path))
        {
            http_response_code(404);
            return;
        }
        list($xOld, $yOld) = getimagesize($path);
        $xNew = $dimensions[0];
        $yNew = $dimensions[1];
        $image = imagecreatefromjpeg($path);
        if ($dimensions[0]<=0)
        {
            if ($dimensions[1]>0)
            {
                $xNew = ceil($dimensions[1]*($xOld/$yOld));
            }
            else{
                $xNew = $xOld;
            }
        }
        if ($dimensions[1]<=0)
        {
            if ($dimensions[0]>0)
            {
                $yNew = ceil($dimensions[0]*($yOld/$xOld));
            }
            else
            {
                $yNew = $yOld;
            }
        }
        $preview = imagecreate($xNew, $yNew);
        if (!imagecopyresized($preview, $image, 0, 0,0,0,$xNew, $yNew, $xOld, $yOld))
        {
            http_response_code(500);
            return;
        }
        header("Content-Type:image/jpeg", true, 200);
        imagejpeg($preview);
        return;
    }
    http_response_code(500);
}