<?php

namespace App\DataFixtures;

use App\Entity\Jeopardy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\ObjectMapper\ObjectMapper;

class JeopardyFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {

        $data = '{"category":"ANIMALS EN ESPA\u00d1OL"}';
        $decoded = json_decode($data, true);

// Clean strings to guarantee valid UTF-8
        foreach ($decoded as &$value) {
            if (is_string($value)) {
                $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);
            }
        }
//        dd($data, $value);
        unset($value);

// Insert $decoded into your database


        $mapper = new ObjectMapper();
        $filename = 'data/jeopardy.json';
//        $filename = 'data/sample.json';
        // $product = new Product();
        // $manager->persist($product);
        foreach (json_decode(file_get_contents($filename)) as $idx => $value) {


//            if (!$valid) {
//                dd($value);
//            }
//            $value = json_encode(json_decode($value));
//            if (!json_validate())


//                $encoded = json_encode($value);
//                $encoded = iconv('UTF-8', 'UTF-8//IGNORE', $encoded);
//                dd($value, $encoded);
//                assert(json_validate($encoded), $encoded);
//                assert(mb_check_encoding($encoded, 'UTF-8'), "Not UTF-8 " .  $encoded);
//                dump($idx, $encoded);
//                $value = json_decode($encoded);
//
            $entity = $mapper->map($value, Jeopardy::class);
            $manager->persist($entity);
////            }
            if (($idx + 1) % 10000 === 0) {
                try {
                    $manager->flush();
                } catch (\Exception $e) {
                    dd($value, $idx, $e->getMessage());
                }
                dump($entity, $idx);
                $manager->clear();
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['jeopardy'];
    }
}
