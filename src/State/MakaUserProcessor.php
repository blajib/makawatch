<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MakaUserProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        $data->setPassword($this->hasher->hashPassword($data, "password"));
        $data->setCreatedAt(new \DateTimeImmutable(date('y-m-d h:i:s')));

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        return $result;

    }
}