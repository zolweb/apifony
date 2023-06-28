<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F17Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}