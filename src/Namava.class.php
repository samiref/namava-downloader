<?php
include_once "FilmDownloader.class.php";
include_once "VideoInfo.class.php";

class Namava extends FilmDownloader
{

    public function login()
    {
        if($this->isLogin() == true)
        {
            return true;
        }

        $post_data = array(
            'UserName' => $this->getUserName(),
            'Password' => $this->getPassword(),
        );

        $contents = $this->getContents('https://www.namava.ir/api/v1.0/accounts/by-phone/login', $post_data, 'json');
        $results = json_decode($contents);
        if ($results->succeeded == false) {
            throw new Exception("Error: " . $results->error->message);
        } else {
            if($this->isLogin() == true)
            {
                return true;
            }
        }

        return false;
    }

    public function isLogin()
    {
        $contents = $this->getContents('https://www.namava.ir/api/v1.0/users/info');
        $results = json_decode($contents);
        $fullName = trim(@$results->result->firstName . ' ' . @$results->result->lastName);

        if ($fullName != '')
        {
            $this->setFullName($fullName);
            return true;
        }

        return false;
    }


    public function getVideoInfo($videoId)
    {
        $page_url = $this->getBaseUrl() . 'api2/movie/' . $videoId;
        $contents = $this->getContents($page_url);
        $results = json_decode($contents);
        if (isset($results->Name) == false)
        {
            throw new Exception("Sorry! Not found any video with this ID.");
        }
        $title = $results->Name;
        $cover = $results->ImageAbsoluteUrl;

        $subtitle = null;
        $key = array_search('Farsi.srt', array_column($results->MediaInfoModel->Tracks, 'Label'));
        if ($key !== false && isset($results->MediaInfoModel->Tracks[$key]->FileFullName)) {
            $subtitle = $results->MediaInfoModel->Tracks[$key]->FileFullName;
        }

        $m3u8_url = $results->MediaInfoModel->Domain . $results->MediaInfoModel->File;

        $contents = $this->getContents($m3u8_url);
        preg_match_all('/#.*BANDWIDTH=(.*?),RESOLUTION=(.*?),.*\n(.*?)\n/', $contents, $matches);

        $qualities = array();
        foreach ($matches[1] as $key => $value) {
            $qualities[] = array(
                'bandwidth' => $matches[1][$key],
                'resolution' => $matches[2][$key],
                'url' => $matches[3][$key],
            );
        }

        $qualities = $this->multiSort($qualities, 'bandwidth', SORT_ASC);
        $qualities = array_combine(range(1, count($qualities)), array_values($qualities));

        $videoData = new VideoInfo();
        $videoData->setId($videoId)
            ->setTitle($title)
            ->setCover($cover)
            ->setSubtitle($subtitle)
            ->setQualities($qualities);


        return $videoData;
    }
}