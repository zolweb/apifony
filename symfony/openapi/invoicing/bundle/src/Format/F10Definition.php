<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F10Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}