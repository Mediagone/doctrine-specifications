# Doctrine specifications

⚠️ _This project is in experimental phase, the API may change any time._

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE)

You probably already ended up with cluttered repositories or duplicated criteria, making it difficult to compose or maintain your queries.

But what if your queries were looking like this?
```php
// Find all articles written by a given user
$articles = $repository->find(
    ManyArticles::asEntity()
        ->postedByUser($userId)
);
```
Or also:
```php
// Find all published articles in a given category
$articles = $repository->find(
    ManyArticles::asEntity()
    ->published()
    ->inCategory($categoryId)
    ->orderedByDateDesc()
    ->paginate($pageNumber, $itemsPerPage)
);
```
Combinations of criteria are unlimited, without any code duplication! \
If you like it, you probably need this package ;)


## Summary

1. [Examples](#examples)
2. [Extended usages](#extended)
    1. [Return formats](#formats)
    2. [Joins](#joins)
    3. [Read models](#readmodels)
    4. [Using multiple Entity Managers](#multipleem)
    5. [Command bus](#commandbus)
3. [Generic specifications](#generic)
    1. [Select specifications](#spec-select)
    2. [Filter specifications](#spec-filter)
    3. [Additional specifications](#spec-more)
    4. [Debug specifications](#spec-debug)
4. [Organizing specifications](#organize)

## Installation
This package requires **PHP 7.4+** and Doctrine **ORM 2.7+**

Add it as Composer dependency:

```sh
$ composer require mediagone/doctrine-specifications
```

## Introduction

The classic _Repository pattern_ (a single class per entity with several methods, one per query) quickly shows its limitations as it grows toward a messy god-class.

Using _[Query Functions](https://ocramius.github.io/doctrine-best-practices/#/90)_ partially solves the problem by splitting up queries into separate classes, but you might still get a lot of code duplication. Things get worse if query criteria can be combined arbitrarily, which may result in the creation of an exponential number of classes. \
The _[Specifications pattern](https://en.wikipedia.org/wiki/Specification_pattern)_ comes to the rescue helping you to split them into explicit and reusable filters, improving useability and testability of your database queries. This package is a customized flavor of this pattern, inspired by Benjamin Eberlei's [article](https://beberlei.de/2013/03/04/doctrine_repositories.html). It revolves around a simple concept: specifications. \
Each specification defines a set of criteria that will be automatically applied to Doctrine's QueryBuilder and Query objects, with the help of two methods:
```php
abstract class Specification
{
    public function modifyBuilder(QueryBuilder $builder) : void { }
    public function modifyQuery(Query $query) : void { }
}
```

Specifications can be combined to build complex queries, while remaining **easily testable and maintainable** separately.


## <a name="examples"></a>Examples

We'll learn together how to create the following query:
```php
$articles = $repository->find(
    ManyArticles::asEntity()
        ->postedByUser($userId)
        ->orderedAlphabetically()
        ->maxCount(5)
);
```

Each method splits the query into separate "specifications":
- _asEntity_ => `SelectArticleEntity` specification
- _postedByUser_ => `FilterArticlePostedBy` specification
- _orderedAlphabetically_ => `OrderArticleAlphabetically` specification
- _maxCount_ => `LimitMaxCount` specification


### SpecificationCompound class

First, we need to create our main class that will be updated later in our example. It extends `SpecificationCompound` which provides a simple specification registration mechanism, we'll see that in details right after.

We'll use a static factory method `asEntity()` to build our query object and define its return type. Here we want to get back results as _entities_, but we could hydrate instead a _read model_ (DTO) (eg. `asModel()`) or return a scalar value (eg. `asCount()`).  

```php
namespace App\Blog\Article\Query; // Example namespace, choose what fits best to your project

use Mediagone\Doctrine\Specifications\SpecificationCompound;

final class ManyArticles extends SpecificationCompound
{
    public static function asEntity() : self
    {
        return new self(
            SpecificationRepositoryResult::MANY_OBJECTS,
            // Put select specifications here (one or more)
        );
    }
    
    // We'll add more specification methods here later
}
 ```

_Notes:_
- Each `SpecificationCompound` must be initialized with a _result format_ and (at least) one _initial specification_.
- The compound's constructor is protected to enforce the usage of [static factory methods](https://medium.com/javarevisited/static-factory-methods-an-alternative-to-public-constructors-73cbe8b9fda), since descriptive naming is more meaningful about what the query will return.
- You may want to create another compound named `OneArticle` for queries that will always return a single result.

### SelectArticleEntity specification

Our first specification defines the selected entity in our query builder by overloading the `modifyBuilder` method:
```php
namespace App\Blog\Article\Query\Specifications; // Example namespace

use App\Blog\Article; // assumed FQCN of your entity
use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\Specification;

final class SelectArticleEntity extends Specification
{
    public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->from(Article::class, 'article');
        $builder->select('article');
    }
}
```
Let's register it in our specification compound:
```php
...
use App\Blog\Article\Query\Specifications\SelectArticleEntity;
use Mediagone\Doctrine\Specifications\SpecificationRepositoryResult;

final class ManyArticles extends SpecificationCompound
{
    public static function asEntity() : self
    {
        return new self(
            SpecificationRepositoryResult::MANY_OBJECTS,
            new SelectArticleEntity()
        );
    }
}
```

> 💡 This is how we create _custom specification_, but having to create a new class for each criterion is really cumbersome. Hopefully, the library provides many generic specifications you can reuse for common usages (see the [Generic specifications](#generic) section below).


So, we can replace our custom specification by the generic one:
```php
use App\Blog\Article;
use App\Blog\Article\Query\Specifications\SelectArticleEntity;
use Mediagone\Doctrine\Specifications\SpecificationRepositoryResult;

final class ManyArticles extends SpecificationCompound
{
    public static function asEntity() : self
    {
        return new self(
            SpecificationRepositoryResult::MANY_OBJECTS,
            SelectEntity::specification(Article::class, 'article'), // from + select
        );
    }
}
```


### Filtering specifications
Our second specification will filter articles by author:
```php
final class FilterArticlePostedByUser extends Specification
{
    private UserId $userId;

    public function __construct(UserId $userId)
    {
        $this->userId = $userId;
    }

    public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->addWhere('article.authorId = :authorId');
        $builder->setParameter('authorId', $this->userId, 'app_userid');
    }
}
```
Add it in the compound but this time using a fluent instance method:
```php
final class ManyArticles extends SpecificationCompound
{
    // ...
    
    public function postedByUser(UserId $userId) : self
    {
        $this->addSpecification(new FilterArticlePostedByUser($userId));
        return $this;
    }
}
```

Again, a generic specification exists, but this time you can use the following helper method to do that without using `addSpecification()` (the method uses it internally):
```php
final class ManyArticles extends SpecificationCompound
{
    // ...
    
    public function postedByUser(UserId $userId) : self
    {
        $this->whereFieldEqual('article.user', 'userId', $userId);
        return $this;
    }
}
```

Now we can do exactly the same for our two last filters: `orderedAlphabetically` and `maxCount`:

```php
final class ManyArticles extends SpecificationCompound
{
    // ...
    
    public function orderedAlphabetically() : self
    {
        // equivalent to: $builder->addOrderBy('article.title', 'ASC');
        $this->orderResultsByAsc('article.title');
        return $this;
    }
    
    public function maxCount(int $count) : self
    {
        // equivalent to: $query->setMaxResults($count);
        $this->limitResultsMaxCount($count);
        return $this;
    }
}
```


### Execute the query

Finally, we can easily retrieve results according to our specification compound, by using the `SpecificationRepository` class (which fully replaces traditional Doctrine repositories):
```php
use Mediagone\Doctrine\Specifications\SpecificationRepository;

$repository = new SpecificationRepository($doctrineEntityManager);

$articles = $repository->find(
    ManyArticles::asEntity()
        ->postedByUser($userId)
        ->orderedAlphabetically()
        ->maxCount(5)
);
```

_Notes:_
- Use _Dependency Injection_ (if available) to instantiate the `DoctrineSpecificationRepository`.
- You can also use this service class as base to implement your own (eg. bus middlewares).


## <a name="extended"></a>Extended usages

### <a name="formats"></a>Return formats

The package allows results to get retrieved in different formats:
- **MANY_OBJECTS** : returns an *array of hydrated objects* (similar to QueryBuilder `getResult()`)
- **SINGLE_OBJECT** : returns a *single hydrated object* or *null* (similar to `getOneOrNullResult()`)
- **SINGLE_SCALAR** : returns a *single scalar* (similar to `getSingleScalarResult()`)

Thereby, you can use the same specifications for different result types by adding multiple _static factory methods_ in a compound.
```php
final class ManyArticles extends SpecificationCompound
{
    public static function asEntity() : self
    {
        return new self(
            SpecificationRepositoryResult::MANY_OBJECTS,
            // Return results as Article instances
            SelectEntity::specification(Article::class, 'article')
        );
    }
    
    public static function asModel() : self
    {
        return new self(
            SpecificationRepositoryResult::MANY_OBJECTS,
            // Return results as ArticleModel instances
            SelectReadModel::specification(Article::class, 'article', ArticleModel::class) 
        );
    }

    public static function asCount() : self
    {
        return new self(
            SpecificationRepositoryResult::SINGLE_SCALAR,
            // Return the number of results
            SelectCount::specification(Article::class, 'article')
        );
    }
}
```
Exemple of usage:
```php
$articleCount = $repository->find(
    ManyArticles::asCount() // retrieve the count instead of entities
        ->postedByUser($userId)
        ->inCategory($category)
);
```


### <a name="joins"></a>Joins

You can define query joins very easily by adding them in the static constructor. \
Note that it will be *applied to all your queries*:
```php
final class ManyArticles extends SpecificationCompound
{
    public static function asEntity() : self
    {
        return new self(
            SpecificationRepositoryResult::MANY_OBJECTS,
            SelectEntity::specification(Article::class, 'article')
            JoinLeft::specification('article.category', 'category'),
        );
    }
}
```

If you want to join only when really needed, you can define the join only for a given specification:

```php
final class ManyArticles extends SpecificationCompound
{
    public static function asEntity() : self
    {
        return new self(
            SpecificationRepositoryResult::MANY_OBJECTS,
            SelectEntity::specification(Article::class, 'article')
        );
    }
    
    public static function byCategoryName(string $categoryName) : self
    {
        $this->joinLeft('article.category', 'category');
        $this->whereFieldEqual('category.name', 'cateName', $categoryName);
    }
}
```

Joins using the same alias are only added once:
```php

final class ManyArticles extends SpecificationCompound
{
    public static function asEntity() : self
    {
        return new self(
            SpecificationRepositoryResult::MANY_OBJECTS,
            SelectEntity::specification(Article::class, 'article'),
            JoinLeft::specification('article.category', 'category') // Join declaration
        );
    }
    
    public static function byCategoryName(string $categoryName) : self
    {
        // Ignored, since the join was already declared in the constructor,
        // it would be the same if declared in another method.
        $this->joinLeft('article.category', 'category');
        $this->whereFieldEqual('category.name', 'catName', $categoryName);
    }
    
    public static function byParentCategoryName(string $categoryName) : self
    {
        // Not ignored since it uses a different alias ("pcat").
        $this->joinLeft('category.parent', 'pcat');
        $this->whereFieldEqual('pcat.name', 'pcatName', $categoryName);
    }
}
```


### <a name="readmodels"></a>Read models

Retrieving data through dedicated classes, instead of entities might be very powerful (if we don't need to update the entity), because it speeds up complex queries (it limits the number of hydrated objects) and allow to flatten relations.

Let's take these two basic entities:
```php
#[ORM\Entity]
class Article
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;
    
    #[Column(type: 'string')]
    private string $title;
    
    #[Column(type: 'string')]
    private string $content;
    
    #[ManyToOne(targetEntity: Category::class)]
    private Category $category;
}

#[ORM\Entity]
class Category
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private int $id;
    
    #[Column(type: 'string')]
    private string $name;
}
```

The normal way to get an Article with the name of it's category would be to query the entity and the related Category entity. But it leads to both objects hydration, and potentially multiple queries (depending on the fetch mode used).

That's why Doctrine offers a way to hydrate custom classes by using the **NEW** operator (see [official documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/dql-doctrine-query-language.html#new-operator-syntax)).

Keeping in sync the query's _selected fields_ and the DTO's _constructor's parameters_ might be tedious, that's why we also provides an interface to handle things for you:
```php
final class ArticleModel implements SpecificationReadModel
{
    private string $id;
    private string $title;
    private string $content;
    private string $categoryName;
    
    // Keep field list close to the constructor's definition that uses it.
    public static function getDqlConstructorArguments(): array
    {
        return [
            'article.id',
            'article.title',
            'article.content',
            'category.name',
        ];
    }
    
    public function __construct(
        string $id,
        string $title,
        string $content,
        string $categoryName,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->categoryName = $categoryName;
    }
}
```

Selecting a _Read Model_ in place of an Entity is very straightforward by registering a `SelectReadModel` specification in the factory method:
```php
final class ManyArticles extends SpecificationCompound
{
    public static function asModel() : self
    {
        return new self(
            SpecificationRepositoryResult::MANY_OBJECTS,
            SelectReadModel::specification(Article::class, 'article', ArticleModel::class)
        );
    }
    
    // ...
}
```



### <a name="multipleem"></a>Using multiple Entity Managers
By default, the *default* entity manager is used, but you can specify for each Compound which entity manager to use by overloading the `getEntityManager` method:
```php
final class ManyArticles extends SpecificationCompound
{
    public function getEntityManager(ManagerRegistry $registry) : EntityManager
    {
        return $registry->getManagerForClass(Article::class);
    }
    
}
```
You can also get it by the name used in the ORM configuration:
```php
public function getEntityManager(ManagerRegistry $registry) : EntityManager
{
    return $registry->getManager('secondary');
}
```


### <a name="commandbus"></a>Command bus

Specification queries are best used through a _Query bus_, that suits very well with DDD, however it's not mandatory. You can easily tweak your own adapter for any bus or another kind of service.

Your query classes might extend `SpecificationCompound`, making them automatically handleable by a dedicated bus middleware.

If you're looking for a bus package (or just want to see how it's done), you can use [mediagone/cqrs-bus](https://github.com/Mediagone/cqrs-bus) which proposes a `SpecificationQuery` base class and the ` SpecificationQueryFetcher` middleware.




## <a name="generic"></a>Generic specifications

To remove the hassle of creating custom specifications for most common usages, the library comes with built-in generic specifications. They can be easily registered using the specific compound's protected methods:


### <a name="spec-select"></a>Select specifications

|Specification name|Description|
|---|:---|
|SelectEntity|Select and return the entity as query result.|
|SelectReadModel|Select and return a DTO class as query result.|
|SelectCount|Count and return the number of results of the query.|
|JoinLeft|Declare a Left join.|
|JoinInner|Declare an Inner join.|
|GroupBy|Declare a GroupBy clause.|
|Having|Declare a Hanving clause.|

### <a name="spec-filter"></a>Filter specifications
Specifications usable in criteria methods:

|Compound method name|Specification name|QueryBuilder condition|
|---|---|:---:|
|->whereClause(...)|WhereClause|*custom where clause*|
|->whereFieldDifferent(...)|WhereFieldDifferent|`field != value`|
|->whereFieldEqual(...)|WhereFieldEqual|`field = value`|
|->whereFieldGreater(...)|WhereFieldGreater|`field > value`|
|->whereFieldGreaterOrEqual(...)|WhereFieldGreaterOrEqual|`field >= value`|
|->whereFieldLesser(...)|WhereFieldLesser|`field < value`|
|->whereFieldLesserOrEqual(...)|WhereFieldLesserOrEqual|`field <= value`|
|->whereFieldIn(...)|WhereFieldIn|`field IN (value)`|
|->whereFieldInArray(...)|WhereFieldInArray|`field IN (values,generated,list)`|
|->whereFieldIsNull(...)|WhereFieldIsNull|`field IS NULL`|
|->whereFieldIsNotNull(...)|WhereFieldIsNotNull|`field IS NOT NULL`|
|->whereFieldLike(...)|WhereFieldLike|`field LIKE 'value'`|
|->whereFieldBetween(...)|WhereFieldBetween|`field >= min AND field <= max`|
|->whereFieldBetweenExclusive(...)|WhereFieldBetweenExclusive|`field > min AND field < max`|
||||
|->orderResultsByAsc(...)|OrderResultsByAsc|`ORDER BY expression ASC`|
|->orderResultsByDesc(...)|OrderResultsByDesc|`ORDER BY expression DESC`|

Example of usage:

```php
use Mediagone\Doctrine\Specifications\Universal\WhereFieldEqual;

final class ManyArticles extends SpecificationCompound
{
    // ...
    
    public function postedByUser(UserId $userId) : self
    {
        // the following line
        $this->whereFieldEqual('article.authorId', 'authorId',  $userId, 'app_userid');
        // is equivalent to
        $this->addSpecification(WhereFieldEqual::specification('article.authorId', 'authorId',  $userId, 'app_userid'));
        
        return $this;
    }
}
```


### <a name="spec-more"></a>Additional specifications

|Compound method name|Specification name|Note|
|---|---|:---|
|->setParameter(...)|SetParameter|_Define a query builder parameter._|
|->limitResultsOffset(...)|LimitResultsOffset|(Pagination) _Defines how many results to skip._|
|->limitResultsMaxCount(...)|LimitResultsMaxCount|(Pagination) _Defines the (max) number of returned results._|
|->limitResultsPaginate(...)|LimitResultsPaginate|(Pagination) _Combines `MaxCount` and `Offset` effects, with different parameters._|

Exemple of usage:
```php
$pageNumber = 2;
$articlesPerPage = 10;

$articles = $repository->find(
    ManyArticles::asEntity()
    ->postedByUser($userId)
    ->inCategory($category)

    // Add results specifications separately (LimitResultsMaxCount and LimitResultsOffset)
    ->maxResult($articlesPerPage)
    ->resultOffset(($pageNumber - 1) * $articlesPerPage)
    
    // Or use the pagination specification (LimitResultsPaginate)
    ->paginate($pageNumber, $articlesPerPage)
);
```


A last couple of specifications provide even more flexibility by allowing you to modify the Doctrine QueryBuilder/Query without having to create separate classes:

|Compound method name|Specification name|
|---|---|
|->modifyBuilder(...)|ModifyBuilder|
|->modifyQuery(...)|ModifyQuery|

```php
use Doctrine\ORM\QueryBuilder;
use Mediagone\Doctrine\Specifications\SpecificationCompound;

final class ManyArticles extends SpecificationCompound
{
    // ...
    
    public function postedByOneOfBothUsers(UserId $userId, UserId $userId2) : self
    {
        $this->modifyBuilder(static function(QueryBuilder $builder) use ($userId, $userId2) {
            $builder
                ->andWhere('article.authorId = :authorId OR article.authorId = :authorId2')
                ->setParameter('authorId', $userId)
                ->setParameter('authorId2', $userId2)
            ;
        });
        
        return $this;
    }
}
```


### <a name="spec-debug"></a>Debugging specifications

The `SpecificationCompound` class comes with built-in methods that adds debug oriented specifications to all compound classes, _you don't have to include them in your own compounds_:


|Compound method name|Specification name|
|---|---|
|->dumpDQL(...)|DebugDumpDQL|
|->dumpSQL(...)|DebugDumpSQL|


So you can easily dump the generated DQL and SQL with few method calls:

```php
$articles = $repository->find(
    ManyArticles::asEntity()
    ->published()
    ->postedByUser($userId)
    
    ->dumpDQL() //  <--- equivalent of   dump($query->getDQL());
    ->dumpSQL() //  <--- equivalent of   dump($query->getSQL());
);
```


## <a name="organize"></a>Organizing specifications

### Naming specifications

Naming convention used in this exemple is only a suggestion, feel free to adapt to your needs or preferences.

There is no hard requirement about naming, but you should use defined prefixes to differentiate between your specifications:
- *Filter...* : specifications that filter out results, but allowing _**multiple results**_.
- *Get...* : specifications that filter out results, in order to get _**a unique (or null) result**_.
- *Order...* : specifications that change the results order.
- *Select...* : specifications that define selected result data (entities, DTO, joins, groupBy...)
...

### Files organization
You'll probably want to create a separate compound for querying single article (eg. `OneArticle`) since the specification filters are usually not the same for single or array results (shared specifications can be easily added to both compounds).

Hence a suggested file structure might be:
```
Article
  ├─ Query
  │   ├─ Specifications
  │   │   ├─ FilterArticlePostedBy.php
  │   │   ├─ GetArticleById.php
  │   │   ├─ OrderArticleAlphabetically.php
  │   │   ├─ SelectArticleCount.php
  │   │   ├─ SelectArticleDTO.php
  │   │   └─ SelectArticleEntity.php
  │   │
  │   ├─ ManyArticles.php
  │   └─ OneArticle.php
  │
  ├─ Article.php
  └─ ArticleDTO.php
```


## License

_Doctrine Specifications_ is licensed under MIT license. See LICENSE file.



[ico-version]: https://img.shields.io/packagist/v/mediagone/doctrine-specifications.svg
[ico-downloads]: https://img.shields.io/packagist/dt/mediagone/doctrine-specifications.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg

[link-packagist]: https://packagist.org/packages/mediagone/doctrine-specifications
[link-downloads]: https://packagist.org/packages/mediagone/doctrine-specifications
