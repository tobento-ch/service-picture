<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Picture\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;
use Tobento\Service\Imager\Action;
use Tobento\Service\Imager\ActionInterface;
use Tobento\Service\Imager\InterventionImage\ImagerFactory;
use Tobento\Service\Imager\Resource;
use Tobento\Service\Picture\Definition\ArrayDefinition;
use Tobento\Service\Picture\Exception\PictureCreateException;
use Tobento\Service\Picture\Exception\ResourceSizeException;
use Tobento\Service\Picture\PictureCreator;
use Tobento\Service\Picture\PictureCreatorInterface;
use Tobento\Service\Picture\Src;

class PictureCreatorTest extends TestCase
{
    protected function createPictureCreator(): PictureCreatorInterface
    {
        return new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
        );
    }
    
    public function testThatImplementsPictureCreatorInterface()
    {
        $this->assertInstanceof(PictureCreatorInterface::class, $this->createPictureCreator());
    }

    public function testCreateFromStreamMethod()
    {
        $createdPicture = $this->createPictureCreator()->createFromStream(
            stream: Psr7\Utils::streamFor(fopen(__DIR__.'/src/image.jpg', 'r')),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
            ]),
        );
        
        $this->assertSame(1, iterator_count($createdPicture->srces()));
        $this->assertNotEmpty($createdPicture->img()->src()->encoded()->encoded());
    }
    
    public function testCreateFromResourceMethodWithFile()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
            ]),
        );
        
        $this->assertSame(1, iterator_count($createdPicture->srces()));
        $this->assertNotEmpty($createdPicture->img()->src()->encoded()->encoded());
    }
    
    public function testCreateFromResourceMethodWithStream()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\Stream(Psr7\Utils::streamFor(fopen(__DIR__.'/src/image.jpg', 'r'))),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
            ]),
        );
        
        $this->assertSame(1, iterator_count($createdPicture->srces()));
        $this->assertNotEmpty($createdPicture->img()->src()->encoded()->encoded());
    }
    
    public function testCreateFromResourceMethodWithBinary()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\Binary(file_get_contents(__DIR__.'/src/image.jpg')),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
            ]),
        );
        
        $this->assertSame(1, iterator_count($createdPicture->srces()));
        $this->assertNotEmpty($createdPicture->img()->src()->encoded()->encoded());
    }
    
    public function testCreateFromResourceMethodWithBase64()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\Base64(base64_encode(file_get_contents(__DIR__.'/src/image.jpg'))),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
            ]),
        );
        
        $this->assertSame(1, iterator_count($createdPicture->srces()));
        $this->assertNotEmpty($createdPicture->img()->src()->encoded()->encoded());
    }

    public function testImgSrcIsCreated()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(50, $encoded->width());
        $this->assertSame(38, $encoded->height());
        $this->assertSame('image/jpeg', $encoded->mimeType());
    }
    
    public function testImgSrcWithWidthOnly()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(50, $encoded->width());
        $this->assertSame(38, $encoded->height());
    }
    
    public function testImgSrcWithHeightOnly()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [null, 50],
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(67, $encoded->width());
        $this->assertSame(50, $encoded->height());
    }
    
    public function testImgSrcWithoutWidthAndHeightUsesResourceSize()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [],
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(200, $encoded->width());
        $this->assertSame(150, $encoded->height());
    }
    
    public function testImgSrcConvertsToMimeTypeIfDefined()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => new Src(
                        width: 50,
                        mimeType: 'image/png',
                    ),
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(50, $encoded->width());
        $this->assertSame(38, $encoded->height());
        $this->assertSame('image/png', $encoded->mimeType());
    }
    
    public function testImgSrcWithDefinedQualityAndActions()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => new Src(
                        width: 50,
                        options: ['quality' => 40, 'actions' => ['greyscale' => []]]
                    ),
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(50, $encoded->width());
        $this->assertSame(38, $encoded->height());
        $this->assertSame('image/jpeg', $encoded->mimeType());
        $this->assertInstanceof(Action\Greyscale::class, $encoded->actions()->all()[0] ?? null);
        $this->assertSame(40, $encoded->actions()->all()[2]?->quality());
    }
    
    public function testImgSrcsetAreCreated()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                    'srcset' => [
                        '30w' => [30],
                        '80w' => [80, 55],
                    ],
                ],
            ]),
        );
        
        $src = $createdPicture->img()->srcset()->all()[0];
        $this->assertNotEmpty($src->encoded()->encoded());
        $this->assertSame(30, $src->encoded()->width());
        $this->assertSame(23, $src->encoded()->height());
        $this->assertSame('image/jpeg', $src->encoded()->mimeType());
        $this->assertSame('30w', $src->descriptor());
        
        $src = $createdPicture->img()->srcset()->all()[1];
        $this->assertNotEmpty($src->encoded()->encoded());
        $this->assertSame(80, $src->encoded()->width());
        $this->assertSame(55, $src->encoded()->height());
        $this->assertSame('image/jpeg', $src->encoded()->mimeType());
        $this->assertSame('80w', $src->descriptor());
    }
    
    public function testImgSrcsetUsingSrc()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                    'srcset' => [
                        '' => new Src(
                            width: 30,
                            mimeType: 'image/png',
                            options: ['actions' => ['greyscale' => []]]
                        ),
                    ],
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->srcset()->all()[0]->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(30, $encoded->width());
        $this->assertSame(23, $encoded->height());
        $this->assertSame('image/png', $encoded->mimeType());
        $this->assertInstanceof(Action\Greyscale::class, $encoded->actions()->all()[0] ?? null);
    }
    
    public function testSourcesAreCreated()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
                'sources' => [
                    [
                        'srcset' => [
                            '' => [80, 60],
                        ],
                        'type' => 'image/png',
                    ],
                    [
                        'srcset' => [
                            '30w' => [30],
                            '80w' => [80, 55],
                        ],
                    ],
                ],
            ]),
        );
        
        $src = $createdPicture->sources()->all()[0]->srcset()->all()[0];
        $this->assertNotEmpty($src->encoded()->encoded());
        $this->assertSame(80, $src->encoded()->width());
        $this->assertSame(60, $src->encoded()->height());
        $this->assertSame('image/png', $src->encoded()->mimeType());
        $this->assertSame('', $src->descriptor());
        
        $src = $createdPicture->sources()->all()[1]->srcset()->all()[0];
        $this->assertNotEmpty($src->encoded()->encoded());
        $this->assertSame(30, $src->encoded()->width());
        $this->assertSame(23, $src->encoded()->height());
        $this->assertSame('image/jpeg', $src->encoded()->mimeType());
        $this->assertSame('30w', $src->descriptor());
        
        $src = $createdPicture->sources()->all()[1]->srcset()->all()[1];
        $this->assertNotEmpty($src->encoded()->encoded());
        $this->assertSame(80, $src->encoded()->width());
        $this->assertSame(55, $src->encoded()->height());
        $this->assertSame('image/jpeg', $src->encoded()->mimeType());
        $this->assertSame('80w', $src->descriptor());
    }
    
    public function testSourcesWithSrc()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
                'sources' => [
                    [
                        'srcset' => [
                            '' => new Src(
                                width: 30,
                                mimeType: 'image/png',
                                options: ['actions' => ['greyscale' => []]]
                            ),
                        ],
                        'type' => 'image/png',
                    ],
                ],
            ]),
        );
        
        $encoded = $createdPicture->sources()->all()[0]->srcset()->all()[0]->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(30, $encoded->width());
        $this->assertSame(23, $encoded->height());
        $this->assertSame('image/png', $encoded->mimeType());
        $this->assertInstanceof(Action\Greyscale::class, $encoded->actions()->all()[0] ?? null);
    }
    
    public function testOptionsQualityIsUsedIfNotSetIndividually()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                    'srcset' => [
                        '' => new Src(
                            width: 30,
                            options: ['quality' => 35]
                        ),
                    ],
                ],
                'options' => [
                    'quality' => [
                        'image/jpeg' => 45,
                    ],
                ],
            ]),
        );

        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertSame(45, $encoded->actions()->all()[1]?->quality());
        
        $encoded = $createdPicture->img()->srcset()->all()[0]->encoded();
        $this->assertSame(35, $encoded->actions()->all()[1]?->quality());
    }
    
    public function testOptionsConvertIsUsedIfNotSetIndividually()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                    'srcset' => [
                        '' => new Src(
                            width: 30,
                            mimeType: 'image/gif',
                        ),
                    ],
                ],
                'options' => [
                    'convert' => ['image/jpeg' => 'image/png'],
                ],
            ]),
        );

        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertSame('image/png', $encoded->mimeType());
        
        $encoded = $createdPicture->img()->srcset()->all()[0]->encoded();
        $this->assertSame('image/gif', $encoded->mimeType());
    }
    
    public function testOptionsActionsAreUsedIfNotSetIndividually()
    {
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                    'srcset' => [
                        '' => new Src(
                            width: 30,
                            options: ['actions' => ['greyscale' => []]]
                        ),
                    ],
                ],
                'options' => [
                    'actions' => [
                        'gamma' => ['gamma' => 5.5],
                    ],
                ],
            ]),
        );

        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertInstanceof(Action\Gamma::class, $encoded->actions()->all()[0] ?? null);
        
        $encoded = $createdPicture->img()->srcset()->all()[0]->encoded();
        $this->assertInstanceof(Action\Greyscale::class, $encoded->actions()->all()[0] ?? null);
    }
    
    public function testThrowsPictureCreateExceptionIfMimeTypeCouldNotBeDetermined()
    {
        $this->expectException(PictureCreateException::class);
        $this->expectExceptionMessage('Unsupported mime type');
        
        $createdPicture = $this->createPictureCreator()->createFromResource(
            resource: new Resource\File('image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
            ]),
        );
    }
    
    public function testAllowedActionsGetsProcessedOnly()
    {
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            allowedActions: [
                Action\Gamma::class,
            ],
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
                'options' => [
                    'actions' => [
                        'gamma' => ['gamma' => 5.5],
                        'greyscale' => [],
                    ],
                ],
            ]),
        );
        
        $actions = $createdPicture->img()->src()->encoded()->actions();
        $this->assertSame(3, count($actions->all()));
        
        $actions = $actions->filter(fn(ActionInterface $action) => $action instanceof Action\Greyscale);
        $this->assertSame(0, count($actions->all()));
    }
    
    public function testAllowedActionsLogsDisallowedActions()
    {
        $logger = new Logger('name');
        $testHandler = new TestHandler();
        $logger->pushHandler($testHandler);
        
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            allowedActions: [
                Action\Gamma::class,
            ],
            logger: $logger,
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
                'options' => [
                    'actions' => [
                        'gamma' => ['gamma' => 5.5],
                        'greyscale' => [],
                    ],
                ],
            ]),
        );
        
        $this->assertTrue($testHandler->hasRecord('Disallowed action greyscale', 'debug'));
    }
    
    public function testDisallowedActionsGetSkipped()
    {
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            disallowedActions: [
                Action\Gamma::class,
            ],
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
                'options' => [
                    'actions' => [
                        'gamma' => ['gamma' => 5.5],
                    ],
                ],
            ]),
        );
        
        $actions = $createdPicture->img()->src()->encoded()->actions();
        $this->assertSame(2, count($actions->all()));
        
        $actions = $actions->filter(fn(ActionInterface $action) => $action instanceof Action\Gamma);
        $this->assertSame(0, count($actions->all()));
    }
    
    public function testDisallowedActionsGetLogged()
    {
        $logger = new Logger('name');
        $testHandler = new TestHandler();
        $logger->pushHandler($testHandler);
        
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            disallowedActions: [
                Action\Gamma::class,
            ],
            logger: $logger,
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [50],
                ],
                'options' => [
                    'actions' => [
                        'gamma' => ['gamma' => 5.5],
                    ],
                ],
            ]),
        );
        
        $this->assertTrue($testHandler->hasRecord('Disallowed action gamma', 'debug'));
    }
    
    public function testUnsupportedMimeTypesFallsbackToDefault()
    {
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            supportedMimeTypes: [
                'image/jpeg', 'image/gif'
            ],
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => new Src(
                        width: 50,
                        mimeType: 'image/png',
                    ),
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame('image/jpeg', $encoded->mimeType());
    }
    
    public function testUpsizeUnlimited()
    {
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            upsize: null,
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [300],
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(300, $encoded->width());
        $this->assertSame(225, $encoded->height());
    }
    
    public function testUpsizeLimited()
    {
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            upsize: 1.0,
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [300],
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(200, $encoded->width());
        $this->assertSame(150, $encoded->height());
    }

    public function testDoesNotSkipSmallerSizedSources()
    {
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            skipSmallerSizedSrc: false,
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [300],
                    'srcset' => [
                        '' => [320],
                    ],
                ],
            ]),
        );
        
        $encoded = $createdPicture->img()->src()->encoded();
        $this->assertNotEmpty($encoded->encoded());
        $this->assertSame(300, $encoded->width());
        $this->assertSame(225, $encoded->height());
        $this->assertSame(2, iterator_count($createdPicture->srces()));
    }
    
    public function testSkipsSmallerSizedSourcesExceptImgSrc()
    {
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            skipSmallerSizedSrc: true,
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [300],
                    'srcset' => [
                        '' => [320],
                    ],
                ],
            ]),
        );
        
        $this->assertSame(1, iterator_count($createdPicture->srces()));
    }
    
    public function testSkippedSmallerSizedSourcesGetsLogged()
    {
        $logger = new Logger('name');
        $testHandler = new TestHandler();
        $logger->pushHandler($testHandler);
        
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            skipSmallerSizedSrc: true,
            logger: $logger,
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [300],
                    'srcset' => [
                        '' => [320],
                    ],
                ],
            ]),
        );
        
        $this->assertTrue($testHandler->hasRecord('Skipped src with width 320 as lower sized', 'debug'));
    }
    
    public function testVerifySizesThrowsResourceSizeExceptionIfResourceIsToSmall()
    {
        $this->expectException(ResourceSizeException::class);
        $this->expectExceptionMessage('Image width to small to create images');
        
        $pictureCreator = new PictureCreator(
            imager: (new ImagerFactory())->createImager(),
            verifySizes: true,
        );
        
        $createdPicture = $pictureCreator->createFromResource(
            resource: new Resource\File(__DIR__.'/src/image.jpg'),
            definition: new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [300],
                ],
            ]),
        );
    }
}