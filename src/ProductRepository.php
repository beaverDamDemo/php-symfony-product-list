<?php

declare(strict_types=1);

final class ProductRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, name, subtitle, description, image FROM products ORDER BY id ASC'
        );

        $products = [];
        foreach ($statement->fetchAll() as $row) {
            $products[] = $this->mapRow($row);
        }

        return $products;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, name, subtitle, description, image FROM products WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();
        if (!is_array($row)) {
            return null;
        }

        return $this->mapRow($row);
    }

    private function mapRow(array $row): array
    {
        $decodedDescription = json_decode((string) ($row['description'] ?? '[]'), true);
        $description = [];
        if (is_array($decodedDescription)) {
            foreach ($decodedDescription as $paragraph) {
                if (is_string($paragraph)) {
                    $description[] = $paragraph;
                }
            }
        }

        if ($description === []) {
            $description = [(string) ($row['description'] ?? '')];
        }

        return [
            'id' => (int) ($row['id'] ?? 0),
            'name' => (string) ($row['name'] ?? 'Izdelek'),
            'subtitle' => (string) ($row['subtitle'] ?? ''),
            'description' => $description,
            'image' => (string) ($row['image'] ?? ''),
        ];
    }
}
