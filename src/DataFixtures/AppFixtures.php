<?php

namespace App\DataFixtures;


use Doctrine\Persistence\ObjectManager;
use App\Entity\Participants;

class AppFixtures
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $participant = new Participants();
        $participant->setEmail("toto@titi.com");

        $manager->flush();
    }
}
