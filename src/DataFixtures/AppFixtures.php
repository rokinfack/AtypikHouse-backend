<?php

namespace App\DataFixtures;

use App\Entity\HabitatProperty;
use App\Entity\Media;
use App\Entity\Reservation;
use App\Factory\ActivityFactory;
use App\Factory\CancelFactory;
use App\Factory\CategoryFactory;
use App\Factory\CommentFactory;
use App\Factory\HabitatFactory;
use App\Factory\HabitatPropertyFactory;
use App\Factory\LikeFactory;
use App\Factory\MediaFactory;
use App\Factory\NotificationFactory;
use App\Factory\PropertyFactory;
use App\Factory\ReportFactory;
use App\Factory\ReservationFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        MediaFactory::createMany(20);

        $users = UserFactory::createMany(20);

        ActivityFactory::createMany(20);

        CategoryFactory::createMany(20,
            function () {
                return [
//                    "image"=>MediaFactory::random()
                ];
            });

        HabitatFactory::createMany(20, function () {
            return [
                "category" => CategoryFactory::random(),
                "owner" => UserFactory::random()
//                "cover_image" => MediaFactory::random()
            ];
        });

        CommentFactory::createMany(20, function () {
            return ["user" => UserFactory::random()];
        });

        PropertyFactory::createMany(20);

        NotificationFactory::createMany(20, function () {
            return [
                "receiverUser" => UserFactory::random(),
                "senderUser" => UserFactory::random()
            ];
        });

        HabitatPropertyFactory::createMany(20, function () {
            return [
                "property" => PropertyFactory::random(),
                "habitat" => HabitatFactory::random(),
            ];
        });

        CancelFactory::createMany(20);

        ReservationFactory::createMany(20, function () {
            return [
                "user" => UserFactory::random(),
                "habitat" => HabitatFactory::random()];
        });

        LikeFactory::createMany(20, function () {
            return [
                "user" => UserFactory::random(),
                "habitat" => HabitatFactory::random()
            ];
        });
        ReportFactory::createMany(20, function () {
            return [
                "user" => UserFactory::random(),
                "habitat" => HabitatFactory::random()
            ];
        });
    }
}
