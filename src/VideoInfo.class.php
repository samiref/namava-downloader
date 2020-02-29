<?php


class VideoInfo
{
    public static $MAX_RES = 99999;
    private $id;
    private $title;
    private $cover;
    private $subtitle;
    private $qualities;
    private $selectedQuality;
    private $fileName;

    public function __construct()
    {
        $this->selectedQuality = 1;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @param mixed $cover
     * @return self
     */
    public function setCover($cover)
    {
        $this->cover = $cover;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param mixed $subtitle
     * @return self
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQualities()
    {
        return $this->qualities;
    }

    /**
     * @param mixed $qualities
     * @return self
     */
    public function setQualities($qualities)
    {
        $this->qualities = $qualities;
        return $this;
    }

    /**
     * @return int
     */
    public function getSelectedQuality()
    {
        return $this->selectedQuality;
    }

    /**
     * @param int $selectedQuality
     * @return self
     */
    public function setSelectedQuality($selectedQuality)
    {
        if($selectedQuality == self::$MAX_RES || strtolower($selectedQuality) == 'max')
        {
            $this->selectedQuality = count($this->getQualities());
        }
        else if(intval($selectedQuality) > 0)
        {
            $this->selectedQuality = $selectedQuality;
        }
        else
        {
            foreach($this->qualities as $key => $quality)
            {
                if(strtolower($quality['resolution']) == strtolower($selectedQuality))
                    $this->selectedQuality = $key;
            }
        }

        if($this->selectedQuality == false)
            $this->selectedQuality = count($this->getQualities());

        $file_name = $this->getId() . '_' . $this->qualities[$this->selectedQuality]['resolution'];
        $this->setFileName( $file_name);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     * @return self
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }





}