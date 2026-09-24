# CHANGELOG

## 2.0

### 2.0.0

#### Nouveautés

##### Query params de type `array` et `object`

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

##### Sérialisation

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

##### Contraintes

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

#### Ruptures de compatibilité

##### L'enveloppe d'erreur devient une liste plate, avec un code par erreur

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

##### Le composant Serializer de Symfony n'est plus utilisé

Le request body était le dernier consommateur du `Serializer` — deux sites d'appel, dans
`getObjectRequestBody` et `getObjectOrNullRequestBody`. Il est remplacé par du code généré, comme
les query params depuis la 10.1.0. Les réponses n'étaient pas concernées : elles passent par
`JsonResponse`, donc par `json_encode`.

**Le bundle généré passe de dix à cinq dépendances.** Disparaissent `symfony/serializer`,
`symfony/property-info`, `symfony/property-access`, `phpstan/phpdoc-parser` et
`phpdocumentor/type-resolver`.

Motivations, au-delà de l'allègement :

- **La garantie de typage ne dépend plus de la version de Symfony.** Le contrôle des items de
  tableaux annoncé en 10.0.0 (voir plus bas, publiée dans `v1.0.0`) n'était réellement appliqué
  qu'à partir de `symfony/serializer` 8
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

##### Suppression de `DeserializerInterface::denormalize()`

La méthode générée `denormalize()` est supprimée du `DeserializerInterface` et du `Deserializer`.
Elle n'était appelée par rien — seul `AbstractController` consomme l'interface, et uniquement via
`deserialize()` — et elle passait `DISABLE_TYPE_ENFORCEMENT` à **TRUE**, l'inverse exact du contrat
annoncé en 10.0.0 (voir plus bas, publiée dans `v1.0.0`). Elle constituait donc un piège :
l'utiliser réintroduisait silencieusement la conversion de types que cette version avait supprimée.

Retirer une méthode d'une interface ne casse aucune implémentation existante. En revanche, si du
code appelle directement `$deserializer->denormalize(...)`, il faut le remplacer par
`deserialize()` en lui passant du JSON.

Au passage, la propriété interne `$serializer` du `Deserializer` généré n'est plus typée
`SerializerInterface&DenormalizerInterface` mais `SerializerInterface` : l'intersection n'existait
que pour `denormalize()`.

##### Les noms de la spec qui ne peuvent pas devenir un identifiant PHP sont rejetés

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

##### Une propriété dont le `default` vaut `null` est enfin optionnelle dans le DTO

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

#### Corrections

##### `x-apifony-raw` accepte enfin n'importe quelle valeur

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

Enfin l'extension n'était lue que sur les schémas de type `object` : partout ailleurs elle était
silencieusement ignorée. `x-apifony-raw` est désormais un type à part entière, décidé avant la
résolution du type déclaré, et vaut donc pour n'importe quel schéma :

```yaml
# le type déclaré est ignoré, ces trois écritures sont équivalentes
monChamp: {x-apifony-raw: true}                      # forme recommandée
monChamp: {type: 'object', x-apifony-raw: true}
monChamp: {type: 'string', x-apifony-raw: true}      # avant : un simple string
```

Omettre `type` est la forme recommandée, et c'est déjà ce qu'un schéma sans `type` signifie en
OpenAPI 3.1. Trois conséquences :

- **Un raw accepte `null`**, y compris quand la propriété est `required` et que le schéma ne se
  déclare pas nullable. « N'importe quelle valeur » inclut `null` : une déclaration de nullabilité
  sur un raw n'a donc pas d'effet. C'est ce qui corrige au passage la génération de
  `public readonly ?mixed $champ`, que PHP refuse de parser (`Type mixed cannot be marked as
  nullable`).
- **Un raw est le seul type dont la valeur par défaut peut être autre chose que `null` ou `[]`** :
  `default: 'fallback'` sur un paramètre optionnel est rendu tel quel.
- **Un raw n'est plus jamais importé comme un modèle.** Un `$ref` vers un composant raw — comme
  vers un composant de type tableau ou scalaire — n'émet plus de `use …\Model\NomDuComposant`
  pointant vers une classe qui n'existe pas.

Rappel de sémantique côté query string : un raw y reçoit la structure que la notation à crochets a
produite (`?p[a][]=1&p[a][]=2` donne `['a' => ['1', '2']]`), dont les feuilles sont toujours des
chaînes — une query string ne sait exprimer ni entier, ni booléen, ni `null`.

##### Un tableau envoyé à un paramètre scalaire ne court-circuite plus l'enveloppe

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

#### Interne

##### Une passe de résolution entre l'analyse et la génération

`src/OpenApi/` produisait un arbre typé, `src/Bundle/` le consommait, et il n'y avait rien entre les
deux : chaque générateur résolvait lui-même les `$ref`, au moment où il émettait. Le même bloc de six
lignes apparaissait **quatorze fois** dans neuf fichiers, et comme résoudre demande l'objet
`components`, un `?Components` nullable traversait **treize des quarante et une classes** de
`src/Bundle/`.

`src/Resolved/` est l'image de `src/OpenApi/` sans les unions. Un paramètre, un en-tête, un request
body et une réponse portent directement leur cible, parce que rien en aval ne demande jamais s'ils
étaient écrits comme une référence. Un schéma fait exception, et c'est la seule : c'est lui qui nomme
une classe, donc il est enveloppé dans un `SchemaRef` qui porte à la fois la cible et le fait d'avoir
été une référence — les deux ensemble décidant le namespace du modèle, le nom qu'il prend, et si un
fichier est émis pour lui.

Une référence est une feuille pendant la construction du graphe, et n'est liée qu'après, une fois
tous les schémas de `components` construits. La construction ne suit donc jamais une référence et
reste bornée par l'arbre d'analyse, qui est fini et acyclique ; la liaison referme ensuite les
cycles, si bien qu'un schéma récursif se résout sans boucler.

`Reference::getName()` ne prend plus le quatrième segment du pointeur au hasard. Un pointeur vers un
autre document, un pointeur qui continue au-delà de l'entrée (`#/components/schemas/Abc/properties/def`)
ou une racine Swagger 2 (`#/definitions/Abc`) donnaient un nom faux ou une clé indéfinie ; ils sont
maintenant refusés là où ils sont écrits.

##### Les modèles à dénormaliser ne sont plus découverts par effet de bord

Savoir quels modèles avaient besoin d'un dénormaliseur se faisait en **générant les statements puis
en les jetant** : `ActionParameter` appelait `getParameterDenormalizationStmts()` uniquement pour
que `ObjectType` atteigne `registerModel` au passage. La génération de statements était donc
mutante, ce qui forçait la boucle d'émission à être un point fixe — on approchait l'ensemble des
méthodes à émettre au lieu de le connaître.

L'arbre de types répond directement : un type dit quels modèles il dénormalise à profondeur zéro, un
objet dit ce que ses attributs réclament, et le graphe est parcouru explicitement, en largeur, une
génération à la fois.

##### Un seul allocateur pour tous les noms générés

Le docblock de `Naming` disait la propriété que les appelants devaient assumer : la conversion est
lossy, donc des noms distincts peuvent se télescoper, et « c'est aux appelants de détecter ces
collisions ». Six le faisaient, chacun avec sa clé, sa durée de vie, son message et son angle mort ;
l'un d'eux vivait dans la commande plutôt que dans la bibliothèque, si bien qu'un `Bundle::build()`
appelé par programme n'avait aucune garde.

Un nom n'a de sens que dans une portée, donc `NameRegistry` tient une table par portée et tout
identifiant y est réclamé avant usage. Validité et unicité étant deux propriétés de l'entrée dans
une portée, `Naming::assertIdentifier` devient `Naming::isIdentifier` : la classe convertit, le
registre décide. Les six messages deviennent un seul, qui nomme les deux coupables et l'emplacement
du second dans la spécification — là où deux des anciens ne nommaient personne et pointaient sur
`documentation root`.

**Six périmètres n'avaient aucune garde et en ont une.** Le plus grave produisait un bundle cassé
plutôt qu'un fichier manquant : un `operationId` valant `validate` émettait
`validate(Request): Response` sur une classe étendant un `AbstractController` qui déclare
`validate(mixed, string, array): void`, soit une erreur fatale au chargement, sur une spécification
que le générateur déclarait réussie. Les autres : noms de routes et ids de services, qui étaient de
simples affectations de tableau — une seconde entrée écrasait la première et un endpoint
disparaissait du bundle ; noms de propriétés de modèles, dont le garde-fou laissait passer un
chiffre en tête (`$0foo`, erreur de parsing) ; en-têtes de réponse, qui n'avaient ni contrôle
d'unicité ni contrôle de validité.

**Deux bugs attrapés au passage.** `Naming::forClass` préserve la casse interne, donc les schémas
`ABC` et `abc` donnaient deux chaînes distinctes mais une seule classe PHP et un seul fichier sur un
système insensible à la casse : classes, méthodes et fichiers sont désormais comparés repliés. Et
les formats `date-time` et `dateTime` produisent tous deux la classe `DateTime`, ce qui ne remontait
au mieux que comme un conflit de chemin de fichier trois étapes plus loin.

##### Une opération ignorée ne produit plus rien

`x-apifony-ignore` n'était lu que là où les contrôleurs sont construits. Toutes les autres passes
parcouraient l'opération quand même, et la collecte des formats le faisait : un format mentionné par
la seule opération ignorée produisait encore une classe de contrainte, un validateur, une interface
de définition et son câblage de services. L'interface de définition fait partie de la surface du
bundle généré — c'est ce qu'un utilisateur implémente — donc une opération explicitement exclue
ajoutait à ce qu'il avait à regarder. L'opération est maintenant écartée pendant la résolution.

##### Un filet de tests sur le générateur lui-même

La suite ne contenait que des tests fonctionnels HTTP contre un kernel booté, et **aucun
`expectException`** : ni les collisions, ni le nommage, ni la stabilité de la sortie générée
n'étaient couverts. La fixture ne peut d'ailleurs pas porter de collision, puisqu'une collision
avorte la génération avant que la suite ne tourne.

- La sortie générée est assertée fichier par fichier contre le bundle committé, en mémoire. C'est le
  standard que le projet s'appliquait déjà à la main (« regenerates byte for byte ») ; il est
  maintenant vérifié, y compris en CI, qui régénérait sans jamais comparer.
- Le comportement de collision est couvert portée par portée, au registre et de bout en bout.
- La liste des méthodes qu'`AbstractController` réserve est écrite à la main : un test relit le
  fichier émis et asserte que chaque méthode déclarée y figure, pour qu'elle ne puisse pas dériver.

La fixture couvrait par ailleurs un seul bucket `components`, `schemas`, et ses dix-sept `$ref`
pointaient tous dedans. Les quatre autres buckets sont désormais exercés, ce qui met sous oracle
huit sites de résolution qui n'en avaient aucun.

##### Les contraintes déjà garanties ne sont plus émises

Le contrôleur revalidait ce que la dénormalisation venait d'établir. Sur la fixture, **28 des 34
appels à `validate()` ne pouvaient rien détecter** : ils ne portaient que des contraintes dont le
code généré, vérifié par PHPStan au niveau max, garantit déjà le respect.

```php
// avant — les quatre contraintes sont mortes, le dénormaliseur rend un list<'abc'|'def'|'ghi'>
$this->validate($qQueryParamEnumArray, 'queryParamEnumArray', [new Assert\NotNull(),
    new Assert\All(constraints: [new Assert\Type(type: 'string'), new Assert\NotNull(),
        new Assert\Choice(choices: ['abc', 'def', 'ghi'])])]);

// après — plus d'appel du tout
```

Ne sont plus émises, au site d'appel, les contraintes que le dénormaliseur applique déjà :
`NotNull`, `Type`, `Choice`, et les bornes **entières** — leur affinage utilise exactement les mêmes
valeurs. Restent `Count`, `Length`, `Unique`, `Regex`, `DivisibleBy`, les formats, `Valid`, et les
bornes d'un `number`, que rien n'affine. Quand il ne reste rien, l'appel et son `try`/`catch`
disparaissent : **34 appels tombent à 7**.

La distinction se fait selon le chemin de lecture, pas selon le type : un paramètre scalaire passe
par les anciens lecteurs, qui coercent le type mais ignorent enums et bornes, donc seul le `NotNull`
y est retiré.

Les **modèles générés** perdent leur `#[Assert\NotNull]` de premier niveau, que PHP garantit déjà
sur une propriété typée promue non nullable — `Schema` passe de 51 à 24 attributs. Tout le reste est
conservé : `Choice`, les bornes et les `Assert\All(constraints: [...])` sur les items restent utiles
à qui construit un modèle à la main, PHP ne typant pas le contenu d'un tableau.

Conséquence assumée : pour un paramètre de type objet, le `Assert\Valid` du site d'appel fait
toujours descendre dans les attributs du modèle, dont certains restent redondants avec la
dénormalisation. C'est le prix d'un modèle qui décrit son schéma.

##### Nettoyage du code mort

- Les huit lecteurs `get{String,Int,Float,Bool}{,OrNull}RequestBody` ne sont plus générés. Ils
  étaient inatteignables : `ActionRequestBody` impose un objet au premier niveau d'un request body,
  donc aucun appel n'a jamais pu être émis vers eux. L'`AbstractController` généré passe de 851 à
  710 lignes.
- `Type::getRequestBodyPayloadTypeCheckingAst()` est retirée de l'interface et de ses six
  implémentations : elle n'était appelée que par sa propre récursion dans `ArrayType`, sans aucun
  point d'entrée.