<?php

//function mySleep($time)
//{
//    $time_str_len = strlen("{$time}");
//    echo "\r\n";
//    while($time-- > 0)
//    {
//        echo "\r";
//        echo str_pad($time,$time_str_len, ' ', STR_PAD_BOTH);
//        sleep(1);
//    }
//}

function mySleep($time)
{
    $time_str_len = strlen("{$time}");
    while($time-- > 0)
    {
        $bs = "\x08";
        echo str_pad($time,$time_str_len, ' ', STR_PAD_BOTH);
        sleep(1);
        echo str_repeat($bs, $time_str_len);
    }
}

function show_progressbar($percentage_num, $fileId, $speed)
{
    $out = "\r";
    $out .= '[' . str_pad(str_repeat('|', intval($percentage_num / 5)) , 20, '.') . ']  ';
    $out .= str_pad(round($percentage_num,2) . " %", 10);
    $out .= "File: " . str_pad( substr($fileId,0, 15), 16);
    $out .= "Speed: " . str_pad($speed, 8) ;

    echo $out;

}


function read_array()
{
    if(file_exists(FILE_NAME))
    {
        $contents = file_get_contents(FILE_NAME);
        $contents = str_replace("\r\n", "\n", $contents);

        if(strlen($contents) == 0)
            return array();

        return explode("\n", $contents);
    }

    return array();
}

function write_array($array)
{
    $contents = implode("\n", $array);
    file_put_contents(FILE_NAME, $contents);
}

function getIni($initFile)
{
    if(count(explode($initFile, '.')) == 1)
        $initFile .= '.ini';

    if(file_exists($initFile) == false)
    {
        throw new Exception("Init file is not exists...");
    }

    $init = @parse_ini_file($initFile);

    if($init === false)
    {
        throw new Exception("Init file have error...\n");
    }


    if(isset($init['username']) == false)
    {
        throw new Exception("Username not set...\n");
    }

    if(isset($init['password']) == false)
    {
        throw new Exception("Password not set...\n");
    }

    if(isset($init['videos_id_file_path']) == false)
    {
        throw new Exception("Videos Id File Path not set...\n");
    }

    if(file_exists($init['videos_id_file_path']) == false)
    {
        throw new Exception("Videos Id File is not exists...\n");
    }

    return $init;
}