<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\MakaUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class MakaUserProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    /**
     * @param MakaUser $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return mixed
     */
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
