<?php

declare(strict_types=1);

namespace PhpDbTest\Sqlite;

use PhpDb\Adapter\Driver\Pdo\Result;
use PhpDb\Adapter\Driver\Pdo\Statement;
use PhpDb\Adapter\Driver\PdoConnectionInterface;
use PhpDb\Adapter\Driver\PdoDriverInterface;
use PhpDb\Adapter\Driver\ResultInterface;
use PhpDb\Adapter\Driver\StatementInterface;
use PhpDb\Adapter\Platform\PlatformInterface;
use PhpDb\Metadata\MetadataInterface;
use PhpDb\Sqlite\AdapterPlatform;
use PhpDb\Sqlite\ConfigProvider;
use PhpDb\Sqlite\Container;
use PhpDb\Sqlite\Metadata;
use PhpDb\Sqlite\Pdo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigProvider::class)]
final class ConfigProviderTest extends TestCase
{
    public const EXPECTED_ALIASES = [
        'SQLite'                      => Pdo\Driver::class,
        'Sqlite'                      => Pdo\Driver::class,
        'sqlite'                      => Pdo\Driver::class,
        'pdo'                         => Pdo\Driver::class,
        'pdo_sqlite'                  => Pdo\Driver::class,
        'pdosqlite'                   => Pdo\Driver::class,
        'pdodriver'                   => Pdo\Driver::class,
        PdoConnectionInterface::class => Pdo\Connection::class,
        PdoDriverInterface::class     => Pdo\Driver::class,
        PlatformInterface::class      => AdapterPlatform::class,
        ResultInterface::class        => Result::class,
        StatementInterface::class     => Statement::class,
        MetadataInterface::class      => Metadata\Source::class,
    ];

    public const EXPECTED_FACTORIES = [
        Pdo\Connection::class  => Container\PdoConnectionFactory::class,
        Pdo\Driver::class      => Container\PdoDriverInterfaceFactory::class,
        Result::class          => Container\PdoResultFactory::class,
        Statement::class       => Container\PdoStatementFactory::class,
        AdapterPlatform::class => Container\PlatformInterfaceFactory::class,
        Metadata\Source::class => Container\MetadataInterfaceFactory::class,
    ];

    private ConfigProvider $configProvider;

    #[Test]
    public function getDependenciesContainsExpectedAliases(): void
    {
        $config = $this->configProvider->getDependencies();
        static::assertEquals(self::EXPECTED_ALIASES, $config['aliases']);
    }

    #[Test]
    public function getDependenciesContainsExpectedFactories(): void
    {
        $config = $this->configProvider->getDependencies();
        static::assertSame(self::EXPECTED_FACTORIES, $config['factories']);
    }

    #[Test]
    public function getDependenciesContainsMetadataAlias(): void
    {
        $dependencies = $this->configProvider->getDependencies();

        static::assertArrayHasKey(MetadataInterface::class, $dependencies['aliases']);
        static::assertSame(
            Metadata\Source::class,
            $dependencies['aliases'][MetadataInterface::class],
        );
    }

    #[Test]
    public function getDependenciesContainsMetadataFactory(): void
    {
        $dependencies = $this->configProvider->getDependencies();

        static::assertArrayHasKey(Metadata\Source::class, $dependencies['factories']);
        static::assertSame(
            Container\MetadataInterfaceFactory::class,
            $dependencies['factories'][Metadata\Source::class],
        );
    }

    #[Test]
    public function getDependenciesReturnsCorrectStructure(): void
    {
        $dependencies = $this->configProvider->getDependencies();

        static::assertNotEmpty($dependencies);
        static::assertArrayHasKey('aliases', $dependencies);
        static::assertArrayHasKey('factories', $dependencies);
    }

    #[Test]
    public function invokeReturnsCorrectStructure(): void
    {
        $config = (new ConfigProvider())();
        static::assertArrayHasKey('dependencies', $config);
        static::assertArrayHasKey('aliases', $config['dependencies']);
        static::assertArrayHasKey('factories', $config['dependencies']);
    }

    #[Test]
    public function invokeReturnsExpectedStructure(): void
    {
        $config = ($this->configProvider)();

        static::assertNotEmpty($config);
        static::assertArrayHasKey('dependencies', $config);
    }

    protected function setUp(): void
    {
        $this->configProvider = new ConfigProvider();
    }
}
