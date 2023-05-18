<?php

namespace App\Controller;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Int64 extends Constraint
{
}