<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class JobFixtures extends Fixture 
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for($i=0; $i < 5; $i++) {

            $job = new Job();
            $job->setTitle( $faker->jobTitle );

            $manager->persist($job);
        }

        $manager->flush();
    }
}
