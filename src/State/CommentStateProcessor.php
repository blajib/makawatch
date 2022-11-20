<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;

class CommentStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Post) {
            $data->setCreatedAt(new \DateTimeImmutable(date('y-m-d h:i:s')));

            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        }

        if ($operation instanceof Patch) {
            $data->setUpdateAt(new \DateTimeImmutable(date('y-m-d h:i:s')));

            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        }

        return null;

    }
}
