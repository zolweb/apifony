<?php

namespace AppZolInvoicingPresentationApiBundle\Format;

interface F14Definition
{
    /**
     * @return array<string>
     */
    public function validate(mixed $value): array;
}