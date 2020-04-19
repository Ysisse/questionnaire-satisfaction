<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DataFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $admin = new Admin();
        $admin->setCode("pXbW5dKKak");
        $manager->persist($admin);

        $manager->flush();
    }
}
