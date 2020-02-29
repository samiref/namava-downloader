<?php


class State
{
    private $duration;
    private $time;
    private $progress;
    private $speed;
    private $downloadedSize;
    private $lastModified;

    public function __toString()
    {
        return
            "Downloaded: {$this->downloadedSize} {$this->_filesize_formatted()}\r\n" .
            "  Progress: {$this->progress}%\r\n" .
            "  Duration: {$this->duration}\r\n" .
            "      Time: {$this->time}\r\n" .
            "     Speed: {$this->speed}\r\n" .
            "  Modified: {$this->lastModified}\r\n" .
            "";
    }

    function _filesize_formatted()
    {
        $size = $this->downloadedSize;
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param mixed $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return mixed
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * @param mixed $speed
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
    }

    /**
     * @return mixed
     */
    public function getDownloadedSize()
    {
        return $this->downloadedSize;
    }

    /**
     * @param mixed $downloadedSize
     */
    public function setDownloadedSize($downloadedSize)
    {
        $this->downloadedSize = $downloadedSize;
    }

    /**
     * @return mixed
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param mixed $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }




}