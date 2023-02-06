<?php
/**
 * @author Philippe Bertin <contact@philippebertin.com>
 */

namespace App\Services;


use App\Kernel;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

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

    // FileUploaderType field suffixes
    const FILE_SUFFIX       = '_file';
    const FILENAME_SUFFIX   = '_filename';
    const REMOVE_SUFFIX     = '_remove';

    // FileUploaderType BlockPrefix
    const BLOCK_PREFIX      = 'file_uploader';
    const BLOCK_FILE        = 'file';

    // File controller is <the_entity_name><suffix>
    const FILE_CONTROLLER_PATH_SUFFIX = '_file';

    const NULL_FIELD = '_null_';

    private $kernel;
    private $request;
    private $router;

    private $entityDirectory;
    private $objectDirectory;

    public function __construct(
        RequestStack $requestStack,
        Kernel $kernel,
        RouterInterface $router
    )
    {
        $this->kernel = $kernel;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
    }

    /**
     * Handle the attached files to upload, replace or remove.
     * Call this single method from the controller.
     * For new object persistence, flush the object before calling.
     * The object id is used to create the storage path.
     *
     * @param FormInterface $form
     * @param $object
     * @return bool
     */
    public function handleFiles(FormInterface $form, $object)
    {
        if ($this->request->getMethod()=='DELETE') {
            $this->removeDirectory($this->getObjectDirectory($object));
            return true;
        }

        $return = false;
        foreach($form->all() as $element) {
            if (
                $element->getConfig()->getType()->getInnerType()->getBlockPrefix()==self::BLOCK_PREFIX
            ) {
                $return = $this->handle($element, $object, $element->getConfig()->getOption('ignore_field_name')) || $return;
            }
        }
        return $return;
    }

    /**
     * @deprecated
     * Handle single file only : for backward compatibility
     *
     * @param FormInterface $form
     * @param $object
     * @return bool
     */
    public function handleFile(FormInterface $form, $object)
    {
        if ($this->request->getMethod()=='DELETE') {
            $this->removeDirectory($this->getObjectDirectory($object));
            return true;
        }

        foreach($form->all() as $element) {
            if (
                $element->getConfig()->getType()->getInnerType()->getBlockPrefix()==self::BLOCK_PREFIX ||
                $element->getConfig()->getType()->getInnerType()->getBlockPrefix()==self::BLOCK_FILE
            ) {
                return $this->handle($element, $object, true);
            }
        }
        return false;

    }

    /**
     * switch to delete and/or upload
     * @param FormInterface $element
     * @param $object
     * @param bool $ignoreFieldName : used for backward compatibility with project only handling single file
     * @return bool
     */
    private function handle(FormInterface $element, $object, $ignoreFieldName=false)
    {
        if ($element->getConfig()->getType()->getInnerType()->getBlockPrefix()==self::BLOCK_PREFIX) {

            $field = $ignoreFieldName ? null : $element->getName();
            $return = false;

            $remove = $element->getName().self::REMOVE_SUFFIX;
            // remove file when the request method is DELETE
            if (
                $element->has($remove) &&
                $element->get($remove)->getData()
            )
            {
                $this->clearDirectory($object, $field, true);
                $return = true;
            }

            $file = $element->getName().self::FILE_SUFFIX;

            // upload file attached in the submitted form
            if (
                $element->has($file) &&
                $element->get($file)->getData()
            )
            {
                $this->uploadFile($element, $object, $field);
                $return = true;
            }

            return $return;

        } elseif ($element->getConfig()->getType()->getInnerType()->getBlockPrefix()==self::BLOCK_FILE) {
            if ($element->getData()) {
                $this->upload($element->getData(), $this->getFieldDirectory($object, null));
                return true;
            }
        }
        return false;
    }

    /**
     * Upload process
     * - first clear the destination directory
     * - then upload the file
     * @param FormInterface $element
     * @param $object
     * @param $field
     */
    private function uploadFile(FormInterface $element, $object, $field)
    {
        $file = $element->get($element->getName().self::FILE_SUFFIX)->getData();
        if ($file) {
            $this->clearDirectory($object, $field);
            $this->upload($file, $this->getFieldDirectory($object, $field));
            // setters
            // ex: 'width' => 'setWidth'
            // will call $object->setWidth(width)
            $setters = $element->getConfig()->getOption('setters');
            if (is_array($setters)) {
                $info = $this->getFileInfo($object, $field);
                if (!empty($info)) {
                    foreach ($setters as $property => $setter) {
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
     * @param $field
     * @return null
     */
    public function getFilename($object, $field=null)
    {
        $dir = $this->getFieldDirectory($object, $field);
        $scan = scandir($dir,1);
        for ($i=0; $i<count($scan); $i++) {
            if (is_file($dir.DIRECTORY_SEPARATOR.$scan[$i])) {
                return $scan[$i];
            }
        }
        return null;
    }

    public function removeFile($object)
    {
        $this->clearDirectory($object, null, true);
    }

    /**
     * Low level upload process
     *
     * @param UploadedFile $file
     * @param $object
     * @param null $filename
     * @return null|string
     */
    private function upload(UploadedFile $file, $dir)
    {
        if ($dir) {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $file->guessExtension();
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            if (is_dir($dir)) {
                $file->move($dir, $filename);
                return $filename;
            }
        }

        return null;

    }

    /**
     * @param $object
     * @param $field
     * @return bool
     */
    public function fileExists($object, $field=null)
    {
        return is_file($this->getFile($object, $field));
    }

    /**
     * @param $object
     * @param $field
     * @return null|string
     */
    public function getFile($object, $field=null)
    {
        if ($field==self::NULL_FIELD) {
            $field = null;
        }
        return $this->getFilePath($object, $field);
    }

    private function clearDirectory($object, $field, $rmDirectory=false)
    {
        $dir = $field
            ? $this->getObjectDirectory($object).DIRECTORY_SEPARATOR.$field
            : $this->getObjectDirectory($object)
        ;
        if (is_dir($dir)) {
            array_map(
                function($name) use ($dir) {
                    $file = $dir.DIRECTORY_SEPARATOR.$name;
                    if (is_file($file)) {
                        @unlink($file);
                    }
                },
                scandir($dir)
            );
            if ($rmDirectory) {
                @rmdir($dir);
            }
        }
    }

    private function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            array_map(
                function($name) use ($dir) {
                    $input = $dir.DIRECTORY_SEPARATOR.$name;
                    if (is_file($input)) {
                        @unlink($input);
                    } elseif (is_dir($input) && $name!='.' && $name!='..') {
                        $this->removeDirectory($input);
                    }
                },
                scandir($dir)
            );
            @rmdir($dir);
        }
    }

    private function getObjectDirectory($object)
    {
        if (!isset($this->objectDirectory)) {
            $this->objectDirectory = $this->getRootUploadDirectory().$this->getEntityDirectory($object).DIRECTORY_SEPARATOR.$object->getId();
        }

        return $this->objectDirectory;
    }

    private function getFieldDirectory($object, $field)
    {
        return $field
            ? $this->getObjectDirectory($object).DIRECTORY_SEPARATOR.$field
            : $this->getObjectDirectory($object)
        ;
    }

    private function getEntityDirectory($object)
    {
        if (isset($this->entityDirectory)) {
            return $this->entityDirectory;
        }

        if(is_object($object)) {
            $a = explode('\\', get_class($object));
            $class = array_pop($a);
            if (preg_match_all('/[A-Z0-9][a-z0-9]*/', $class, $matches)) {
                $this->entityDirectory = implode('_', array_map(function($str){return lcfirst($str);}, $matches[0]));
                return $this->entityDirectory;
            }
        }

        return '_all';
    }

    private function getRootUploadDirectory()
    {
        return $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . self::ROOT_UPLOAD_DIRECTORY . DIRECTORY_SEPARATOR;
    }

    private function getFilePath($object, $field)
    {
        $dir = $field
            ? $this->getObjectDirectory($object).DIRECTORY_SEPARATOR.$field
            : $this->getObjectDirectory($object)
        ;
        if (!is_dir($dir)) {
            return null;
        }
        $scan = scandir($dir,1);
        for ($i=0; $i<count($scan); $i++) {
            if (is_file($dir.DIRECTORY_SEPARATOR.$scan[$i])) {
                return $dir.DIRECTORY_SEPARATOR.$scan[$i];
            }
        }
        return null;
    }

    /**
     * Read low level file info
     * and return an array [ 'width' => <width>, ... 'filename' => <filename> ]
     *
     * @param $object
     * @return array|bool
     */
    public function getFileInfo($object, $field=null)
    {
        if ($this->fileExists($object, $field)) {
            $mime = $this->getMime($object, $field);
            $file = $this->getFile($object, $field);
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

    public function getMime($object, $field=null)
    {
        if ($field==self::NULL_FIELD) {
            $field = null;
        }
        return $this->fileExists($object, $field)
            ? mime_content_type($this->getFile($object, $field))
            : ''
        ;
    }

    public function getFileUrl($object, $field) {
        if(is_object($object)) {
            $a = explode('\\', get_class($object));
            $class = array_pop($a);
            if (preg_match_all('/[A-Z0-9][a-z0-9]*/', $class, $matches)) {
                $path = implode('_', array_map(function($str){return lcfirst($str);}, $matches[0]));
                $path .= self::FILE_CONTROLLER_PATH_SUFFIX;
                if ($this->router->getRouteCollection()->get($path)) {
                    try {
                        $url = $this->router->generate($path, ['id'=>$object->getId(), 'field'=>$field?$field:self::NULL_FIELD]);
                        return $url;
                    } catch (\Exception $e) { }
                }
            }
        }

        return null;
    }
}