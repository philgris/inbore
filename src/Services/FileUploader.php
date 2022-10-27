<?php
/**
 * @author Philippe Bertin <contact@philippebertin.com>
 */

namespace App\Services;


use App\Kernel;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Single file uploader service
 *
 * Class FileUploader
 * @package App\Services
 */
class FileUploader
{
    // Path of the upload directory.
    // All files are stored under this folder.
    const ROOT_UPLOAD_DIRECTORY = 'files';

    private $kernel;
    private $request;
    private $slugger;
    private $config;

    public function __construct(
        RequestStack $requestStack,
        ParameterBagInterface $config,
        SluggerInterface $slugger,
        Kernel $kernel
    )
    {
        $this->kernel = $kernel;
        $this->request = $requestStack->getCurrentRequest();
        $this->slugger = $slugger;
        // see upload parameters in config/admin.yml
        $this->config = $config->get('admin');
    }

    /**
     * Handle the attached file to upload, replace or remove.
     * Call this single method from the controller.
     * For new object persistence, flush the object before calling.
     * The object id is used to create the storage path.
     *
     * @param FormInterface $form
     * @param $object
     * @param $entity
     * @return bool
     */
    public function handleFile(FormInterface $form, $object, $entity)
    {
        $return = false;
        // remove file when the request method is DELETE
        if (
            $this->request->getMethod()=='DELETE' ||
            (
                isset($this->config[$entity]['upload']['remove']) &&
                $form->has($this->config[$entity]['upload']['remove']) &&
                $form->get($this->config[$entity]['upload']['remove'])->getData()
            )
        )
        {
            $this->removeFile($object);
            $return = true;
        }

        // upload file attached in the submitted form
        if (
            isset($this->config[$entity]['upload']['field']) &&
            $form->has($this->config[$entity]['upload']['field']) &&
            $form->get($this->config[$entity]['upload']['field'])->getData()
        )
        {
            $this->uploadFile($form, $object, $entity);
            $return = true;
        }

        return $return;
    }

    /**
     * Check if a file upload is to be handled
     * @param $entity
     * @return bool
     */
    public function handleUpload($entity)
    {
        // upload is handled if parameter upload > field is set in config/admin.yml
        // field is the name of the entity field (ex: file, pdf...)
        return isset($this->config[$entity]['upload']['field']);
    }

    /**
     * Upload process
     * - first clear the destination directory
     * - then upload the file
     *
     * @param FormInterface $form : submitted form
     * @param $object : related object (ex: protocol)
     * @param $entity : name of the entity as set in admin.yml (ex: protocol)
     */
    public function uploadFile(FormInterface $form, $object, $entity)
    {
        //$entity = $this->getEntity($object);
        if (
            $this->handleUpload($entity) &&
            $form->has($this->config[$entity]['upload']['field']) &&
            $form->get($this->config[$entity]['upload']['field'])->getData()
        ) {
//            $this->removeFile($object);
            $this->clearDirectory($object);
            $this->upload(
                $form->get($this->config[$entity]['upload']['field'])->getData(),
                $this->getEntityDirectory($entity).DIRECTORY_SEPARATOR.$object->getId()
                //$this->getFilename($object)
            );
            // read file info and update object properties with setters given in admin.yml
            // ex: upload > setters > width : setWidth
            // will call $object->setWidth(width)
            if (isset($this->config[$entity]['upload']['setters'])) {
                $info = $this->getFileInfo($object);
                if (!empty($info)) {
                    foreach ($this->config[$entity]['upload']['setters'] as $property => $setter) {
                        if (array_key_exists($property, $info) && !empty($setter) && method_exists($object, $setter)) {
                            try {
                                $method = new \ReflectionMethod($object, $setter);
                                $method->invoke($object, $info[$property]);
                            } catch (\ReflectionException $e) {

                            }
                        }
                    }
                }
            }
        }

    }

    /**
     * Get full path filename
     * Each file is stored in a folder : file/<entity>/<objectId>/<filename>
     *
     * @param $object
     * @return null
     */
    public function getFilename($object)
    {
        $entity = $this->getEntity($object);
        $dir = $this->getRootUploadDirectory().$this->getEntityDirectory($entity).DIRECTORY_SEPARATOR.$object->getId();
        $scan = scandir($dir,1);
        return is_file($dir.DIRECTORY_SEPARATOR.$scan[0])
            ? $scan[0]
            : null
        ;
    }

    public function removeFile($object)
    {
//        $this->remove($object);
        $this->clearDirectory($object, true);
    }

    /**
     * Low level upload process
     *
     * @param UploadedFile $file
     * @param $targetDirectory
     * @param null $filename
     * @return null|string
     */
    private function upload(UploadedFile $file, $targetDirectory, $filename=null)
    {
        $filename = $filename ? $filename : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $file->guessExtension();
        $dir = $this->getRootUploadDirectory() . $targetDirectory . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (is_dir($dir)) {
            $file->move($dir, $filename);
            return $filename;
        } else {
            return null;
        }

    }

    public function fileExists($object)
    {
        return is_file($this->getFile($object));
    }

    public function getFile($object)
    {
        return $this->getRootUploadDirectory().$this->getFilePath($object);
    }

    public function remove($object, $removeParentDir=false)
    {
        if ($this->fileExists($object)) {
            unlink($this->getFile($object));
            //@TODO remove parent dir if empty
//            if ($removeParentDir) {
//                @rmdir(dirname($this->getRootUploadDirectory().$this->getEntityDirectory($entity).$object->getId()));
//            }
        }
    }

    private function clearDirectory($object, $rmDirectory=false)
    {
        $entity = $this->getEntity($object);
        $dir = $this->getRootUploadDirectory().$this->getEntityDirectory($entity).DIRECTORY_SEPARATOR.$object->getId();
        if (is_dir($dir)) {
            array_map(function($name) use ($dir) {$file = $dir.DIRECTORY_SEPARATOR.$name; if(is_file($file)){@unlink($file);}}, scandir($dir));
            if ($rmDirectory) {
                @rmdir($dir);
            }
        }
    }

    private function getEntity($object)
    {
        $a = explode('\\', get_class($object));
        $entity = strtolower(array_pop($a));
        return $entity;
    }

    private function getEntityDirectory($entity)
    {
        return isset($this->config[$entity]['upload']['dir'])
            ? $this->config[$entity]['upload']['dir']
            : $entity
        ;
    }

    private function getRootUploadDirectory()
    {
        return $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . self::ROOT_UPLOAD_DIRECTORY . DIRECTORY_SEPARATOR;
    }

    private function getFilePath($object)
    {
        $entity = $this->getEntity($object);
        $dir = $this->getRootUploadDirectory().$this->getEntityDirectory($entity).DIRECTORY_SEPARATOR.$object->getId();
        if (!is_dir($dir)) {
            return null;
        }
        $scan = scandir($dir,1);
        return is_file($dir.DIRECTORY_SEPARATOR.$scan[0])
            ? $this->getEntityDirectory($entity).DIRECTORY_SEPARATOR.$object->getId().DIRECTORY_SEPARATOR.$scan[0]
            : null
        ;
    }

    /**
     * Read low level file info
     * and return an array [ 'width' => <width>, ... 'filename' => <filename> ]
     *
     * @param $object
     * @return array|bool
     */
    public function getFileInfo($object)
    {
        if ($this->fileExists($object)) {
            $mime = $this->getMime($object);
            $file = $this->getFile($object);
            if (preg_match('/^video\//', $mime)) {
                return [
                    'width'     => null,
                    'height'    => null,
                    'size'      => filesize($file),
                    'mime'      => $mime,
                    'path'      => str_replace($this->getRootUploadDirectory(), '', dirname($file)),
                    'filename'  => basename($file)
                ];
            } elseif (preg_match('/^image\//', $mime)) {
                $a = getimagesize($file);
                if (is_array($a) && count($a)>2) {
                    $a['width']     = $a[0];
                    $a['height']    = $a[1];
                    $a['size']      = filesize($file);
                    $a['mime']      = $mime;
                    $a['path']      = str_replace($this->getRootUploadDirectory(), '', dirname($file));
                    $a['filename']  = basename($file);
                    return $a;
                }
            }
        }
        return [];
    }

    public function getMime($object)
    {
        return $this->fileExists($object)
            ? mime_content_type($this->getFile($object))
            : ''
        ;
    }
}