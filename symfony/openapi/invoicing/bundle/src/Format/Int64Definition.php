<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface Int64Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}