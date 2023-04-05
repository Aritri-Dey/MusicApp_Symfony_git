<?php

namespace App\DataFixtures;

use App\Entity\Music;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MusicFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $music = new Music();
        $music->setUid(1);
        $music->setTitle('Apna bana le');
        $music->setSubtitle('Bhediya');
        $music->setSongPath('/assets/audio/ApnaBanaLe.mp3');
        $music->setImgPath('/assets/image/apnabanale.jpg');
        $manager->persist($music);

        $music2 = new Music();
        $music2->setUid(2);
        $music2->setTitle('Choo Lo');
        $music2->setSubtitle('Local train');
        $music2->setSongPath('/assets/audio/ChooLo.mp3');
        $music2->setImgPath('/assets/image/localtrain.jpg');
        $manager->persist($music2);

        $music3 = new Music();
        $music3->setUid(3);
        $music3->setTitle('Aro Ekbar');
        $music3->setSubtitle('Fossils');
        $music3->setSongPath('/assets/audio/AroEkbaarFossils.mp3');
        $music3->setImgPath('/assets/image/fossils.jpg');
        $manager->persist($music3);

        $music4 = new Music();
        $music4->setUid(4);
        $music4->setTitle('Tum Jab Paas');
        $music4->setSubtitle('Prateek Kuhad');
        $music4->setSongPath('/assets/audio/TumJabPaas.mp3');
        $music4->setImgPath('/assets/image/prateek.jpg');
        $manager->persist($music4);

        $music5 = new Music();
        $music5->setUid(5);
        $music5->setTitle('Pasoori');
        $music5->setSubtitle('Coke Studio');
        $music5->setSongPath('/assets/audio/Pasoori.mp3');
        $music5->setImgPath('/assets/image/pasoori.jpg');
        $manager->persist($music5);

        $music6 = new Music();
        $music6->setUid(6);
        $music6->setTitle('Khairiyat');
        $music6->setSubtitle('Chhichore');
        $music6->setSongPath('/assets/audio/Khairiyat.mp3');
        $music6->setImgPath('/assets/image/khairiyat.jpg');
        $manager->persist($music6);

        $music7 = new Music();
        $music7->setUid(7);
        $music7->setTitle('Nazm Nazm');
        $music7->setSubtitle('Bareilly ki barfi');
        $music7->setSongPath('/assets/audio/NazmNazm.mp3');
        $music7->setImgPath('/assets/image/nazm.jpg');
        $manager->persist($music7);

        $music8 = new Music();
        $music8->setUid(8);
        $music8->setTitle('Hasnuhana');
        $music8->setSubtitle('Fossils');
        $music8->setSongPath('/assets/audio/HasnuhanaFossils.mp3');
        $music8->setImgPath('/assets/image/fossils.jpg');
        $manager->persist($music8);

        $music9 = new Music();
        $music9->setUid(9);
        $music9->setTitle('Ekla Ghor');
        $music9->setSubtitle('Fossils');
        $music9->setSongPath('/assets/audio/EklaGhorFossils.mp3');
        $music9->setImgPath('/assets/image/fossils.jpg');
        $manager->persist($music9);

        $manager->flush(); //to make sure both the queries run at the same time
    }
}
