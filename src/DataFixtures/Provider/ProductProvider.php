<?php

namespace App\DataFixtures\Provider;

use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class ProductProvider extends BaseProvider
{
    /** @var string[] */
    private $productNames = [];
    /** @var string[] */
    private $productFeatures = [];
    /** @var SplFileInfo[] */
    private $productDescriptionsFiles = [];
    /** @var SplFileInfo[] */
    private $productImagesFiles = [];


    public function __construct(
        Generator $aliceGenerator,
        Filesystem $filesystem,
        string $fixturesResourcesDir,
        string $productImagesDir
    )
    {
        $this
            ->loadMockaroo($fixturesResourcesDir)
            ->loadImages($filesystem, $fixturesResourcesDir, $productImagesDir)
            ->loadDescriptions($fixturesResourcesDir)
        ;
        parent::__construct($aliceGenerator);
    }

    private function loadMockaroo(string $dir): self
    {
        $file = $dir.'/products/mockaroo.json';

        if (\is_file($file)) {
            $fields = \json_decode(\file_get_contents($file));
            foreach ($fields as $field) {
                $this->productNames[] = $field->name;
                $this->productFeatures[] = $field->feature;
            }
        }

        return $this;
    }

    private function loadDescriptions(string $dir): self
    {
        $files = \iterator_to_array(
            Finder::create()->in($dir.'/products/descriptions')->depth(0)->files()->name('*.md')
        );
        $this->productDescriptionsFiles = $files;

        return $this;
    }

    private function loadImages(Filesystem $fs, string $dir, string $categoryImagesDir): self
    {
        $files = \iterator_to_array(
            Finder::create()->in($dir.'/products/images')->depth(0)->files()->name(['*.png', '*.jpg'])
        );

        if (!$fs->exists($categoryImagesDir)) {
            $fs->mkdir($categoryImagesDir);
        }
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if (!$fs->exists($categoryImagesDir.'/'.$file->getFilename())) {
                $fs->copy($file->getRealPath(), $categoryImagesDir.'/'.$file->getFilename());
            }
        }
        $this->productImagesFiles = $files;

        return $this;
    }


    /**
     * @return string|null
     */
    public function productName(): ?string
    {
        return empty($this->productNames)
            ? null
            : self::randomElement($this->productNames);
    }

    /**
     * @return string|null
     */
    public function productFeatureName(): ?string
    {
        return empty($this->productFeatures)
            ? null
            : self::randomElement($this->productFeatures);
    }

    /**
     * @return string|null
     */
    public function productDescription(): ?string
    {
        return empty($this->productDescriptionsFiles)
            ? null
            : self::randomElement($this->productDescriptionsFiles)->getContents();
    }

    /**
     * @return string|null
     */
    public function productImage(): ?string
    {
        return empty($this->productImagesFiles)
            ? null
            : self::randomElement($this->productImagesFiles)->getFilename();
    }
}
