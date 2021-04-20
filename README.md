# Doctrine specifications

⚠️ _This project is in experimental phase, the API may change any time._

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE)

You probably already ended up with cluttered repositories or duplicated criteria, making it difficult to compose or maintain your queries.

But what if your queries were looking like this?
```php
$articles = $repository->find(
    ManyArticle::asEntity()
    ->published()
    ->postedBy($user)
    ->withCategories($categories)
    ->orderedAlphabetically()
    ->paginate($pageNumber, $itemsPerPage)
);
```
If you like it, you probably need this package :)


## Installation
This package requires **PHP 7.4+** and Doctrine **ORM 2.7+**

Add it as Composer dependency:

```sh
$ composer require mediagone/doctrine-specifications
```

## Introduction

The classic _Repository pattern_ (a single class per entity with several methods, one per query) quickly shows its limitations as it grows toward a messy god-class.

Using _[Query Functions](https://ocramius.github.io/doctrine-best-practices/#/90)_ partially solves the problem by splitting up queries into separate classes, but you might still get a lot of code duplication. Things get worse if query criteria can be combined arbitrarily, which may result in the creation of an exponential number of classes.

The _[Specifications pattern](https://en.wikipedia.org/wiki/Specification_pattern)_ comes to the rescue helping you to split them into explicit and reusable filters, improving useability and testability of your database queries. This package is a customized flavor of this pattern, inspired by Benjamin Eberlei's [article](https://beberlei.de/2013/03/04/doctrine_repositories.html). It revolves around a simple concept: specifications. Each specification defines a set of criteria that will be automatically applied to Doctrine's QueryBuilder and Query objects.
```php
abstract class Specification
{
    public function modifyBuilder(QueryBuilder $builder) : void { }
    public function modifyQuery(Query $query) : void { }
}
```

Specifications can be chained to build complex queries, but are **easily tested and maintained separately**.


## Example of usage

We'll learn together how to create the following query:
```php
$articles = $repository->find(
    ManyArticle::asEntity()
    ->postedBy($user)
    ->orderedAlphabetically()
    ->maxCount(5)
);
```

Each method splits the query into separate specifications:
- asEntity => `SelectArticleEntity` specification
- postedBy => `FilterArticlePostedBy` specification
- orderedAlphabetically => `OrderArticleAlphabetically` specification
- maxCount => `LimitMaxCount` specification

### SpecificationCollection class
First, we need to create our main class that will be updated later in our example. It extends `SpecificationCollection` which provides a simple specification registration mechanism, we'll see that in details right after.

```php
namespace App\Blog\Query\Article; // Example namespace, choose what fits best to your project
use Mediagone\Doctrine\Specifications\SpecificationCollection;

final class ManyArticle extends SpecificationCollection
{
    
}
 ```


### SelectArticleEntity specification
Our first specification defines the selected entity in our query builder by overloading the `modifyBuilder` method:
```php
namespace App\Blog\Query\Article\Specifications; // Example namespace
use App\Blog\Article; // Assumed FQCN of your entity
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
Let's register it in our specification collection:
```php
...
use App\Blog\Query\Article\Specifications\SelectArticleEntity; // Previously chosen namespace
use Mediagone\Doctrine\Specifications\SpecificationRepositoryResult;

final class ManyArticle extends SpecificationCollection
{
    public static function asEntity() : self
    {
        return new self(new SelectArticleEntity(), SpecificationRepositoryResult::MANY_OBJECTS);
    }
}
```
_Notes:_ 
- Each SpecificationCollection must be initialized with an _initial specification_ and a _repository result format_, both being closely related.
- The SpecificationCollection's constructor is protected to enforce the usage of [static factory methods](https://medium.com/javarevisited/static-factory-methods-an-alternative-to-public-constructors-73cbe8b9fda), because descriptive naming is more meaningful about what the specifications will return.



### Filtering specifications
Our second specification will filter articles by author:
```php
final class FilterArticlePostedBy extends Specification
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->addWhere('article.authorId = :userId');
        $builder->setParameter('userId', $this->user->getId());
    }
}
```
Again, add it in the collection but this time using a fluent instance method:
```php
final class ManyArticle extends SpecificationCollection
{
    // ...
    
    public function postedBy(User $user) : self
    {
        $this->addSpecification(new FilterArticlePostedBy($user));
        return $this;
    }
}
```

Now we can do exactly the same for our two last filters: `orderedAlphabetically` and `maxCount`.

```php
final class OrderArticleAlphabetically extends Specification
{
    public function modifyBuilder(QueryBuilder $builder) : void
    {
        $builder->addOrderBy('article.title', 'ASC');
    }
}
```

Notice that we can also modify the Doctrine query as well through `modifyQuery`:
```php
use Doctrine\ORM\Query;

final class LimitMaxCount extends Specification
{
    private int $count;

    public function __construct(int $count)
    {
        if ($count <= 0) {
            throw new InvalidArgumentException('Count must be a positive integer.');
        }
        
        $this->count = $count;
    }
    
    public function modifyQuery(Query $query) : void
    {
        $query->setMaxResults($this->count);
    }
}
```
Don't forget to register them in the collection:
```php
final class ManyArticle extends SpecificationCollection
{
    // ...
    
    public function orderedAlphabetically() : self
    {
        $this->addSpecification(new OrderArticleAlphabetically());
        return $this;
    }
    
    public function maxCount(int $count) : self
    {
        $this->addSpecification(new LimitMaxCount($count));
        return $this;
    }
}
```


### Execute the query

Finally, we can easily retrieve results according to our specification collection, by using the `SpecificationRepository` class (which fully replaces traditional Doctrine repositories):
```php
use Mediagone\Doctrine\Specifications\SpecificationRepository;

$repository = new SpecificationRepository($doctrineEntityManager);

$articles = $repository->find(
    ManyArticle::asEntity()
    ->postedBy($user)
    ->orderedAlphabetically()
    ->maxCount(5)
);
```

_Notes:_ 
- Use _Dependency Injection_ to instantiate the `DoctrineSpecificationRepository` when possible.
- You can also use this service as base to implement your own (eg. bus middlewares).


## Extended usage

Naming convention used in this exemple is only a suggestion, feel free to adapt to your needs or preferences.


### Return types

The package allows results to get retrieved in different formats:
- MANY_OBJECTS : returns an **array of hydrated objects** (similar to QueryBuilder `getResult()`)
- SINGLE_OBJECT : returns a **single hydrated object** or **null** (similar to `getOneOrNullResult()`)
- SINGLE_SCALAR : returns a **single scalar** (similar to `getSingleScalarResult()`)

Thereby, you can use the same specifications for different result types (count, DTOs...) by adding multiple _static factory methods_ in a collection.
```php
final class ManyArticle extends SpecificationCollection
{
    public static function asEntity() : self
    {
        return new self(new SelectArticleEntity(), SpecificationRepositoryResult::MANY_OBJECTS);
    }

    public static function asCount() : self
    {
        return new self(new SelectArticleEntity(), SpecificationRepositoryResult::SINGLE_SCALAR);
    }
}
```
Exemple of usage:
```php
$totalArticleCount = $repository->find(
    ManyArticle::asCount() // retrieve the count instead of entities
    ->postedBy($user)
    ->inCategory($category)
);
```

_Note:_

- You'll probably want to create a separate collection for querying single article (eg. `OneArticle`) since specification filters are usually not the same for single or array results. Shared filters can be easily added to both collections.


### Debugging

The `SpecificationCollection` class comes with built-in methods that adds debug oriented specifications to the collection.
- DebugDumpDQL
- DebugDumpSQL

So you can easily dump the generated DQL and SQL by adding some method calls:

```php
$articles = $repository->find(
    ManyArticle::asEntity()
    ->published()
    ->postedBy($user)
    
    ->dumpDQL() //  <--- equivalent of   dump($query->getDQL());
    ->dumpSQL() //  <--- equivalent of   dump($query->getSQL());
);
```

### Generic specifications

The library comes with some other built-in generic specifications:
- LimitResultsMaxCount
- LimitResultsOffset
- LimitResultsPaginate

Exemple of usage for pagination:
```php
$pageNumber = 2;
$articlesPerPage = 10;

$articles = $repository->find(
    ManyArticle::asEntity()
    ->postedBy($user)
    ->inCategory($category)

    // Add results specifications separately (LimitResultsMaxCount and LimitResultsOffset)
    ->maxResult($articlesPerPage)
    ->resultOffset(($pageNumber - 1) * $articlesPerPage)
    
    // Or use the pagination specification (LimitResultsPaginate)
    ->paginate($pageNumber, $articlesPerPage)
);
```

### Command bus

Specification queries are best used through a _Query bus_, that suits very well with DDD, however it's not a hard requirement. You can easily tweak your own adapter for any bus or another kind of service.

Your query classes might extend `SpecificationCollection`, making them automatically handleable by a dedicated bus middleware.

If you're looking for a bus package (or just want to see how it's done), you can use [mediagone/cqrs-bus](https://github.com/Mediagone/cqrs-bus) which proposes a `SpecificationQuery` base class and the ` SpecificationQueryFetcher` middleware.



## License

_Doctrine Specifications_ is licensed under MIT license. See LICENSE file.



[ico-version]: https://img.shields.io/packagist/v/mediagone/doctrine-specifications.svg
[ico-downloads]: https://img.shields.io/packagist/dt/mediagone/doctrine-specifications.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg

[link-packagist]: https://packagist.org/packages/mediagone/doctrine-specifications
[link-downloads]: https://packagist.org/packages/mediagone/doctrine-specifications
