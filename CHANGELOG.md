# CHANGELOG

## 11.0

### 11.0.0

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

#### Le composant Serializer de Symfony n'est plus utilisé

Le request body était le dernier consommateur du `Serializer` — deux sites d'appel, dans
`getObjectRequestBody` et `getObjectOrNullRequestBody`. Il est remplacé par du code généré, comme
les query params depuis la 10.1.0. Les réponses n'étaient pas concernées : elles passent par
`JsonResponse`, donc par `json_encode`.

**Le bundle généré passe de dix à cinq dépendances.** Disparaissent `symfony/serializer`,
`symfony/property-info`, `symfony/property-access`, `phpstan/phpdoc-parser` et
`phpdocumentor/type-resolver`.

Motivations, au-delà de l'allègement :

- **La garantie de typage ne dépend plus de la version de Symfony.** Le contrôle des items de
  tableaux annoncé en 10.0.0 n'était réellement appliqué qu'à partir de `symfony/serializer` 8
  (mesuré sur 6.4.0, 6.4.46, 7.2.9 et 7.4.19 : une propriété déclarée `list<int>` acceptait
  `["abc"]`). Le code généré applique les mêmes règles partout.
- **Les erreurs client ne fuitent plus les noms de classes générées.** Au lieu de
  `Request body could not be deserialized: The type of the "x" attribute for class "Ns\Model\Schema"
  must be one of "int"`, on obtient `Property 'objectProperty.stringProperty' in 'requestBody' must
  be an integer.`, avec le chemin exact de la valeur fautive.
- **Un couplage invisible disparaît.** La dénormalisation ne fonctionnait que parce que
  `PhpStanExtractor` lisait les `@param list<Foo>` des constructeurs promus. Rien ne le disait, et
  activer `no_superfluous_phpdoc_tags` sur le bundle aurait suffi à la dégrader silencieusement.
- **Le code généré est vérifié par PHPStan** au niveau max, là où le `@return T` du `Serializer`
  n'était qu'une assertion.

Le dénormaliseur garantit désormais exactement ce que le PHPDoc du modèle annonce, y compris les
types affinés que la validation seule contrôlait auparavant :

```php
$v10 = $this->denormalizeStringJson($this->getRequiredJsonProperty($v0, 'enumStringProperty', $v9), $v9);
if (!\in_array($v10, ['abc', 'def', 'ghi'], true)) {
    throw new DenormalizationException($this->getJsonErrorMessage($v9, 'must be one of \'abc\', \'def\', \'ghi\'.'));
}
```

**Conséquences pour les projets existants :**

- `DeserializerInterface` et `Deserializer` ne sont plus générés, et le service correspondant
  disparaît de `services.yaml`. Le constructeur de `AbstractController` ne prend plus que
  `$validator`.
- Une valeur d'enum ou hors bornes dans un body est désormais rejetée à la dénormalisation, avec
  un message plat, là où elle l'était par la validation avec un message clé par propriété.
- Les applications qui activaient `framework.property_info` uniquement pour apifony peuvent le
  retirer.
- Les méthodes de dénormalisation sont émises **une fois par modèle** sur l'`AbstractController`,
  et non plus par contrôleur : un modèle partagé par plusieurs opérations n'est plus dupliqué.
- Les modèles générés portent une contrainte `Assert\Type` sur les items de leurs tableaux. Elle
  est redondante avec la dénormalisation, qui garantit déjà le type, et sert de filet si le code
  généré était fautif.

#### Correction : une propriété dont le `default` vaut `null` est enfin optionnelle dans le DTO

`ModelAttribute::hasDefault()` testait `default !== null` au lieu de `hasDefault`, confondant donc
« a une valeur par défaut » et « a une valeur par défaut non nulle ». Une propriété non requise
déclarée `default: null` — la seule valeur pour laquelle la distinction compte — était rendue
**obligatoire** dans le constructeur du modèle généré, alors que ses voisines ne l'étaient pas :

```php
public readonly string $defaultProperty = 'abc',           // default: 'abc'
public readonly array $emptyArrayDefaultProperty = [],     // default: []
public readonly ?string $nullDefaultProperty,              // default: null  <- pas de défaut
```

La désérialisation n'était pas concernée : elle lit `required` du schéma et passe tous les
arguments nommés. Seul le code construisant un modèle à la main, typiquement les payloads de
réponse dans les handlers, devait passer `nullDefaultProperty: null` explicitement.

**Attention, l'ordre des paramètres du constructeur change.** `Model` trie les attributs en plaçant
ceux qui ont un défaut en dernier ; la propriété corrigée passe donc du premier groupe au second.
Le code généré n'est pas affecté (il n'utilise que des arguments nommés), mais **toute construction
positionnelle d'un modèle concerné doit être revue** — l'arité reste la même et les types sont
souvent compatibles, donc la casse peut être silencieuse.

#### Correction : `x-apifony-raw` accepte enfin n'importe quelle valeur

Un request body déclaré `x-apifony-raw` générait `(new \ReflectionClass(mixed::class))` comme
valeur initiale. `mixed::class` est syntaxiquement valide et vaut la chaîne `"mixed"`, si bien que
chaque requête levait `ReflectionException: Class "mixed" does not exist` — **une 500 systématique**.
Le bug est antérieur à cette version et n'était couvert par aucun test.

Par ailleurs la dénormalisation d'une valeur raw exigeait un tableau, ce qui contredit son
intention : recevoir le résultat de `json_decode`, quel qu'il soit. Une valeur raw est désormais
transmise telle quelle, sans contrôle de forme, que ce soit pour un request body, un query param ou
une propriété imbriquée dans un modèle :

```php
// body
$requestBodyPayload = $this->getJsonRequestBody($request);

// query param
return $this->getRawParameter($request, $name, $in);
```

Un body `[1, "two", false]` ou `"une chaîne"` est donc accepté sur un schéma raw, là où seul un
objet passait. La fixture couvre maintenant les trois emplacements, avec une seconde opération
dédiée.

#### Les noms de la spec qui ne peuvent pas devenir un identifiant PHP sont rejetés

Transformer un nom de la spécification en identifiant PHP supprime tout ce qui n'est ni lettre ni
chiffre. Rien ne vérifiait le résultat, et l'échec était silencieux : le générateur écrasait sa
propre sortie et annonçait un succès. Mesuré sur des spécifications valides, avant correction :

| Spécification | Résultat |
|---|---|
| `operationId` `getUser` et `get_user` | un seul agrégat, un seul handler, **une seule route** — l'autre endpoint absent du bundle |
| schémas `User` et `user` | un seul `User.php`, contenant le second ; un `$ref` vers le premier liait le mauvais modèle |
| formats `date-time` et `dateTime` | classes mutuellement écrasées |
| path param `user-id` | `function op(..., string $user-id)` — **erreur de parsing PHP** |
| `operationId: 2fa` | `function 2fa()` — **erreur de parsing PHP** |

Les conversions sont désormais regroupées dans une classe `Naming` unique, et deux familles de
contrôles s'appuient dessus.

**Collisions** — quatre portées sont gardées : noms d'agrégats, noms de classes de modèles, registre
des dénormaliseurs, et, en filet sous l'ensemble, le chemin de chaque fichier sur le point d'être
écrit. Chaque message nomme les deux coupables :

```
[ERROR] Operations 'getUser' and 'get_user' both map to the 'GetUser' aggregate.
[ERROR] Schemas 'User' and 'user' both map to the 'User' model.
[ERROR] Two generated files would be written to 'src/Format/DateTime.php'.
```

**Validité** — un nom qui produirait un identifiant PHP invalide est refusé, avec sa localisation
dans la spécification : `operationId`, nom de schéma, nom de format, nom de bundle, et nom de path
parameter. Ce dernier est le seul nom utilisé tel quel par PHP, le routeur l'injectant dans le
contrôleur par son nom, ce qui interdit de le normaliser.

Le contrôle porte uniquement sur ce qui empêcherait le code de compiler. **Les noms comportant des
tirets restent acceptés partout ailleurs** — `X-Api-Key` en en-tête continue de fonctionner — et
l'unicode aussi : `café` donne `$qCafé`, que PHP accepte.

#### Nettoyage du code mort

- Les huit lecteurs `get{String,Int,Float,Bool}{,OrNull}RequestBody` ne sont plus générés. Ils
  étaient inatteignables : `ActionRequestBody` impose un objet au premier niveau d'un request body,
  donc aucun appel n'a jamais pu être émis vers eux. L'`AbstractController` généré passe de 851 à
  710 lignes.
- `Type::getRequestBodyPayloadTypeCheckingAst()` est retirée de l'interface et de ses six
  implémentations : elle n'était appelée que par sa propre récursion dans `ArrayType`, sans aucun
  point d'entrée.

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

#### L'enveloppe d'erreur devient une liste plate, avec un code par erreur

`errors` était un objet dont les clés étaient les emplacements, et dont la valeur changeait de type
selon la cause de l'échec. `errors.requestBody` valait une **liste** après un échec de
dénormalisation et une **map** après un échec de validation, si bien qu'aucun client ne pouvait être
écrit sans renifler le type. Par ailleurs l'emplacement exact d'une erreur imbriquée était encodé de
trois façons : dans la clé pour la validation du body, et dans la prose du message partout ailleurs.

`errors` est désormais **une liste plate d'objets**, de forme unique quelle que soit l'origine :

```json
{
  "code": "validation_failed",
  "message": "Validation has failed.",
  "errors": [
    {"in": "query", "path": "queryParamAbcList[0].def", "code": "required", "message": "This value is required."},
    {"in": "query", "path": "queryParamObject.nestedObjectProperty.emailProperty", "code": "invalid_format", "message": "This value is not a valid email address."},
    {"in": "requestBody", "path": "integerMatrixProperty[0][0]", "code": "invalid_type", "message": "This value should be of type integer."}
  ]
}
```

- **`in`** vaut `path`, `query`, `header`, `cookie` ou `requestBody`.
- **`path`** localise la valeur fautive dans une **syntaxe unique**, celle des property paths de
  Symfony : un point avant une propriété, des crochets autour d'un index. Elle s'applique aussi bien
  au corps qu'aux paramètres, y compris ceux envoyés en notation à crochets — `?point[x]=1` est donc
  rapporté en `point.x`. La chaîne est vide pour une erreur portant sur le corps entier.
- **`code`** est exploitable par machine : `required`, `invalid_type`, `invalid_format`,
  `invalid_enum_value`, `invalid_length`, `out_of_range`, `invalid_count`, `invalid_multiple`,
  `invalid_pattern`, `duplicate_values`, `invalid_json`, et `invalid_value` par défaut.
- **`message`** reste une phrase en anglais, désormais autonome : elle ne répète plus l'emplacement,
  qui est dans `path`.

Côté code généré, `ParameterValidationException` et `RequestBodyValidationException` fusionnent en
une seule `ValidationException` portant `list<array{path, code, message}>`, et
`DenormalizationException` porte `$path` et `$errorCode` — `$code` étant déjà pris par `\Exception`.
Les méthodes `validateParameter` et `validateRequestBody` sont remplacées par une unique `validate`.

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