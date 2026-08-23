<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Zoopla\Contracts;

interface ZooplaTransport
{
    public function sendProperty(string $reference, array $payload, array $credentials): array;

    public function removeProperty(string $reference, array $credentials): array;

    public function branchPropertyList(array $credentials): array;
}
