# CHANGELOG

## 10.0

### 10.0.0

**Breaking change majeur dans cette nouvelle version.**

Avant cette version, il était possible de deserialize d'un type vers un autre.
Par exemple, si l'API attendait un int mais qu'une string était reçue dans le JSON, une conversion silencieuse était faite.
Avec cette nouvelle version, le résultat sera maintenant une erreur 400 de validation.
Concrètement, Apifony force maintenant un typage correct dans le JSON en configurant ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT à **FALSE**.

cf. https://symfony.com/doc/current/serializer.html#recursive-denormalization-and-type-safety