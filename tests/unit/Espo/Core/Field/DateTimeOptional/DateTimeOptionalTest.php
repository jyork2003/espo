<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2022 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace tests\unit\Espo\Core\Field\DateTimeOptionalTest;

use Espo\Core\{
    Field\DateTimeOptional,
};

use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;
use DateInterval;

class DateTimeOptionalTest extends \PHPUnit\Framework\TestCase
{
    public function testFromString()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $this->assertEquals('2021-05-01 10:20:30', $value->getString());

        $this->assertFalse($value->isAllDay());
    }

    public function testFromDateTime1()
    {
        $dt = new DateTimeImmutable('2021-05-01 10:20:30', new DateTimeZone('UTC'));

        $value = DateTimeOptional::fromDateTime($dt);

        $this->assertEquals('2021-05-01 10:20:30', $value->getString());
    }

    public function testFromDateTime2()
    {
        $dt = new DateTimeImmutable('2021-05-01 10:20:30', new DateTimeZone('Europe/Kiev'));

        $value = DateTimeOptional::fromDateTime($dt);

        $this->assertEquals('2021-05-01 07:20:30', $value->getString());
    }

    public function testBad1()
    {
        $this->expectException(RuntimeException::class);

        DateTimeOptional::fromString('2021-05-A 10:20:30');
    }

    public function testBad2()
    {
        $this->expectException(RuntimeException::class);

        DateTimeOptional::fromString('2021-05-1 10:20:30');
    }

    public function testEmpty()
    {
        $this->expectException(RuntimeException::class);

        DateTimeOptional::fromString('');
    }

    public function testGetDateTime()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $this->assertEquals('2021-05-01', $value->getDateTime()->format('Y-m-d'));
    }

    public function testGetMethods()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $dt = new DateTimeImmutable('2021-05-01 10:20:30', new DateTimeZone('UTC'));

        $this->assertEquals(1, $value->getDay());
        $this->assertEquals(5, $value->getMonth());
        $this->assertEquals(2021, $value->getYear());
        $this->assertEquals(6, $value->getDayOfWeek());
        $this->assertEquals(10, $value->getHour());
        $this->assertEquals(20, $value->getMinute());
        $this->assertEquals(30, $value->getSecond());

        $this->assertEquals($dt->getTimestamp(), $value->getTimestamp());
    }

    public function testAdd()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $modifiedValue = $value->add(DateInterval::createFromDateString('1 day'));

        $this->assertEquals('2021-05-02 10:20:30', $modifiedValue->getString());

        $this->assertNotSame($modifiedValue, $value);
    }

    public function testSubtract()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $modifiedValue = $value->subtract(DateInterval::createFromDateString('1 day'));

        $this->assertEquals('2021-04-30 10:20:30', $modifiedValue->getString());

        $this->assertNotSame($modifiedValue, $value);
    }

    public function testModify()
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $modifiedValue = $value->modify('+1 month');

        $this->assertEquals('2021-06-01 10:20:30', $modifiedValue->getString());

        $this->assertNotSame($modifiedValue, $value);
    }

    public function testWithTimezone()
    {
        $value = DateTimeOptional
            ::fromString('2021-05-01 10:20:30')
            ->withTimezone(new DateTimeZone('Europe/Kiev'));

        $this->assertEquals('2021-05-01 10:20:30', $value->getString());

        $this->assertEquals(13, $value->getHour());
    }

    public function getGetTimezone()
    {
        $value = DateTimeOptional
            ::fromString('2021-05-01 10:20:30')
            ->withTimezone(new DateTimeZone('Europe/Kiev'));

        $this->assertEquals(new DateTimeZone('Europe/Kiev'), $value->getTimezone());
    }

    public function testDiff(): void
    {
        $value1 = DateTimeOptional::fromString('2021-05-01 10:10:30');
        $value2 = DateTimeOptional::fromString('2021-05-01 10:20:30');

        $this->assertEquals(10, $value1->diff($value2)->i);
        $this->assertEquals(0, $value1->diff($value2)->invert);
    }

    public function testNow(): void
    {
        $value = DateTimeOptional::createNow();

        $this->assertNotNull($value);
    }


    public function testWithTime1(): void
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:10:30');

        $this->assertEquals(
            '2021-05-01 00:00:00',
            $value->withTime(0, 0, 0)->getString()
        );

        $this->assertEquals(
            '2021-05-01 00:10:30',
            $value->withTime(0, null, null)->getString()
        );

        $this->assertEquals(
            '2021-05-01 10:00:00',
            $value->withTime(null, 0)->getString()
        );

        $this->assertEquals(
            '2021-05-01 10:00:10',
            $value->withTime(null, 0, 10)->getString()
        );
    }

    public function testWithTime2(): void
    {
        $value = DateTimeOptional::fromString('2021-05-01');

        $this->assertEquals(
            '2021-05-01 00:00:00',
            $value->withTime(0, 0, 0)->getString()
        );

        $this->assertEquals(
            '2021-05-01 00:00:00',
            $value->withTime(0, null, null)->getString()
        );

        $this->assertEquals(
            '2021-05-01 00:00:00',
            $value->withTime(null, 0)->getString()
        );

        $this->assertEquals(
            '2021-05-01 00:00:10',
            $value->withTime(null, 0, 10)->getString()
        );
    }

    public function testComparison(): void
    {
        $value = DateTimeOptional::fromString('2021-05-01 10:10:30')
            ->withTimezone(new DateTimeZone('Europe/Kiev'));

        $this->assertTrue(
            $value->isEqualTo(
                $value->withTimezone(new DateTimeZone('UTC'))
            )
        );

        $this->assertFalse(
            $value->isEqualTo(
                $value->modify('+1 minute')
            )
        );

        $this->assertFalse(
            $value->isGreaterThan(
                $value->modify('+1 minute')
            )
        );

        $this->assertFalse(
            $value->isLessThan(
                $value->modify('-1 minute')
            )
        );

        $this->assertTrue(
            $value->isGreaterThan(
                $value->modify('-1 minute')
            )
        );

        $this->assertTrue(
            $value->isLessThan(
                $value->modify('+1 minute')
            )
        );
    }
}
