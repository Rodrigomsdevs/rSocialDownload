<?php
error_reporting(1);

class Downloader
{
    private $videoUrl;
    private $downloaderModel;

    public function __construct($videoUrl)
    {
        $this->videoUrl = $videoUrl;

        if (strpos($videoUrl, "facebook.com") !== false) {
            include("FacebookDownloader.php");

            $facebookModel = new FacebookDownloader($videoUrl);
            $this->downloaderModel = $facebookModel;
        } elseif (strpos($videoUrl, "instagram.com") !== false) {
            include("InstagramDownloader.php");

            $instagramModel = new InstagramDownloader($videoUrl);
            $this->downloaderModel = $instagramModel;
        }
    }

    public function getResponse()
    {
        if (!isset($this->downloaderModel)) {
            return json_encode(['error' => 'No suitable downloader model found.']);
        }

        $this->downloaderModel->execute();
        return $this->downloaderModel->getResponse();
    }
}
