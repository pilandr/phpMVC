<?php
namespace App\Model;

use Base\AbstractModel;
use Base\Db;

class User extends AbstractModel
{

    private int $id;
    private string $name;
    private string $password;
    private string $email;
    private string $createdAt;

    public function __construct($data = [])
    {
        if ($data) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->password = $data['password'];
            $this->createdAt = $data['created_at'];
        }
    }

    public static function getByIds(array $userIds): array
    {
        $db = Db::getInstance();
        $idsString = implode(',', $userIds);
        $data = $db->fetchAll(
            "SELECT * fROM users WHERE id IN($idsString)",
            __METHOD__
        );
        if (!$data) {
            return [];
        }


        $users = [];
        foreach ($data as $elem) {
            $user = new self($elem);
            $user->id = $elem['id'];
            $users[$user->id] = $user;
        }

        return $users;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function save(): int
    {
        $db = Db::getInstance();
        $insert = "INSERT INTO users (`name`, `password`, `email`) VALUES (
            :name, :password, :email
        )";
        $db->exec($insert, __METHOD__, [
            ':name' => $this->name,
            ':password' => $this->password,
            ':email' => $this->email
        ]);

        $id = $db->lastInsertId();
        $this->id = $id;

        return $id;
    }

    public static function getById(int $id): ?self
    {
        $db = Db::getInstance();
        $select = "SELECT * FROM users WHERE id = $id";
        $data = $db->fetchOne($select, __METHOD__);

        if (!$data) {
            return null;
        }

        return new self($data);
    }

    public static function getByName(string $name): ?self
    {
        $db = Db::getInstance();
        $select = "SELECT * FROM users WHERE `name` = :name";
        $data = $db->fetchOne($select, __METHOD__, [
            ':name' => $name
        ]);

        if (!$data) {
            return null;
        }

        return new self($data);
    }

    public static function getByEmail(string $email): ?self
    {
        $db = Db::getInstance();
        $select = "SELECT * FROM users WHERE `email` = :email";
        $data = $db->fetchOne($select, __METHOD__, [
            ':email' => $email
        ]);

        if (!$data) {
            return null;
        }

        return new self($data);
    }

    public static function getPasswordHash(string $password): string
    {
        return sha1(',.tj+gtf' . $password);
    }

    public function isAdmin(): bool
    {
        return in_array($this->id, ADMIN_IDS);
    }

}