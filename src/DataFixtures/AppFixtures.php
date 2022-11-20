<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Item;
use App\Entity\Media;
use App\Entity\Type;
use App\Entity\MakaUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{
    private array $categorieNames = [
        'repas',
        'politesse',
        'jeux',
        'voyage',
        'hopital',
        'famille',
    ];

    private array $typeNames = [
        'video',
        'image',
    ];

    private array $itemNames = [
        'manger',
        'lire',
        'bonjour',
        'dormir',
        'voler',
        'jouer à la console',
        'effectuer un saut en parachute',
    ];

    private array $levels = [
        1, 2, 3, 4, 5, 6, 7,
    ];

    private array $users = [];

    private string $commentText = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.";

    private array $categories;

    private array $types;

    private UserPasswordHasherInterface $hasher;

    /**
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        // création du SUPER_ADMIN
        $userAdmin = new MakaUser();
        $userAdmin->setFirstName('jib');
        $userAdmin->setLastName('bla');
        $userAdmin->setPassword($this->hasher->hashPassword($userAdmin,'california'));
        $userAdmin->setCreatedAt(new \DateTime('now'));
        $userAdmin->setEmail('bla@gmail.com');
        $userAdmin->setBirthDate(new \DateTime('08-04-1988'));
        $userAdmin->setRoles(['ROLE_USER']);
        $userAdmin->setProfession('developpeur');

        $manager->persist($userAdmin);
        $manager->flush();
        $this->users[] = $userAdmin;

        for ($i = 1; $i <= 30; $i++) {
            $user = new MakaUser();
            $user->setEmail($faker->email);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setProfession($faker->domainName);
            $user->setRoles(['ROLE_USER']);
            $user->setBirthDate($faker->dateTimeBetween('-50 years', '-20 years'));
            $user->setCreatedAt(new \DateTime('now'));
            $user->setPassword($this->hasher->hashPassword($user,'california'));

            $manager->persist($user);
            $manager->flush();
            $this->users[] = $user;
        }

        //création de plusieurs catégories d'items
        /** @var Category $category */
        foreach ($this->categorieNames as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            $userAdmin->addCategory($category);

            $manager->persist($userAdmin);
            $manager->flush();
            $this->categories[] = $category;
        }

        //Création des différents type de médias
        foreach ($this->typeNames as $typeName) {
            $type = new Type();
            $type->setName($typeName);

            $manager->persist($type);
            $manager->flush();
            $this->types[] = $type;

        }

        //création Items/Medias/comments
        foreach ($this->itemNames as $itemName) {
            $user = $this->users[array_rand($this->users)];

            $comment = new Comment();
            $comment->setMakaUser($user);
            $comment->setText($this->commentText);
            $manager->persist($comment);
            $manager->flush();

            $media = new Media();
            $media->setName($itemName);
            $media->setMakaUser($user);
            $media->setType($this->types[array_rand($this->types)]);
            $media->setVerified(true);
            $media->addComment($comment);
            $manager->persist($media);
            $manager->flush();

            $item = new Item();
            $item->setName($itemName);
            $item->setMakaUser($user);
            $item->addCategory($this->categories[array_rand($this->categories)]);
            $item->addMedia($media);
            $item->setLevel($this->levels[array_rand($this->levels)]);
            $item->setVerified(true);
            $manager->persist($item);
            $manager->flush();

        }
    }
}
