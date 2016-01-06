<?php
/**
 * This file is part of the Mesour DataGrid (http://grid.mesour.com)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\DataGrid\Column;

use Mesour;


/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
class Image extends BaseColumn
{

    private $maxWidth = 0;

    private $maxHeight = 0;

    private $previewPath;

    private $previewRootPath;

    private $rootPath;

    /**
     * Set max width in pixels
     *
     * @param $maxWidth 0 = unlimited
     * @return $this
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = (int)$maxWidth;
        return $this;
    }

    /**
     * Set max height in pixels
     *
     * @param $maxHeight 0 = unlimited
     * @return $this
     */
    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = (int)$maxHeight;
        return $this;
    }

    /**
     * @param $previewWebPath
     * @param null $previewRootPath default $_SERVER['DOCUMENT_ROOT']
     * @param null $imageRootPath   default $_SERVER['DOCUMENT_ROOT']
     * @return $this
     * @throws Mesour\InvalidArgumentException
     */
    public function setPreviewPath($previewWebPath, $previewRootPath = NULL, $imageRootPath = NULL)
    {
        if (!is_null($imageRootPath) && !is_string($imageRootPath)) {
            throw new Mesour\InvalidArgumentException('Image root path must be string. ' . gettype($imageRootPath) . ' given.');
        } elseif (is_null($imageRootPath)) {
            $this->rootPath = $_SERVER['DOCUMENT_ROOT'];
        } else {
            $this->rootPath = $imageRootPath;
        }

        if (!is_null($previewRootPath) && !is_string($previewRootPath)) {
            throw new Mesour\InvalidArgumentException('Preview root path must be string. ' . gettype($previewRootPath) . ' given.');
        } elseif (is_null($previewRootPath)) {
            $this->previewRootPath = $_SERVER['DOCUMENT_ROOT'];
        } else {
            $this->previewRootPath = $previewRootPath;
        }


        $this->previewPath = $previewWebPath;
        return $this;
    }

    public function getHeaderAttributes()
    {
        return [
            'class' => 'grid-column-' . $this->getName()
        ];
    }

    public function getBodyContent($data, $rawData)
    {
        $src = $this->tryInvokeCallback([$this, $rawData]);
        if ($src === self::NO_CALLBACK) {
            $src = $data[$this->getName()];
        }

        $img = Mesour\Components\Utils\Html::el('img');
        if (!$this->previewPath) {
            if ($this->maxWidth > 0) {
                $img->style('max-width:' . $this->fixPixels($this->maxWidth));
            }
            if ($this->maxHeight > 0) {
                $img->style('max-height:' . $this->fixPixels($this->maxHeight));
            }
        } else {
            $imageName = str_replace(['/', '\\'], '_', $src);
            $imageDir = $this->previewRootPath . '/' . $this->previewPath;
            $imagePath = $imageDir . '/' . $imageName;

            @mkdir($imageDir);

            if (!is_dir($imageDir)) {
                throw new Mesour\InvalidArgumentException('Image preview dir "' . $imageDir . '" does not exist.');
            }

            if (!is_file($imagePath)) {
                $image = Mesour\Components\Utils\Image::fromFile($this->createFullPath($src));
                $newWidth = $width = $image->getWidth();
                $newHeight = $height = $image->getHeight();

                if ($this->maxWidth > 0) {
                    if ($width > $this->maxWidth) {
                        $newWidth = $this->maxWidth;
                    }
                }
                if ($this->maxHeight > 0) {
                    if ($height > $this->maxHeight) {
                        $newHeight = $this->maxHeight;
                    }
                }

                $image->resize($newWidth, $newHeight, Mesour\Components\Utils\Image::FIT);
                $image->save($imagePath);
            }

            $src = $this->previewPath . '/' . $imageName;
        }
        $img->src($src);
        return $img;
    }

    private function createFullPath($image_file)
    {
        return $this->rootPath . $image_file;
    }

    private function fixPixels($value)
    {
        return is_numeric($value) ? ($value . 'px') : $value;
    }

}
