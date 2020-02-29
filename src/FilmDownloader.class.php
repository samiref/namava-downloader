<?php
include_once "VideoInfo.class.php";
include_once "State.class.php";

abstract class FilmDownloader
{
    private $userName;
    private $password;
    private $fullName;
    private $cookieFilePath;
    private $filmsPath;
    private $proxy;
    private $baseUrl;
    private $userAgent;
    private $defaultCookie;

    private $lastRequestHeaders;
    private $lastResponseHeaders;
    private $lastResponseBody;

    public function __construct()
    {
        $this->setCookieFilePath("Cookies");
        $this->setFilmsPath("Films");
        $this->userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36';
    }

    public abstract function login();
    public abstract function isLogin();

    /***
     * @param $videoId
     * @return VideoInfo
     */
    public abstract function getVideoInfo($videoId);

    /**
     * @param mixed $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param mixed $userName
     * @return self
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @param mixed $cookieFilePath
     * @return self
     */
    public function setCookieFilePath($cookieFilePath)
    {
        $this->cookieFilePath = $cookieFilePath;
        if(file_exists($this->getCookieFilePath()) == false)
            mkdir($this->getCookieFilePath(), 0777, true);

        return $this;
    }

    /**
     * @param mixed $filmsPath
     * @return self
     */
    public function setFilmsPath($filmsPath)
    {
        $this->filmsPath = $filmsPath;

        if(file_exists($this->getFilmsPath()) == false)
            mkdir($this->getFilmsPath(), 0777, true);
        return $this;
    }

    /**
     * @param mixed $baseUrl
     * @return self
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }


    /**
     * @param mixed $defaultCookie
     * @return self
     */
    public function setDefaultCookie($defaultCookie)
    {
        $this->defaultCookie = $defaultCookie;
        return $this;
    }

    /**
     * @param mixed $fullName
     * @return self
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @param mixed $proxy
     * @return self
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    /**
     * @param string $userAgent
     * @return self
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }



    /**
     * @return mixed
     *
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return mixed
     */
    public function getCookieFilePath()
    {
        return $this->cookieFilePath;
    }


    /**
     * @return mixed
     */
    public function getFilmsPath()
    {
        return $this->filmsPath;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return mixed
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @return mixed
     */
    public function getDefaultCookie()
    {
        return $this->defaultCookie;
    }


    /**
     * @return mixed
     */
    public function getLastRequestHeaders()
    {
        return $this->lastRequestHeaders;
    }

    /**
     * @return mixed
     */
    public function getLastResponseHeaders()
    {
        return $this->lastResponseHeaders;
    }

    /**
     * @return mixed
     */
    public function getLastResponseBody()
    {
        return $this->lastResponseBody;
    }


    /***
     * @param $url
     * @param null $data
     * @param string $dataType is form or json
     * @return false|string
     */
    public function getContents($url, $data = null, $dataType = 'form')
    {
        $this->lastRequestHeaders = '';
        $this->lastResponseHeaders = '';
        $this->lastResponseBody = '';


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        curl_setopt($ch, CURLOPT_HEADER, 1);

        $cookieFile = $this->cookieFilePath . DIRECTORY_SEPARATOR . $this->getCookieFileName();
        curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        if ($this->getProxy()) {
            list($proxyIp, $proxyPort) = explode(':', $this->getProxy());
            curl_setopt($ch, CURLOPT_PROXY, $proxyIp);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
        }

        $headers = array(
            'Authority: ' . $this->getBaseUrl(),
            'Accept: application/json, text/plain, */*',
            'X-Application-Type: WebClient',
            'User-Agent: ' . $this->getUserAgent(),
            'Origin: ' . $this->getBaseUrl(),
            'Sec-Fetch-Site: same-origin',
            'Sec-Fetch-Mode: cors',
            'Referer: ' . $this->getBaseUrl(),
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: en-US,en;q=0.9,fa;q=0.8',
        );

        if($dataType == 'json')
            $headers[] = 'Content-Type: application/json;charset=UTF-8';
        else
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($data !== null) {

            curl_setopt($ch, CURLOPT_POST, true);

            if($dataType == 'json')
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            else
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($ch);

        // Get Response Header for debug...
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->lastResponseHeaders = $header = substr($response, 0, $header_size);
        $this->lastResponseBody = $body = substr($response, $header_size);
        return $body;
    }

    function multiSort($mdarray, $mdkey, $sort = SORT_ASC)
    {
        foreach ($mdarray as $key => $row) {
            // replace 0 with the field's index/key
            $dates[$key] = $row[$mdkey];
        }
        array_multisort($dates, $sort, $mdarray);
        return $mdarray;
    }
///***
//     * @param $url
//     * @param null $data
//     * @param string $dataType is form or json
//     * @return false|string
//     */
//    public function getContents($url, $data = null, $dataType = 'form')
//    {
//        $this->lastRequestHeaders = '';
//        $this->lastResponseHeaders = '';
//        $this->lastResponseBody = '';
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        // for deflate response body
//        //curl_setopt($ch,CURLOPT_ENCODING , 'gzip, deflate');
//
//        curl_setopt($ch, CURLOPT_HEADER, 1);
//
//        // add this cookie for switch to last version of namava
//        if($this->defaultCookie)
//            curl_setopt($ch, CURLOPT_COOKIE, $this->defaultCookie);
//
//        $cookieFile = $this->cookieFilePath . DIRECTORY_SEPARATOR . $this->getCookieFileName();
//        curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookieFile);
//        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
//
//        if ($this->proxy) {
//            list($proxyIp, $proxyPort) = explode(':', $this->proxy);
//            curl_setopt($ch, CURLOPT_PROXY, $proxyIp);
//            curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
//        }
//
//
//        $headers = array(
//            'Authority: ' . $this->getBaseUrl(),
//            'X-Application-Type: WebClient',
//            'Accept-Encoding: gzip, deflate',
//            'Accept-Language: en-US,en;q=0.9',
//            'Upgrade-Insecure-Requests: 1',
//            'User-Agent: ' . $this->userAgent,
//            'Accept: text/html,application/xhtml+xml,application/xml,application/json, text/plain;q=0.9,image/webp,image/apng,*/*;q=0.8',
//            'Cache-Control: max-age=0',
//            'Proxy-Connection: keep-alive',
//            'Sec-Fetch-Site: same-origin',
//            'Sec-Fetch-Mode: cors',
//            'Accept-Encoding: gzip, deflate, br',
//        );
//
//        if($data)
//        {
//            if($dataType == 'json')
//                $headers[] = 'application/json;charset=UTF-8';
//            else
//                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
//        }
//        if($this->baseUrl)
//        {
//            $headers[] = 'Origin: ' . $this->baseUrl;
//            $headers[] = 'Referer: ' . $this->baseUrl;
//        }
//
//
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//
//
//        if ($data !== null) {
//            curl_setopt($ch, CURLOPT_POST, true);
//
//            if($dataType == 'json')
//                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//            else
//                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
//
//        }
//
//        $response = curl_exec($ch);
//
//        // Get Response Header for debug...
//        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
//        $this->lastResponseHeaders = $header = substr($response, 0, $header_size);
//        $this->lastResponseBody = $body = substr($response, $header_size);
//        return $body;
//    }

    public function InitFFmpegCommand(VideoInfo $videoInfo)
    {
        $resolutionID = $videoInfo->getSelectedQuality();
        if($resolutionID == false)
            throw new Exception("Not Selected resulotion id in VideoInfo.");

        $qualities = $videoInfo->getQualities();

        $video_file = $this->getFilmsPath() . DIRECTORY_SEPARATOR . $videoInfo->getFileName() . '.mp4';

        $cmd_proxy = '';
        if ($this->getProxy())
        {
            $cmd_proxy = '-http_proxy http://' . $this->getProxy();
        }

        $ffmpegUserAgentCommand = '';
        if($this->getUserAgent())
        {
            $ffmpegUserAgentCommand = ' -user_agent "' . $this->getUserAgent() . '" ';
        }

        $video_m3u = $qualities[$resolutionID]['url'];

        $command = 'ffmpeg ' . $ffmpegUserAgentCommand . ' ' . $cmd_proxy . ' -i "' . $video_m3u . '" -c copy -y "' . $video_file . '"';

        return $command;
    }

    public function downloadCover(VideoInfo $videoInfo)
    {
        $cover_file = $this->getFilmsPath() . DIRECTORY_SEPARATOR . $videoInfo->getFileName() . '.jpg';
        if ($videoInfo->getCover())
        {
            file_put_contents($cover_file, $this->getContents($videoInfo->getCover()));
        }
    }

    public function downloadSubtitle(VideoInfo $videoInfo)
    {
        $subtitle_file = $this->getFilmsPath() . DIRECTORY_SEPARATOR . $videoInfo->getFileName() . '.srt';
        if ($videoInfo->getSubtitle())
        {
            file_put_contents($subtitle_file, $this->getContents($videoInfo->getSubtitle()));
        }
    }

    public function writeInfo(VideoInfo $videoInfo)
    {
        $info_file = $this->getFilmsPath() . DIRECTORY_SEPARATOR . $videoInfo->getFileName() . '.info';
        file_put_contents($info_file, serialize($videoInfo));
    }

    public function execConsoleCommand($ffmpegCommand, VideoInfo $videoInfo)
    {
        $log_file = $this->getFilmsPath() . DIRECTORY_SEPARATOR . $videoInfo->getFileName() . '.log';

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        {
            $command = 'start /B ' . $ffmpegCommand . '<nul >nul 2>"' . $log_file . '"';

            pclose(popen($command, 'r'));
        }
        else
        {
            $command = $ffmpegCommand . ' </dev/null >/dev/null 2>"' . $log_file . '" &';
            shell_exec($command);
        }
    }

    public function getState(VideoInfo $videoInfo)
    {
        $filename = $videoInfo->getFileName();
        $dirname = $this->getFilmsPath();

        $filename_log = $dirname . DIRECTORY_SEPARATOR . $filename . '.log';
        $filename_video = $dirname . DIRECTORY_SEPARATOR . $filename . '.mp4';

        $modified_date = date('Y-m-d H:i:s', filemtime($filename_log));
        if (is_file($filename_video)) {
            $filesize = filesize($filename_video);
        } else {
            $filesize = 0;
        }

        $content = @file_get_contents($filename_log);

        //get duration of source
        preg_match("/Duration: (.*?), start:/", $content, $matches);

        $rawDuration = isset($matches[1]) ? $matches[1] : "00:00:00.00";

        //rawDuration is in 00:00:00.00 format. This converts it to seconds.
        $ar = array_reverse(explode(":", $rawDuration));
        $duration = floatval($ar[0]);
        if (!empty($ar[1])) $duration += intval($ar[1]) * 60;
        if (!empty($ar[2])) $duration += intval($ar[2]) * 60 * 60;

        //get the time in the file that is already encoded
        preg_match_all("/time=(.*?) bitrate/", $content, $matches);

        $rawTime = array_pop($matches);

        //this is needed if there is more than one match
        if (is_array($rawTime)) {
            $rawTime = array_pop($rawTime);
        }

        //rawTime is in 00:00:00.00 format. This converts it to seconds.
        $ar = array_reverse(explode(":", $rawTime));
        $time = floatval($ar[0]);
        if (!empty($ar[1])) $time += intval($ar[1]) * 60;
        if (!empty($ar[2])) $time += intval($ar[2]) * 60 * 60;

        //calculate the progress
        if($duration == 0)
            $progress = 0;
        else
            $progress = round(($time / $duration) * 100, 2);


        preg_match_all("/ speed=(.*?)x/", $content, $matches);
        $last_speed = array_pop($matches);
        $last_speed = array_pop($last_speed);

        $state = new State();
        $state->setDuration($rawDuration);
        $state->setTime($rawTime);
        $state->setProgress($progress);
        $state->setSpeed($last_speed);
        $state->setDownloadedSize($filesize);
        $state->setLastModified($modified_date);

        return $state;

    }

    private function getCookieFileName()
    {
        return get_class($this) . '_' . $this->userName . '_' . 'cookie.txt';
    }

}