<?php

namespace App\DataFixtures;

use App\Entity\Music;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MusicFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $listOfMusic = [
            [
            'uid' => 1,
            'title' => 'Apna bana le',
            'subtitle' => 'Bhediya',
            'songPath' => '/assets/audio/ApnaBanaLe.mp3',
            'imgPath' => '/assets/image/apnabanale.jpg',
            ],
            [
            'uid' => 2,
            'title' => 'Choo Lo',
            'subtitle' => 'Local train',
            'songPath' => '/assets/audio/ChooLo.mp3',
            'imgPath' => '/assets/image/localtrain.jpg',
            ],
            [
            'uid' => 3,
            'title' => 'Aro Ekbar',
            'subtitle' => 'Fossils',
            'songPath' => '/assets/audio/AroEkbaarFossils.mp3',
            'imgPath' => '/assets/image/fossils.jpg',
            ],
            [
            'uid' => 4,
            'title' => 'Tum Jab Paas',
            'subtitle' => 'Prateek Kuhhad',
            'songPath' => '/assets/audio/TumJabPaas.mp3',
            'imgPath' => '/assets/image/prateek.jpg',
            ],
            [
            'uid' => 5,
            'title' => 'Pasoori',
            'subtitle' => 'Coke Studio',
            'songPath' => '/assets/audio/Pasoori.mp3',
            'imgPath' => '/assets/image/pasoori.jpg',
            ],
            [
            'uid' => 6,
            'title' => 'Khairiyat',
            'subtitle' => 'Chichhore',
            'songPath' => '/assets/audio/Khairiyat.mp3',
            'imgPath' => '/assets/image/khairiyat.jpg',
            ],
            [
            'uid' => 7,
            'title' => 'Nazm Nazm',
            'subtitle' => 'aireilly Ki Barfi',
            'songPath' => '/assets/audio/NazmNazm.mp3',
            'imgPath' => '/assets/image/nazm.jpg',
            ],
            [
            'uid' => 8,
            'title' => 'Hasnuhana',
            'subtitle' => 'Fossils',
            'songPath' => '/assets/audio/HasnuhanaFossils.mp3',
            'imgPath' => '/assets/image/fossils.jpg',
            ],
            [
            'uid' => 9,
            'title' => 'Ekla Ghor',
            'subtitle' => 'Fossils',
            'songPath' => '/assets/audio/EklaGhorFossils.mp3',
            'imgPath' => '/assets/image/fossils.jpg',
            ],
        ];
        foreach($listOfMusic as $musicRow) {
            $music = new Music();
            $music->setUid($musicRow['uid']);
            $music->setTitle($musicRow['title']);
            $music->setSubTitle($musicRow['subtitle']);
            $music->setSongPath($musicRow['songPath']);
            $music->setImgPath($musicRow['imgPath']);
            $manager->persist($music);
        }
        $manager->flush(); 
    }
}
