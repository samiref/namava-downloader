<?php
include_once "src/Namava.class.php";
include_once "src/FilmDownloader.class.php";
include_once "src/VideoInfo.class.php";
include_once "src/functions.php";

if (php_sapi_name() !== "cli") exit("403");

echo "===========================================================\n";
echo "= Namava Downloader - version 1.0.0 - Copyright 2020\n";
echo "= By Seyyed AMir <www.dblog.ir> <seyyed.a@gmail.com>\n";
echo "= SignUp here: https://www.namava.ir/\n";
echo "= Project link: https://github.com/seyyed-amir/namava-downloader\n";
echo "===========================================================\n";

////////////////////////////////////////////////
///
///      Init
if($argc == 0 || isset($argv[1]) == false)
{
    $print  = "Please input ini file...\n";
    $print .=  "Example: " . $argv[0] . " init1[.ini]\n";
    echo $print;
    die();
}

$initFile = $argv[1];

$init = getIni($initFile);

define('FILE_NAME'          , $init['videos_id_file_path']);
define('SESSION_FILE'       , 'session');
define('DOWNLOADED_FILE'    , 'Downloaded_List.txt');
define('NAMAVA_USERNAME'    , $init['username']);
define('NAMAVA_PASSWORD'    , $init['password']);
//////////////////////////////////////////////////





$namava = new Namava();
$namava->setUserName(NAMAVA_USERNAME)
    ->setPassword(NAMAVA_PASSWORD)
    ->setBaseUrl("https://www.namava.ir/");

// set Proxy
if(isset($init['proxy']))
{
    $namava->setProxy($init['proxy']);
}

// try login
try
{
    $loginRes = $namava->login();
}
catch (Exception $e)
{
    die($e->getMessage());
}

if($loginRes)
{
    echo "Login to Namava.ir OK!\r\n";

    while(true)
    {
        // get last session
        $session = '';
        if(file_exists(SESSION_FILE))
            $session = file_get_contents(SESSION_FILE);

        if(empty($session))
        {
            // Read videos id file
            $files_url = read_array();

            // if file is empty
            if (count($files_url) == 0)
            {
                mySleep(60);
            } // if line is empty
            else if (empty($files_url[0]))
            {
                unset($files_url[0]);
                write_array($files_url);
                echo "Line Is Empty...\n";
            } // if url is ok
            else
            {
                $videoId = $files_url[0];

                echo date('Y-m-d H:i:s') . " Start Download Video : {$videoId}\r\n";

                $videoInfo = $namava->getVideoInfo($videoId);
                $videoInfo->setSelectedQuality($init['default_resolution']);
                $ffmpedCommand = $namava->InitFFmpegCommand($videoInfo);

                $namava->downloadCover($videoInfo);
                $namava->downloadSubtitle($videoInfo);
                //$namava->writeInfo($videoInfo);

                $namava->execConsoleCommand($ffmpedCommand, $videoInfo);

                echo date('Y-m-d H:i:s') . " ADD -> " . $videoId . "  Title: {$videoInfo->getTitle()}" . "\n";
                array_shift($files_url);

                file_put_contents(SESSION_FILE, serialize($videoInfo));

                write_array($files_url);

                // Archive VideoId
                $archiveText = "{$videoInfo->getId()} -> {$videoInfo->getTitle()} -> {$videoInfo->getFileName()}";
                $archiveText .= "-> Start at " . date('Y-m-d H:i:s') . "\r\n";
                file_put_contents(DOWNLOADED_FILE, $archiveText);

                mySleep(10);
            }
        }
        else
        {
            $videoInfo = unserialize($session);
        }



        // check status and show progress bar
        do
        {
            $stat = $namava->getState($videoInfo);
            show_progressbar($stat->getProgress(), $videoInfo->getFileName(), $stat->getSpeed());
            mySleep(10);
        } while ($stat->getProgress() <= 99);


        if($stat->getProgress() >= 99)
        {
            file_put_contents(SESSION_FILE, "");

            echo "\r\n";
            echo date('Y-m-d H:i:s') . " END -> " . $videoId . "  Title: {$videoInfo->getTitle()}" . "\n";
            echo "\r\n";
            echo "\r\n";
        }

    }
}

