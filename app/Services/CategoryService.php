<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CategoryService extends EntityManagerService
{
    public function create(string $name, User $user): Category
    {
        $category = new Category();

        $category->setUser($user);

        return $this->update($category, $name);
    }

    public function getPaginatedCategories(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManager->getRepository(Category::class)
            ->createQueryBuilder("c")
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);


        // defense against SQL Injection
        $orderBy = in_array($params->orderBy, ['name', 'createdAt', 'updatedAt']) ? $params->orderBy : 'updatedAt';
        $orderDir = in_array(strtolower($params->orderDir), ['asc', 'desc']) ? strtolower($params->orderDir) : 'asc';

        if (! empty($params->searchTerm)) {
            $query
                ->where("c.name LIKE :search")
                ->setParameter('search', '%' . addcslashes($params->searchTerm, '%_') . '%');
        }

        $query->orderBy("c.".$orderBy, $orderDir);

        return new Paginator($query);
    }

    public function delete(int $id): void
    {
        $category = $this->entityManager->find(Category::class, $id);

        $this->entityManager->remove($category);
    }

    public function getById(int $id): ?Category
    {
        return $this->entityManager->find(Category::class, $id);
    }

    public function getByName(string $name): ?Category
    {
        return $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $name]) ?? null;
    }

    public function update(Category $category, string $name): Category
    {
        $category->setName($name);

        $this->entityManager->persist($category);

        return $category;
    }

    public function getCategoryNames(): array
    {
        return $this->entityManager
            ->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->select('c.id', 'c.name')
            ->getQuery()
            ->getArrayResult();
    }

    public function getAllKeyedByName(): array
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();
        $categoriesMap = [];

        foreach ($categories as $category) {
            $categoriesMap[strtolower($category->getName())] = $category;
        }

        return $categoriesMap;
    }
}