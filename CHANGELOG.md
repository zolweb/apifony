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
Le contrôleur reçoit une méthode de dénormalisation par modèle, si bien que les schémas récursifs
via `$ref` sont supportés : la récursion du schéma devient une récursion du code généré, bornée par
la donnée reçue (et par `max_input_nesting_level`, 64 par défaut).

```
?tree[name]=root&tree[children][0][name]=a&tree[children][0][children][0][name]=a1
```

#### Correction : le typage des items de tableaux est maintenant contrôlé sur toutes les versions

Découvert en couvrant les tableaux imbriqués. Le contrôle de type annoncé en 10.0.0 ne s'appliquait
pas aux **items d'un tableau** avant `symfony/serializer` 8. Mesuré, pour une propriété déclarée
`list<int>` recevant `["abc"]` :

| `symfony/serializer` | `list<int>` | `list<list<int>>` |
|---|---|---|
| 6.4.0 (plancher déclaré) | accepté | accepté |
| 6.4.46 (dernier LTS) | accepté | accepté |
| 7.2.9 | accepté | accepté |
| 7.4.19 | accepté | accepté |
| 8.1.7 | rejeté | rejeté |

Sur 6.4 et 7.x, le handler recevait donc un tableau dont les éléments contredisaient le `@param`
déclaré, sans erreur. Le bundle généré déclarant `symfony/serializer: ^6.4 || ^7.0 || ^8.0`, la
garantie ne valait que pour les projets sur Symfony 8.

Le type de l'item est désormais émis comme contrainte dans le `Assert\All` que produisait déjà
`ArrayType`, ce qui rend la garantie indépendante de la version de Symfony :

```php
#[Assert\All(constraints: [new Assert\Type(type: 'string'), new Assert\NotNull()])]
public readonly array $arrayProperty,

#[Assert\All(constraints: [new Assert\Type(type: 'array'), new Assert\NotNull(),
    new Assert\All(constraints: [new Assert\Type(type: 'int'), new Assert\NotNull()])])]
public readonly array $integerMatrixProperty,
```

**Les projets sur Symfony 6.4 ou 7.x verront donc des 400 sur des payloads qui passaient**, ce qui
est précisément l'objet de la 10.0.0. Un `number` continue d'accepter un entier comme un flottant,
conformément à ce que font déjà les lecteurs scalaires. Les items de type objet ne reçoivent pas de
contrainte : le normalizer ne sait de toute façon pas construire un objet à partir d'un scalaire.

Sur Symfony 8 le rejet vient du `Serializer`, en dessous il vient de la validation : le message
diffère donc selon la version, mais l'issue est la même.

Cela ne concernait **que le request body**. Les query params ne passent pas par le `Serializer` :
leur coercition est générée et applique les mêmes règles strictes quelle que soit la version.

#### Suppression de `DeserializerInterface::denormalize()`

La méthode générée `denormalize()` est supprimée du `DeserializerInterface` et du `Deserializer`.
Elle n'était appelée par rien — seul `AbstractController` consomme l'interface, et uniquement via
`deserialize()` — et elle passait `DISABLE_TYPE_ENFORCEMENT` à **TRUE**, l'inverse exact du contrat
annoncé en 10.0.0. Elle constituait donc un piège : l'utiliser réintroduisait silencieusement la
conversion de types que la 10.0.0 avait supprimée.

Retirer une méthode d'une interface ne casse aucune implémentation existante. En revanche, si du
code appelle directement `$deserializer->denormalize(...)`, il faut le remplacer par
`deserialize()` en lui passant du JSON.

Au passage, la propriété interne `$serializer` du `Deserializer` généré n'est plus typée
`SerializerInterface&DenormalizerInterface` mais `SerializerInterface` : l'intersection n'existait
que pour `denormalize()`.

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