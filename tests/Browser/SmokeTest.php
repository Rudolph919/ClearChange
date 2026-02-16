<?php

/*
|--------------------------------------------------------------------------
| Smoke Tests
|--------------------------------------------------------------------------
|
| Browser smoke tests verify that key pages load without JavaScript errors
| or console output. Use assertNoSmoke() to check for JS errors and logs.
|
*/

test('homepage loads without smoke', function () {
    $page = visit('/');

    $page->assertSee('Laravel');
    $page->assertNoSmoke();
});

test('login page loads without smoke', function () {
    $page = visit('/login');

    $page->assertSee('Log in');
    $page->assertNoSmoke();
});

test('public pages have no JavaScript errors or console logs', function () {
    $pages = visit(['/', '/login', '/register']);

    $pages->assertNoSmoke();
});
