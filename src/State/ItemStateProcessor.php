<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class ItemStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        $data->setCreatedAt(new \DateTimeImmutable(date('y-m-d h:i:s')));

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        return $result;

    }
}
