<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\GuestUser;
use Fisharebest\Webtrees\Http\Exceptions\HttpAccessDeniedException;
use Fisharebest\Webtrees\TestCase;
use Fisharebest\Webtrees\User;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

#[CoversClass(AuthAdministrator::class)]
class AuthAdministratorTest extends TestCase
{
    public function testAllowed(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('lorem ipsum'));

        $user = $this->createMock(User::class);
        $user->method('getPreference')->with(UserInterface::PREF_IS_ADMINISTRATOR)->willReturn('1');

        $request    = self::createRequest()->withAttribute('user', $user);
        $middleware = new AuthAdministrator();
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
        self::assertSame('lorem ipsum', (string) $response->getBody());
    }

    public function testNotAllowed(): void
    {
        $this->expectException(HttpAccessDeniedException::class);
        $this->expectExceptionMessage('You do not have permission to view this page.');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('lorem ipsum'));

        $user = $this->createMock(User::class);
        $user->method('getPreference')->with(UserInterface::PREF_IS_ADMINISTRATOR)->willReturn('');

        $request    = self::createRequest()->withAttribute('user', $user);
        $middleware = new AuthAdministrator();

        $middleware->process($request, $handler);
    }

    public function testNotLoggedIn(): void
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(response('lorem ipsum'));

        $request    = self::createRequest()->withAttribute('user', new GuestUser());
        $middleware = new AuthAdministrator();
        $response   = $middleware->process($request, $handler);

        self::assertSame(StatusCodeInterface::STATUS_FOUND, $response->getStatusCode());
    }
}
