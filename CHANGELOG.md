# CHANGELOG

## 10.1

### 10.1.0

**Support des query params de type `array` et `object`.**

Un paramètre `in: query` peut maintenant avoir un schéma de type `array` ou `object`, à profondeur
arbitraire, et il est fortement typé dans l'interface du handler. Quand le type natif PHP ne suffit
pas à l'exprimer, un bloc PHPDoc est généré sur la méthode de l'interface :

```php
/**
 * @param list<string>    $qTags
 * @param list<list<int>> $qMatrix
 * @param list<Abc>       $qAbcList
 */
public function op(array $qTags, array $qMatrix, array $qAbcList, OpFilter $qFilter): Op200Response;
```

Les schémas inline de type objet donnent lieu à un modèle généré, comme pour les request bodies.

#### Sérialisation

Seule la **notation à crochets** de PHP est acceptée, la seule qui permette la profondeur :

```
?tags[]=a&tags[]=b
?tags[0]=a&tags[1]=b
?matrix[0][]=1&matrix[0][]=2&matrix[1][]=3
?filter[name]=x&filter[tags][]=y
```

Les styles OpenAPI `form` (`?tags=a&tags=b`, `?tags=a,b`), `spaceDelimited` et `pipeDelimited` ne
sont **pas** supportés : PHP ne conserve que la dernière valeur d'une clé répétée, et aucun de ces
styles ne permet d'aller au-delà d'un niveau. `style` et `explode` restent ignorés.

#### Contraintes

- `array` et `object` ne sont supportés qu'en `query`. En `path`, `header` ou `cookie`, la
  génération échoue avec un message explicite.
- Les schémas récursifs (via `$ref`) ne sont pas supportés pour ces paramètres.
- Un paramètre tableau optionnel se déclare avec `default: []`, un paramètre objet optionnel avec
  `type: ['object', 'null']` et `default: null` — ce sont les seules valeurs par défaut que
  `Schema` accepte pour ces types.
- Un tableau se transmet avec des **crochets vides** (`?tags[]=a&tags[]=b`). Tout ce dont les clés
  devraient être réécrites pour former une liste est rejeté en 400 plutôt que réindexé
  silencieusement : `?tags[0]=a&tags[2]=b` et `?tags[foo]=a` échouent. Noter que `parse_str` rend
  `?tags[0]=a&tags[1]=b` indistinguable des crochets vides, cette forme est donc acceptée.
- Une query string ne peut pas transporter `null` : un `list<?string>` ou un `?array` requis ne
  vaudront jamais `null` sur un élément reçu.
- `max_input_vars` (1000 par défaut) tronque `$_GET` silencieusement. Déclarer un `maxItems` sur
  les paramètres tableau reste recommandé.

#### Correction

Envoyer un tableau à un paramètre scalaire (`?monParam[]=a`) faisait lever une
`BadRequestException` par `InputBag::get()`, convertie par `HttpKernel` en 400 générique qui
court-circuitait l'enveloppe `validation_failed`. Ces paramètres sont maintenant lus sans
contrainte de type puis validés, et l'erreur est rapportée normalement :

```json
{"code":"validation_failed","message":"Validation has failed.",
 "errors":{"query":{"monParam":["Parameter 'monParam' in 'query' must be a string."]}}}
```

Les violations de contraintes imbriquées dans un paramètre tableau ou objet sont préfixées par leur
emplacement (`[0]: ...`, `nestedObject.email: ...`). Les paramètres scalaires sont inchangés.

**La régénération du bundle est nécessaire** (`AbstractController`, contrôleurs et interfaces de
handler changent).

## 10.0

### 10.0.0

**Breaking change majeur dans cette nouvelle version.**

Avant cette version, il était possible de deserialize d'un type vers un autre.
Par exemple, si l'API attendait un int mais qu'une string était reçue dans le JSON, une conversion silencieuse était faite.
Avec cette nouvelle version, le résultat sera maintenant une erreur 400 de validation.
Concrètement, Apifony force maintenant un typage correct dans le JSON en configurant ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT à **FALSE**.

cf. https://symfony.com/doc/current/serializer.html#recursive-denormalization-and-type-safety