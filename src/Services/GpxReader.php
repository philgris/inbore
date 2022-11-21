<?php
/**
 * @author Philippe Bertin <contact@philippebertin.com>
 */

namespace App\Services;


use App\Entity\Gps;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;

/**
 * Import gpx file or zip archive and convert to gps points
 * Require FileUploader service
 *
 * Class GpxReader
 * @package App\Services
 */
class GpxReader
{
    const TYPE_RECORD_RTE = 'rte';
    const TYPE_RECORD_TRK = 'trk';
    const TYPE_RECORD_WPT = 'wpt';
    const GPX_MIMES = ['application/gpx','application/xml','application/gpx+xml','text/xml'];
    const NS_TOPOGRAFIX = 'http://www.topografix.com/GPX/1/1';

    private $security;
    private $fileUploader;
    private $gps;

    public function __construct(Security $security, FileUploader $fileUploader)
    {
        $this->security = $security;
        $this->fileUploader = $fileUploader;
        $this->gps = new Gps();
    }

    /**
     * @param Gps $gps
     * @param UploadedFile $file
     * @return array
     *
     * $gps object is used to set the idSite
     * Switch between zip or gpx
     */
    public function import(Gps $gps, UploadedFile $file)
    {
        $this->gps = $gps;
        if (
            strtolower($file->getClientOriginalExtension())=='zip'
        ) {
            return $this->importZip();
        } elseif (strtolower($file->getClientOriginalExtension())=='gpx') {
            return $this->importGpx();
        } else {
            return [];
        }
    }

    /**
     * @return array
     *
     * Unzip archive and batch extraction
     */
    private function importZip()
    {
        $imports = [];
        $zipPath = $this->fileUploader->getFile($this->gps);
        $dir = dirname($zipPath);
        $zipArchive = new \ZipArchive();
        if ($zipArchive->open($zipPath) === TRUE) {
            $zipArchive->extractTo($dir);
            $zipArchive->close();
        }
        @unlink($zipPath);
        foreach (scandir($dir) as $file) {
            $path = $dir.DIRECTORY_SEPARATOR.$file;
            if(is_file($path)) {
                if(in_array(mime_content_type($path), self::GPX_MIMES)) {
                    $imports[] = [
                        'file'  => pathinfo($path)['filename'],
                        'data'  => $this->read($path)
                    ];
                }
                @unlink($path);
            }
        }
        return $imports;
    }

    /**
     * @return array
     *
     */
    private function importGpx()
    {
        $path = $this->fileUploader->getFile($this->gps);
        return [
            [
                'file'  => pathinfo($path)['filename'],
                'data'  => $this->read($path)
            ]
        ];
    }

    /**
     * Switch between way point and track points
     *
     * @param $path
     * @return array
     */
    private function read($path)
    {
        $doc = new \DOMDocument;
        $doc->load($path);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('xmlns', self::NS_TOPOGRAFIX);

        // wpt : single points
        $wptNodes = $xpath->query('//xmlns:wpt');
        if ($wptNodes->count()) {
            return $this->readWpts($doc, $xpath, $wptNodes);
        }

        // trkpt : tracking points
        $trkptNodes = $xpath->query('//xmlns:trkpt');
        if ($trkptNodes->count()) {
            return $this->readTrkpts($doc, $xpath, $trkptNodes);
        }
    }

    /**
     * @param \DOMDocument $doc
     * @param \DOMXPath $xpath
     * @param $wptNodes
     * @return array
     *
     * Read way points
     */
    private function readWpts(\DOMDocument $doc, \DOMXPath $xpath, $wptNodes)
    {
        $wpts = [];
        for ($i=0; $i<$wptNodes->count(); $i++) {
            $gps = new Gps();
            if (!$wptNodes->item($i)->hasAttribute('lat') || !$wptNodes->item($i)->hasAttribute('lon')) {
                continue;
            }
            // id <- name
            $nameNodes = $xpath->query('xmlns:name', $wptNodes->item($i));
            if ($nameNodes->count()) {
                $gps->setId($nameNodes->item(0)->nodeValue);
            } else {
                continue;
            }
            // id site
            $gps->setIdSite($this->gps->getIdSite());
            // id team day
            $gps->setIdTeamDay($this->gps->getIdTeamDay());
            // type record
            $gps->setTypeRecord(self::TYPE_RECORD_WPT);
            // latitude
            $gps->setLatitude($wptNodes->item($i)->getAttribute('lat'));
            // longitude
            $gps->setLongitude($wptNodes->item($i)->getAttribute('lon'));
            // altitude
            $eleNodes = $xpath->query('xmlns:ele', $wptNodes->item($i));
            if ($eleNodes->count()) {
                $gps->setAltitude($eleNodes->item(0)->nodeValue);
            }
            // timestamp <- time
            $timeNodes = $xpath->query('xmlns:time', $wptNodes->item($i));
            if ($timeNodes->count()) {
                $gps->setTimestamp(new \DateTime($timeNodes->item(0)->nodeValue));
            }
            // date_cre
            $gps->setDateCre(new \DateTime());
            // user_cre
            $gps->setUserCre($this->security->getUser()->getId());
            // date_maj
            $gps->setDateMaj(new \DateTime());
            // user_maj
            $gps->setUserMaj($this->security->getUser()->getId());
            $wpts[] = $gps;
        }
        return $wpts;
    }

    /**
     * @param \DOMDocument $doc
     * @param \DOMXPath $xpath
     * @param $trkptNodes
     * @return array
     *
     * Read track points
     */
    private function readTrkpts(\DOMDocument $doc, \DOMXPath $xpath, $trkptNodes)
    {
        $trkpts = [];
        if ($this->gps->getIdTrack()) {
            $idTrack = $this->gps->getIdTrack()->getId();
        } else {
            // id <- name
            $nameNodes = $xpath->query('//xmlns:name');
            if ($nameNodes->count()) {
                $idTrack = $nameNodes->item(0)->nodeValue;
            } else {
                $date = new \DateTime('now');
                $datetime = $date->format('YmdHis');
                $idTrack = '_TRACK_'.$datetime;
            }
        }


        for ($i=0; $i<$trkptNodes->count(); $i++) {
            $gps = new Gps();
            if (!$trkptNodes->item($i)->hasAttribute('lat') || !$trkptNodes->item($i)->hasAttribute('lon')) {
                continue;
            }
            // id <- <idTrack>-<$i+1>
            $gps->setId($idTrack.'-'.($i+1));
//            $gps->setId($idTrack.'-'.str_pad(($i+1), strlen($trkptNodes->count()), 0, STR_PAD_LEFT));
            // id site
            $gps->setIdSite($this->gps->getIdSite());
            // id team day
            $gps->setIdTeamDay($this->gps->getIdTeamDay());
            // id track
            $gps->setIdTrack($this->gps->getIdTrack());
            // type record
            $gps->setTypeRecord(self::TYPE_RECORD_TRK);
            // latitude
            $gps->setLatitude($trkptNodes->item($i)->getAttribute('lat'));
            // longitude
            $gps->setLongitude($trkptNodes->item($i)->getAttribute('lon'));
            // altitude
            $eleNodes = $xpath->query('xmlns:ele', $trkptNodes->item($i));
            if ($eleNodes->count()) {
                $gps->setAltitude($eleNodes->item(0)->nodeValue);
            }
            // timestamp <- time
            $timeNodes = $xpath->query('xmlns:time', $trkptNodes->item($i));
            if ($timeNodes->count()) {
                $gps->setTimestamp(new \DateTime($timeNodes->item(0)->nodeValue));
            }
            // date_cre
            $gps->setDateCre(new \DateTime());
            // user_cre
            $gps->setUserCre($this->security->getUser()->getId());
            // date_maj
            $gps->setDateMaj(new \DateTime());
            // user_maj
            $gps->setUserMaj($this->security->getUser()->getId());
            $trkpts[] = $gps;
        }
        return $trkpts;
    }
}
