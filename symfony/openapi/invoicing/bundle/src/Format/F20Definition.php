<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F20Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}