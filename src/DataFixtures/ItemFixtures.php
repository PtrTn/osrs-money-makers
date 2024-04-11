<?php

namespace App\DataFixtures;

use App\Entity\Item;
use App\Entity\Price;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ItemFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createItem($manager, 5931, 'Jute fibre', 1225);
        $this->createItem($manager, 21652, 'Drift net', 3000);
        $this->createItem($manager, 8782, 'Mahogany plank', 2104);
        $this->createItem($manager, 6332, 'Mahogany logs', 395);
        $this->createItem($manager, 561, 'Nature rune', 107);
        $this->createItem($manager, 9075, 'Astral rune', 78);
        $this->createItem($manager, 617, 'Coins', 1);
        $this->createItem($manager, 1761, 'Soft clay', 104);
        $this->createItem($manager, 563, 'Law rune', 118);
        $this->createItem($manager, 8007, 'Varrock teleport (tablet)', 465);
        $this->createItem($manager, 8013, 'Teleport to house (tablet)', 500);
        $this->createItem($manager, 2353, 'Steel bar', 440);
        $this->createItem($manager, 2, 'Cannonball', 164);
        $this->createItem($manager, 21967, 'Ruby dragon bolts', 901);
        $this->createItem($manager, 564, 'Cosmic rune', 86);
        $this->createItem($manager, 565, 'Blood rune', 234);
        $this->createItem($manager, 21944, 'Ruby dragon bolts (e)', 934);
        $this->createItem($manager, 24961, 'Catherby teleport (tablet)', 2499);
        $this->createItem($manager, 22198, 'Yew bird house', 1187);
        $this->createItem($manager, 22195, 'Mahogany bird house', 1429);
        $this->createItem($manager, 8792, 'Clockwork', 880);
        $this->createItem($manager, 1615, 'Dragonstone', 11283);
        $this->createItem($manager, 11115, 'Dragonstone bracelet', 11750);
        $this->createItem($manager, 11126, 'Combat bracelet', 12380);
        $this->createItem($manager, 11118, 'Combat bracelet(4)', 11118);
        $this->createItem($manager, 2357, 'Gold bar', 109);
        $this->createItem($manager, 1515, 'Yew logs', 222);

        $manager->flush();
    }

    private function createItem(ObjectManager $manager, int $gameId, string $name, int $avgPrice): void
    {
        $price = new Price();
        $price->setLow($avgPrice);
        $price->setHigh($avgPrice);

        $item = new Item();
        $item->setGameId($gameId);
        $item->setName($name);
        $item->setExamine(null);
        $item->setPrice($price);

        $this->addReference($name, $item);

        $manager->persist($price);
        $manager->persist($item);
    }
}
