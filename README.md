# Picture Service

The Picture Service provides interfaces for creating and rendering a picture for PHP applications. It comes with a default implementation but you may create specific implementation to fit your requirements.

## Table of Contents

- [Getting started](#getting-started)
    - [Requirements](#requirements)
    - [Highlights](#highlights)
- [Documentation](#documentation)
    - [Picture Creator](#picture-creator)
        - [Create Picture Creator](#create-picture-creator)
        - [Picture Creating](#picture-creating)
        - [Created Picture](#created-picture)
    - [Definition](#definition)
        - [Array Definition](#array-definition)
        - [Picture Definition](#picture-definition)
    - [Definitions](#definitions)
        - [Default Definitions](#default-definitions)
        - [Json Files Definitions](#json-files-definitions)
        - [Stack Definitions](#stack-definitions)
    - [Picture](#picture)
        - [Img](#img)
        - [Sources](#sources)
        - [Source](#source)
        - [Srcset](#srcset)
        - [Src](#src)
    - [Picture Factory](#picture-factory)
    - [Picture Tag Factory](#picture-tag-factory)
    - [Picture Tag](#picture-tag)
    - [Null Picture Tag](#null-picture-tag)
- [Credits](#credits)
___

# Getting started

Add the latest version of the picture service project running this command.

```
composer require tobento/service-picture
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

# Documentation

## Picture Creator

The picture creator creates images based on the given [definition](#definition) and returns a [Created Picture](#created-picture) holding the images to be stored wherever you like.

### Create Picture Creator

The picture creator uses the [Imager Service](https://github.com/tobento-ch/service-imager) to create images.

```php
use Tobento\Service\Imager\InterventionImage\ImagerFactory;
use Tobento\Service\Picture\PictureCreator;

$pictureCreator = new PictureCreator(
    imager: (new ImagerFactory())->createImager(),
);
```

**In Detail**

```php
use Psr\Log\LoggerInterface;
use Tobento\Service\Imager\Action;
use Tobento\Service\Imager\InterventionImage\ImagerFactory;
use Tobento\Service\Picture\Exception\ResourceSizeException;
use Tobento\Service\Picture\PictureCreator;
use Tobento\Service\Picture\PictureCreatorInterface;

$pictureCreator = new PictureCreator(
    imager: (new ImagerFactory())->createImager(),

    // You may define imager actions which are allowed only.
    // If empty array all are allowed if not in disallowedActions.
    allowedActions: [
        Action\Greyscale::class,
    ],
    
    // You may define imager actions which are not allowed and will be skipped:
    disallowedActions: [
        Action\Colorize::class,
    ],
    
    // You may adjust the supported mime types:
    supportedMimeTypes: [
        'image/png', 'image/jpeg', 'image/gif', 'image/webp',
    ],
    
    // You may set an upsize limit for resizing images:
    upsize: 1.2, // default is null (unlimited)
    
    // You may skip smaller sized images (except img src)
    // from generating at all to prevent unsharp images.
    skipSmallerSizedSrc: true, // false is default
    
    // You may verify sizes. If one of the defined image size
    // is too small, a ResourceSizeException will be thrown.
    verifySizes: true, // false is default
    
    // You may pass a logger for debugging:
    logger: null, // LoggerInterface
);

var_dump($pictureCreator instanceof PictureCreatorInterface);
// bool(true)
```

### Picture Creating

**Create Picture From Stream**

Use the ```createFromStream``` method to create a picture from a PSR-7 stream:

```php
use Psr\Http\Message\StreamInterface;
use Tobento\Service\Picture\CreatedPictureInterface;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\PictureInterface;

$createdPicture = $pictureCreator->createFromStream(
    stream: $stream, // StreamInterface
    definition: $definition, // DefinitionInterface
);

var_dump($createdPicture instanceof CreatedPictureInterface);
// bool(true)

var_dump($createdPicture instanceof PictureInterface);
// bool(true)
```

Check out the [Definition](#definition) section to learn more about definitions.

You may check out the [Picture](#picture) section to learn more about the default ```PictureInterface::class``` implementation.

**Create Picture From Resource**

Use the ```createFromResource``` method to create a picture from any resource implementing the ```ResourceInterface``` interface:

```php
use Tobento\Service\Imager\ResourceInterface;
use Tobento\Service\Picture\CreatedPictureInterface;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\PictureInterface;

$createdPicture = $pictureCreator->createFromResource(
    resource: $resource, // ResourceInterface
    definition: $definition, // DefinitionInterface
);

var_dump($createdPicture instanceof CreatedPictureInterface);
// bool(true)

var_dump($createdPicture instanceof PictureInterface);
// bool(true)
```

Check out the [Imager Service - Resource](https://github.com/tobento-ch/service-imager#resource) section to learn more about it.

Check out the [Definition](#definition) section to learn more about definitions.

You may check out the [Picture](#picture) section to learn more about the default ```PictureInterface::class``` implementation.

**Supported Resources**

- [Base64 Resource](https://github.com/tobento-ch/service-imager#base64-resource)
- [Binary Resource](https://github.com/tobento-ch/service-imager#binary-resource)
- [File Resource](https://github.com/tobento-ch/service-imager#file-resource)
- [Stream Resource](https://github.com/tobento-ch/service-imager#stream-resource)

### Created Picture

The created picture holds all created images which you can use to store the images in the way you need it.

```php
use Tobento\Service\Imager\Response\Encoded;
use Tobento\Service\Picture\CreatedPictureInterface;
use Tobento\Service\Picture\PictureInterface;

var_dump($createdPicture instanceof CreatedPictureInterface);
// bool(true)

var_dump($createdPicture instanceof PictureInterface);
// bool(true)

foreach($createdPicture->srces() as $src) {
    var_dump($src instanceof SrcInterface);
    // bool(true)
    
    // The encoded image which was created:
    var_dump($src->encoded() instanceof Encoded);
    // bool(true)
}
```

Check out the [Encoded Response](https://github.com/tobento-ch/service-imager#encoded-response) to learn more about it.

### Definition

#### Array Definition

The array definition is the default picture definition.

```php
use Tobento\Service\Picture\Definition\ArrayDefinition;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\PictureInterface;
use Tobento\Service\Picture\Src;

$definition = new ArrayDefinition(name: 'product:main', definition: [
    'img' => [
        'src' => [600, 600], // [width, height]
        // or width only, height will be resized keeping ratio
        'src' => [600],
        // or height only, width will be resized keeping ratio
        'src' => [null, 600],
        // or
        'src' => [null, null], // keeps resource dimensions.
        // or using Src class
        'src' => new Src(
            width: 600, // or null
            height: 600, // or null
            // you may set a mime type to convert to:
            mimeType: 'image/webp',
            // you may define quality and actions:
            options: ['quality' => 90, 'actions' => ['crop' => [300, 200, 10, 15]]]
        ),
        // img attributes:
        'alt' => 'Alternative Text',
        'class' => 'foo',
        'data-foo' => 'foo',
        'sizes' => '(max-width: 600px) 480px, 800px',
        'loading' => 'lazy',
        'srcset' => [
            '480w' => [480], // same definition as img src
            '800w' => [800],
        ],
    ],
    // You may define any sources:
    'sources' => [
        [
            'media' => '(min-width: 800px)',
            'srcset' => [
                '' => [1200, 600],
            ],
            'type' => 'image/webp',
        ],
        [
            'media' => '(max-width: 600px)',
            'srcset' => [
                '' => [600, 600],
            ],
            'type' => 'image/webp',
            'width' => '600',
            'height' => '600',
        ],
    ],
    // Attributes for the picture tag:
    'attributes' => [
        'class' => 'foo',
    ],
    // Options:
    'options' => [
        // You may define a global quality if not specified individually with the Src class:
        'quality' => [
            'image/jpeg' => 70,
            'image/webp' => 70,
        ],
        // You may convert all png to jpeg types if not defined individually:
        'convert' => ['image/png' => 'image/jpeg'],
        // You may define imager actions used if not specified individually with the Src class:
        'actions' => [
            'greyscale' => [],
            'gamma' => ['gamma' => 5.5],
        ],
    ],
]);

var_dump($definition instanceof DefinitionInterface);
// bool(true)

// Definition to picture:
var_dump($definition->toPicture() instanceof PictureInterface);
// bool(true)
```

**Example with pixel density descriptors:**

```php
use Tobento\Service\Picture\Definition\ArrayDefinition;

$definition = new ArrayDefinition(name: 'logo', definition: [
    'img' => [
        'src' => [250],
        'srcset' => [
            '' => [250],
            '2x' => [500],
        ],
    ],
]);
```

#### Picture Definition

```php
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\Definition\PictureDefinition;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\PictureInterface;
use Tobento\Service\Picture\Source;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\Srcset;

$definition = new PictureDefinition(
    name: 'product',
    picture: new Picture(
        img: new Img(
            src: new Src(
                width: 600, // or null
                height: 600, // or null
                // you may set a mime type to convert to:
                mimeType: 'image/webp',
                // you may define quality and actions:
                options: ['quality' => 90, 'actions' => ['crop' => [300, 200, 10, 15]]],
            ),
            // you may define a srcset:
            srcset: new Srcset(
                new Src(width: 480, descriptor: '480w'),
                new Src(width: 800, descriptor: '800w'),
            ),
            attributes: [
                'sizes' => '(max-width: 600px) 480px, 800px',
                'loading' => 'lazy',
                'class' => 'foo',
            ],
        ),
        sources: new Sources(
            new Source(
                srcset: new Srcset(
                    new Src(width: 1200, height: 600),
                ),
                attributes: [
                    'media' => '(min-width: 800px)',
                    'type' => 'image/webp',
                ],
            ),
            new Source(
                srcset: new Srcset(
                    new Src(width: 600, height: 600),
                ),
                attributes: [
                    'media' => '(max-width: 600px)',
                    'type' => 'image/webp',
                ],
            ),
        ),
        attributes: [
            'class' => 'foo',
        ],
        options: [
            // You may define a global quality if not specified individually with the Src class:
            'quality' => [
                'image/jpeg' => 70,
                'image/webp' => 70,
            ],
            // You may convert all png to jpeg types if not defined individually:
            'convert' => ['image/png' => 'image/jpeg'],
            // You may define imager actions used if not specified individually with the Src class:
            'actions' => [
                'greyscale' => [],
                'gamma' => ['gamma' => 5.5],
            ],
        ],
    )
);

var_dump($definition instanceof DefinitionInterface);
// bool(true)

// Definition to picture:
var_dump($definition->toPicture() instanceof PictureInterface);
// bool(true)
```

### Definitions

You may use the following definitions classes to add, filter and get definitions from.

#### Default Definitions

```php
use Tobento\Service\Picture\Definition\ArrayDefinition;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\Definitions\Definitions;
use Tobento\Service\Picture\DefinitionsInterface;

$definitions = new Definitions(
    'product', // a definitions name
    new ArrayDefinition('product-main', ['img' => ['src' => [50]]]),
    // ... DefinitionInterface
);

var_dump($definitions instanceof DefinitionsInterface);
// bool(true)
```

#### Json Files Definitions

The ```JsonFilesDefinitions``` class loads definitions from JSON files within the given directories.

```php
use Tobento\Service\Dir\Dir;
use Tobento\Service\Dir\Dirs;
use Tobento\Service\Picture\Definition\ArrayDefinition;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\Definitions\JsonFilesDefinitions;
use Tobento\Service\Picture\DefinitionsInterface;

$definitions = new JsonFilesDefinitions(
    name: 'product', // a definitions name
    dirs: new Dirs(new Dir('dir/private/picture-definitions/'))
);

var_dump($definitions instanceof DefinitionsInterface);
// bool(true)
```

**Example Directory:**

```
private/
    picture-definitions/
        product.json
        ...
```

**Example Json Definition:**

```
{
    "img": {
        "src": {
            "width": 300,
            "height": null,
            "descriptor": null,
            "mimeType": "image/png",
            "url": null,
            "path": null,
            "options": []
        },
        "srcset": [
            {
                "width": 200
            }
        ],
        "attributes": {
            "key": "value"
        }
    },
    "sources": [
        {
            "srcset": [
                {
                    "width": 200,
                    "height": null,
                    "descriptor": null,
                    "url": null,
                    "path": null,
                    "options": []
                }
            ],
            "attributes": {
                "key": "value"
            }
        }
    ],
    "attributes": {
        "key": "value"
    },
    "options": {
        "key": "value"
    }
}
```

#### Stack Definitions

```php
use Tobento\Service\Picture\Definition\ArrayDefinition;
use Tobento\Service\Picture\Definitions\Definitions;
use Tobento\Service\Picture\Definitions\StackDefinitions;
use Tobento\Service\Picture\DefinitionsInterface;

$definitions = new StackDefinitions(
    'name', // a definitions name
    new Definitions('product', new ArrayDefinition('product-main', ['img' => ['src' => [50]]])),
    // ... DefinitionsInterface
);

var_dump($definitions instanceof DefinitionsInterface);
// bool(true)
```

### Picture

The picture class may be used to create, store or render the picture.

```php
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\ImgInterface;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\PictureInterface;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\SourcesInterface;
use Tobento\Service\Picture\Src;

$picture = new Picture(
    img: new Img(
        src: new Src(),
    ),
    sources: new Sources(),
    attributes: [],
    options: [],
);

var_dump($picture instanceof PictureInterface);
// bool(true)

// returns the img:
var_dump($picture->img() instanceof ImgInterface);
// bool(true)

// returns a new instance with the given img:
$picture = $picture->withImg(new Img(new Src()));

// returns the sources:
var_dump($picture->sources() instanceof SourcesInterface);
// bool(true)

// returns a new instance with the given sources:
$picture = $picture->withSources(new Sources());

// returns the attributes:
$attributes = $picture->attributes();

// returns a new instance with the given attributes:
$picture = $picture->withAttributes([]);

// returns the options:
$options = $picture->options();

// returns a new instance with the given options:
$picture = $picture->withOptions([]);
```

**Srces**

The ```srces``` method returns a Generator with all collected ```Src``` classes:

```php
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\SrcInterface;

$picture = new Picture(
    img: new Img(
        src: new Src(),
    ),
    sources: new Sources(),
);

foreach($picture->srces() as $src) {
    var_dump($src instanceof SrcInterface);
    // bool(true)
}
```

**toTag**

The ```toTag``` method returns a new created picture tag.

```php
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\PictureTagInterface;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\Src;

$picture = new Picture(
    img: new Img(
        src: new Src(),
    ),
    sources: new Sources(),
);

var_dump($picture->toTag() instanceof PictureTagInterface);
// bool(true)
```

Check out the [Picture Tag](#picture-tag) section to learn more about the default ```PictureTagInterface::class```.

**jsonSerialize**

The ```jsonSerialize``` method serializes the object to a value that can be serialized natively by ```json_encode()```.

```php
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\Src;

$picture = new Picture(
    img: new Img(
        src: new Src(),
    ),
    sources: new Sources(),
);

$jsonSerialized = $picture->jsonSerialize();
```

#### Img

```php
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\ImgInterface;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\SrcInterface;
use Tobento\Service\Picture\Srcset;
use Tobento\Service\Picture\SrcsetInterface;

$img = new Img(
    src: new Src(), // SrcInterface
    srcset: new Srcset(), // null|SrcsetInterface
    attributes: [],
);

var_dump($img instanceof ImgInterface);
// bool(true)

// returns the src:
var_dump($img->src() instanceof SrcInterface);
// bool(true)

// returns a new instance with the given src:
$img = $img->withSrc(new Src());

// returns the srcset or null if none:
var_dump($img->srcset() instanceof SrcsetInterface);
// bool(true)

// returns a new instance with the given srcset:
$img = $img->withSrcset(new Srcset());

// returns the attributes:
$attributes = $img->attributes();

// returns a new instance with the given attributes:
$img = $img->withAttributes([]);

// json serialize:
$jsonSerialized = $img->jsonSerialize();
```

#### Sources

```php
use Tobento\Service\Picture\Source;
use Tobento\Service\Picture\SourceInterface;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\SourcesInterface;
use Tobento\Service\Picture\Srcset;
use Tobento\Service\Picture\SrcInterface;

$sources = new Sources(
    new Source(new Srcset()),
    new Source(new Srcset()),
);

var_dump($sources instanceof SourcesInterface);
// bool(true)

// iterate sources:
foreach($sources as $source) {
    var_dump($source instanceof SourceInterface);
    // bool(true)
}
// or
foreach($sources->all() as $source) {}

// count sources:
var_dump($sources->count());
// int(2)

// returns all collected Src classes:
foreach($sources->srces() as $src) {
    var_dump($src instanceof SrcInterface);
    // bool(true)
}

// json serialize:
$jsonSerialized = $sources->jsonSerialize();
```

#### Source

```php
use Tobento\Service\Picture\Source;
use Tobento\Service\Picture\SourceInterface;
use Tobento\Service\Picture\Srcset;
use Tobento\Service\Picture\SrcsetInterface;

$source = new Source(
    srcset: new Srcset(),
    attributes: []
);

var_dump($source instanceof SourceInterface);
// bool(true)

// returns the srcset or null if none:
var_dump($source->srcset() instanceof SrcsetInterface);
// bool(true)

// returns a new instance with the given srcset:
$source = $source->withSrcset(new Srcset());

// returns the attributes:
$attributes = $source->attributes();

// returns a new instance with the given attributes:
$source = $source->withAttributes([]);

// json serialize:
$jsonSerialized = $source->jsonSerialize();
```

#### Srcset

```php
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\SrcInterface;
use Tobento\Service\Picture\Srcset;
use Tobento\Service\Picture\SrcsetInterface;

$srcset = new Srcset(
    new Src(width: 200, descriptor: '1x'),
    new Src(width: 400, descriptor: '2x'),
);

var_dump($srcset instanceof SrcsetInterface);
// bool(true)

// iterate srces:
foreach($srcset as $src) {
    var_dump($src instanceof SrcInterface);
    // bool(true)
}
// or
foreach($srcset->all() as $src) {}

// count srces:
var_dump($srcset->count());
// int(2)

// json serialize:
$jsonSerialized = $srcset->jsonSerialize();
```

#### Src

```php
use Tobento\Service\Imager\Response\Encoded;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\SrcInterface;

$src = new Src(
    width: 200, // null|int
    height: 200, // null|int
    descriptor: '200w', // null|string, may be used for srcset
    mimeType: 'image/jpeg', // null|string
    url: null, // null|string
    path: null, // null|string
    encoded: null, // null|Encoded
    options: [],
);

var_dump($src instanceof SrcInterface);
// bool(true)

// returns the width or null if none:
var_dump($src->width());
// int(200)

// returns a new instance with the given width:
$src = $src->withWidth(300); // or null

// returns the height or null if none:
var_dump($src->height());
// int(200)

// returns a new instance with the given height:
$src = $src->withHeight(300); // or null

// returns the descriptor or null if none:
var_dump($src->descriptor());
// string(4) "200w"

// returns a new instance with the given descriptor:
$src = $src->withDescriptor('1x'); // or null

// returns the mimeType or null if none:
var_dump($src->mimeType());
// string(10) "image/jpeg"

// returns a new instance with the given mimeType:
$src = $src->withMimeType('image/gif'); // or null

// returns the url or null if none:
var_dump($src->url());
// NULL or string

// returns a new instance with the given url:
$src = $src->withUrl('https://example.com/image.jpg'); // or null

// returns the path or null if none:
var_dump($src->path());
// NULL or string

// returns a new instance with the given path:
$src = $src->withPath('path/image.jpg'); // or null

// returns the encoded or null if none:
var_dump($src->encoded());
// NULL or Encoded

// returns a new instance with the given encoded:
$src = $src->withEncoded(null); // or Encoded

// returns the options:
var_dump($src->options());
// array(0) { }

// returns a new instance with the given options:
$src = $src->withOptions([]);

// json serialize:
$jsonSerialized = $src->jsonSerialize();
```

## Picture Factory

The picture factory may be used to create a picture.

```php
use Tobento\Service\Picture\PictureFactory;
use Tobento\Service\Picture\PictureFactoryInterface;

$factory = new PictureFactory(
    // you may throw exceptions if an error occurs:
    throwOnError: true, // false is default
);

var_dump($factory instanceof PictureFactoryInterface);
// bool(true)
```

**Create From Array**

Use the ```createFromArray``` method to create a picture from the given array:

```php
use Tobento\Service\Picture\PictureFactory;
use Tobento\Service\Picture\PictureInterface;

$picture = (new PictureFactory())->createFromArray([
    'img' => [
        'src' => [
            'width' => 300,
            'height' => null,
            'descriptor' => '2x', // or null
            'mimeType' => 'image/jpeg', // or null
            'url' => null,
            'path' => null,
            'options' => [],
        ],
        'srcset' => [
            ['width' => 200],
        ],
        'attributes' => ['key' => 'value'],
    ],
    'sources' => [
        [
            'srcset' => [
                [
                    'width' => 200,
                    'height' => null,
                    'descriptor' => null,
                    'url' => null,
                    'path' => null,
                    'options' => [],
                ],
            ],
            'attributes' => ['key' => 'value']
        ],
    ],
    'attributes' => ['key' => 'value'],
    'options' => ['key' => 'value'],
]);

var_dump($picture instanceof PictureInterface);
// bool(true)
```

## Picture Tag Factory

The picture tag factory may be used to create picture tags.

```php
use Tobento\Service\Picture\PictureTagFactory;
use Tobento\Service\Picture\PictureTagFactoryInterface;

$factory = new PictureTagFactory();

var_dump($factory instanceof PictureTagFactoryInterface);
// bool(true)
```

**Create From Picture**

Use the ```createFromPicture``` method to create a picture tag from the given picture:

```php
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\PictureTagFactory;
use Tobento\Service\Picture\PictureTagInterface;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\Src;

$picture = new Picture(
    img: new Img(
        src: new Src(url: 'https://example.com/image.jpg'),
    ),
    sources: new Sources(),
);

$pictureTag = (new PictureTagFactory())->createFromPicture($picture);

var_dump($pictureTag instanceof PictureTagInterface);
// bool(true)
```

## Picture Tag

The picture tag renders the picture html tag.

```php
use Tobento\Service\Picture\PictureTag;
use Tobento\Service\Picture\PictureTagInterface;
use Tobento\Service\Tag\Attributes;
use Tobento\Service\Tag\Tag;
use Tobento\Service\Tag\TagInterface;

$picture = new PictureTag(
    new Tag(name: 'picture'),
    new Tag(name: 'img', attributes: new Attributes(['src' => 'image.jpg'])),
    new Tag(name: 'source', attributes: new Attributes(['srcset' => 'image.webp', 'type' => 'image/webp'])),
    new Tag(name: 'source', attributes: new Attributes(['srcset' => 'image.avif', 'type' => 'image/avif'])),
);

var_dump($picture instanceof PictureTagInterface);
// bool(true)

var_dump($picture instanceof \Stringable);
// bool(true)

// returns the "picture" tag:
var_dump($picture->tag() instanceof TagInterface);
// bool(true)

// returns a new instance with the given "picture" tag:
$picture = $picture->withTag(new Tag('picture'));

// returns the "img" tag:
var_dump($picture->img() instanceof TagInterface);
// bool(true)

// returns a new instance with the given "img" tag:
$picture = $picture->withImg(new Tag('img'));

// returns the "source" tags:
foreach($picture->sources() as $source) {
    var_dump($source instanceof TagInterface);
    // bool(true)
}

// returns a new instance with the given "source" tags:
$picture = $picture->withSources(
    new Tag(name: 'source', attributes: new Attributes(['srcset' => 'image.webp', 'type' => 'image/webp'])),
    new Tag(name: 'source', attributes: new Attributes(['srcset' => 'image.avif', 'type' => 'image/avif'])),
);

// returns a new instance with the given "picture" attribute:
$picture = $picture->attr(name: 'data-foo', value: 'Foo');

// returns a new instance with the given "img" attribute:
$picture = $picture->imgAttr(name: 'data-bar', value: 'Bar');
```

You may check out the [Tag Service - Tag Interface](https://github.com/tobento-ch/service-tag#tag-interface) section to learn more about the ```TagInterface::class```.

**Render Picture**

```php
<?= $picture->render() ?>

// or just
<?= $picture ?>
```

## Null Picture Tag

The null picture tag does not render any content at all and may be useful in some sitations.

```php
use Tobento\Service\Picture\PictureTagInterface;
use Tobento\Service\Picture\NullPictureTag;

$picture = new NullPictureTag();

var_dump($picture instanceof PictureTagInterface);
// bool(true)

var_dump((string)$picture);
// string(0) ""
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)