<?php

namespace App\DataFixtures;

use App\Entity\Item;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ItemFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $items = [];
        $items[] = $this->createItem(5931, 'Jute fibre', 1225);
        $items[] = $this->createItem(21652, 'Drift net', 3000);
        $items[] = $this->createItem(8782, 'Mahogany plank', 2104);
        $items[] = $this->createItem(6332, 'Mahogany logs', 395);
        $items[] = $this->createItem(561, 'Nature rune', 107);
        $items[] = $this->createItem(9075, 'Astral rune', 78);
        $items[] = $this->createItem(617, 'Coins', 1);
        $items[] = $this->createItem(1761, 'Soft clay', 104);
        $items[] = $this->createItem(563, 'Law rune', 118);
        $items[] = $this->createItem(8007, 'Varrock teleport (tablet)', 465);
        $items[] = $this->createItem(8013, 'Teleport to house (tablet)', 500);
        $items[] = $this->createItem(2353, 'Steel bar', 440);
        $items[] = $this->createItem(2, 'Cannonball', 164);
        $items[] = $this->createItem(21967, 'Ruby dragon bolts', 901);
        $items[] = $this->createItem(564, 'Cosmic rune', 86);
        $items[] = $this->createItem(565, 'Blood rune', 234);
        $items[] = $this->createItem(21944, 'Ruby dragon bolts (e)', 934);

        foreach ($items as $item) {
            $manager->persist($item);
        }
        $manager->flush();
    }

    private function createItem(int $gameId, string $name, int $price): Item
    {
        $item = new Item();
        $item->setGameId($gameId);
        $item->setName($name);
        $item->setExamine(null);
        $item->setLowPrice(null);
        $item->setHighPrice(null);
        $item->setAvgPrice($price);

        $this->addReference($name, $item);

        return $item;
    }
}
