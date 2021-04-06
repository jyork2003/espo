<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2021 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
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

namespace tests\unit\Espo\Core\Acl;

use Espo\Core\{
    Acl\ScopeData,
    Acl\Table,
};

use InvalidArgumentException;
use RuntimeException;

class ScopeDataTest extends \PHPUnit\Framework\TestCase
{

    protected function setUp() : void
    {
    }

    public function testBooleanTrue()
    {
        $data = ScopeData::fromRaw(true);

        $this->assertTrue($data->isBoolean());
        $this->assertTrue($data->isTrue());
        $this->assertFalse($data->isFalse());

        $this->assertTrue($data->getRaw());

        $this->assertEquals(Table::LEVEL_NO, $data->getDelete());
    }

    public function testBooleanFalse()
    {
        $data = ScopeData::fromRaw(false);

        $this->assertTrue($data->isBoolean());
        $this->assertTrue($data->isFalse());
        $this->assertFalse($data->isTrue());

        $this->assertFalse($data->getRaw());

        $this->assertEquals(Table::LEVEL_NO, $data->getDelete());
    }

    public function testNotBoolean()
    {
        $data = ScopeData::fromRaw((object) []);

        $this->assertFalse($data->isBoolean());

        $this->assertFalse($data->isTrue());
        $this->assertFalse($data->isFalse());
    }

    public function testInvalid()
    {
        $this->expectException(InvalidArgumentException::class);

        ScopeData::fromRaw(null);
    }

    public function testRecord()
    {
        $raw = (object) [
            Table::ACTION_CREATE => Table::LEVEL_YES,
            Table::ACTION_READ => Table::LEVEL_ALL,
            Table::ACTION_EDIT => Table::LEVEL_TEAM,
            Table::ACTION_DELETE => Table::LEVEL_NO,
        ];

        $data = ScopeData::fromRaw($raw);

        $this->assertEquals(Table::LEVEL_YES, $data->getCreate());
        $this->assertEquals(Table::LEVEL_ALL, $data->getRead());
        $this->assertEquals(Table::LEVEL_TEAM, $data->getEdit());
        $this->assertEquals(Table::LEVEL_NO, $data->getDelete());

        $this->assertEquals($raw, $data->getRaw());

        $this->assertNotSame($raw, $data->getRaw());

        $this->assertTrue($data->hasNotNo());
    }

    public function testRecordEmpty()
    {
        $raw = (object) [];

        $data = ScopeData::fromRaw($raw);

        $this->assertEquals(Table::LEVEL_NO, $data->getDelete());

        $this->assertFalse($data->hasNotNo());
    }

    public function testRecordOnlyNo()
    {
        $raw = (object) [
            Table::ACTION_CREATE => Table::LEVEL_NO,
            Table::ACTION_READ => Table::LEVEL_NO,
            Table::ACTION_EDIT => Table::LEVEL_NO,
            Table::ACTION_DELETE => Table::LEVEL_NO,
        ];

        $data = ScopeData::fromRaw($raw);

        $this->assertEquals(Table::LEVEL_NO, $data->getDelete());

        $this->assertFalse($data->hasNotNo());
    }

    public function testAccessingProperty()
    {
        $this->expectException(RuntimeException::class);

        $data = ScopeData::fromRaw(false);

        $data->read;
    }
}
