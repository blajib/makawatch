<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Type;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ItemRepository;
use App\Repository\MediaRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    private UserRepository $userRepository;

    private ItemRepository $itemRepository;

    private MediaRepository $mediaRepository;

    private CategoryRepository $categoryRepository;

    private TypeRepository $typeRepository;

    private CommentRepository $commentRepository;

    private array $categories = [
        'repas',
        'politesse',
        'jeux',
        'voyage',
        'hopital',
        'famille',
    ];

    private $types = [
        'video',
        'image',
    ];

    public function __construct(UserRepository $userRepository, ItemRepository $itemRepository, MediaRepository $mediaRepository, CategoryRepository $categoryRepository, TypeRepository $typeRepository, CommentRepository $commentRepository)
    {
        $this->userRepository = $userRepository;
        $this->itemRepository = $itemRepository;
        $this->mediaRepository = $mediaRepository;
        $this->categoryRepository = $categoryRepository;
        $this->typeRepository = $typeRepository;
        $this->commentRepository = $commentRepository;
    }

    public function load(ObjectManager $manager): void
    {
        // création du SUPER_ADMIN
        $user = new User();
        $user->setFirstName('jib');
        $user->setLastName('bla');
        $user->setPassword('california');
        $user->setCreatedAt(new \DateTime('now'));
        $user->setEmail('bla@gmail.com');
        $user->setBirthDate(new \DateTime('08-04-1988'));
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setProfession('developpeur');

        $manager->persist($user);

        // création de plusieurs catégories d'items
        /** @var Category $category */
        foreach ($this->categories as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $user->addCategory($category);
            $manager->persist($user);

        }

        //Création des différents type de médias
        foreach ($this->types as $typeName) {
            $manager->persist((new Type())->setName($typeName));

        }
        
        $manager->flush();
    }
}
