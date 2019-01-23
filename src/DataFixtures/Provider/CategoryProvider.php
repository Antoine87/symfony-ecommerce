<?php

namespace App\DataFixtures\Provider;

use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class CategoryProvider extends BaseProvider
{
    /** @var string[] */
    private $categoryNames = [];
    /** @var SplFileInfo[] */
    private $categoryDescriptionsFiles = [];
    /** @var SplFileInfo[] */
    private $categoryImagesFiles = [];


    public function __construct(
        Generator $aliceGenerator,
        Filesystem $filesystem,
        string $fixturesResourcesDir,
        string $categoryImagesDir
    )
    {
        $this
            ->loadMockaroo($fixturesResourcesDir)
            ->loadDescriptions($fixturesResourcesDir)
            ->loadImages($filesystem, $fixturesResourcesDir, $categoryImagesDir)
        ;
        parent::__construct($aliceGenerator);
    }

    private function loadMockaroo(string $dir): self
    {
        $file = $dir.'/categories/mockaroo.json';

        if (\is_file($file)) {
            $fields = \json_decode(\file_get_contents($file));
            foreach ($fields as $field) {
                $this->categoryNames[] = $field->name;
            }
        }

        return $this;
    }

    private function loadDescriptions(string $dir): self
    {
        $files = \iterator_to_array(
            Finder::create()->in($dir.'/categories/descriptions')->depth(0)->files()->name('*.md')
        );
        $this->categoryDescriptionsFiles = $files;

        return $this;
    }

    private function loadImages(Filesystem $fs, string $dir, string $categoryImagesDir): self
    {
        $files = \iterator_to_array(
            Finder::create()->in($dir.'/categories/images')->depth(0)->files()->name(['*.png', '*.jpg'])
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
        $this->categoryImagesFiles = $files;

        return $this;
    }


    /**
     * @return string|null
     */
    public function categoryName(): ?string
    {
        return empty($this->categoryNames)
            ? null
            : self::randomElement($this->categoryNames);
    }

    /**
     * @return string|null
     */
    public function categoryDescription(): ?string
    {
        return empty($this->categoryDescriptionsFiles)
            ? null
            : self::randomElement($this->categoryDescriptionsFiles)->getContents();
    }

    /**
     * @return string|null
     */
    public function categoryImage(): ?string
    {
        return empty($this->categoryImagesFiles)
            ? null
            : self::randomElement($this->categoryImagesFiles)->getFilename();
    }
}
