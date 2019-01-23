<?php

declare(strict_types=1);

namespace App\DataFixtures\Loader;

use App\Module\Customer\Customer;
use App\Module\Order\Offer;
use App\Module\Order\Order;
use App\Module\Product\Feature;
use App\Module\Product\Product;
use App\Module\Product\Review;
use Faker\Generator;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Nelmio\Alice\IsAServiceTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CustomLoader implements LoaderInterface
{
    use IsAServiceTrait;

    private $decoratedLoader;
    private $passwordEncoder;
    private $faker;

    /** @var Order[] */
    private $orders = [];
    /** @var Product[] */
    private $products = [];
    /** @var Review[] */
    private $reviews = [];
    /** @var Customer[] */
    private $customers = [];
    /** @var Offer[] */
    private $offers = [];
    /** @var Feature[] */
    private $productFeatures = [];


    public function __construct(
        LoaderInterface $decoratedLoader,
        Generator $aliceGenerator,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->decoratedLoader = $decoratedLoader;
        $this->faker = $aliceGenerator;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(array $fixturesFiles, array $parameters = [], array $objects = [], PurgeMode $purgeMode = null): array
    {
        $objects = $this->decoratedLoader->load($fixturesFiles, $parameters, $objects, $purgeMode);

        foreach ($objects as $fixtureName => $object) {
            if ($object instanceof Order) {
                $this->orders[$fixtureName] = $object;
            } else if ($object instanceof Product) {
                $this->products[$fixtureName] = $object;
            } else if ($object instanceof Review) {
                $this->reviews[$fixtureName] = $object;
            } else if ($object instanceof Customer) {
                $this->customers[$fixtureName] = $object;
            } else if ($object instanceof Offer) {
                $this->offers[$fixtureName] = $object;
            } else if ($object instanceof Feature) {
                $this->productFeatures[$fixtureName] = $object;
            }
        }

        $this
            ->encodePasswords()
            ->addProductFeatures()
            ->assignProductReviews()
        ;
        return $objects;
    }

    private function encodePasswords(): self
    {
        foreach ($this->customers as $customer) {
            $customer->setPassword($this->passwordEncoder->encodePassword($customer, $customer->getPassword()));
        }

        return $this;
    }

    private function addProductFeatures(): self
    {
        foreach ($this->products as $product) {
            for ($i = 1, $rand = \mt_rand(0, 3); $i <= $rand; $i++) {
                $product->addFeature($this->faker->randomElement($this->productFeatures));
            }
        }

        return $this;
    }

    /**
     * Multi-column unique constraint
     *
     * @see Review::_forAliceDoNotUseInvalidState()
     */
    private function assignProductReviews(): self
    {
        $uniqueCustomerProduct = [];
        foreach (\array_keys($this->customers) as $customer) {
            foreach (\array_keys($this->products) as $product) {
                $uniqueCustomerProduct[] = [$customer, $product];
            }
        }
        foreach ($this->reviews as $review) {
            [$customer, $product] = $this->faker->unique()->randomElement($uniqueCustomerProduct);
            $review
                ->_forAliceDoNotUseSetAuthor($this->customers[$customer])
                ->_forAliceDoNotUseSetProduct($this->products[$product]);
        }

        return $this;
    }
}
