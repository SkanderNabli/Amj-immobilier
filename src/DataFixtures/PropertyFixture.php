<?php

namespace App\DataFixtures;

use App\Entity\Option;
use App\Entity\Property;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PropertyFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $tabOption= [
            0=>'Air conditioning',
            1=>'Heating',
            2=>'Terrace',
            3=>'Balcony',
            4=>'Garage',
            5=>'Central Heating',
            6=>'Laundry Room',
            7=>'Alarm'
            ];

        $factory = Factory::create('fr_FR');

        $user = new User();

        $user->setUsername('Shibalba')
            ->setLastname('Nabli')
            ->setFirstname('Skander')
            ->setCivility(0)
            ->setEmail('skandernabli34@gmail.com')
            ->setPassword('$2y$13$FQeCNWqq3hWvIdCuVHX/Yuqu/G9H3847lV0QZIvOANMaCfrJhLvl2')
            ->setTel('0627804915')
            ->setNewsletter(1)
        ;
        $manager->persist($user);


        for ($i = 0; $i < 100 ; $i++){

            $prop = new Property();


            $prop
                ->setTitle($factory->words(2,true))
                ->setDescr($factory->realText(200,1))
                ->setSurface($factory->numberBetween(30,500))
                ->setRooms($factory->numberBetween(1,5))
                ->setBedrooms($factory->numberBetween(1,4))
                ->setFloor($factory->numberBetween(0,4))
                ->setPrice($factory->numberBetween(10000,9999999))
                ->setHeat($factory->boolean)
                ->setCity($factory->city)
                ->setAddress($factory->streetAddress)
                ->setPostalCode($factory->numerify($factory->randomDigitNotNull.'####'))
                ->setCreatedAt($factory->date($format = 'Y-m-d H:i:s', $max = 'now'))
                ->setFeatured($factory->boolean)
                ->setAuthor($user)
                ->setCategory($factory->numberBetween(0,count(Property::CATEGORY)-1))
                ->setLat($factory->latitude)
                ->setLng($factory->longitude)
            ;

            $manager->persist($prop);
        }



        foreach ($tabOption as $optionName ){

            $option = new Option();

            $option->setName($optionName);
            $manager->persist($option);

        }

        $manager->flush();



    }



}
