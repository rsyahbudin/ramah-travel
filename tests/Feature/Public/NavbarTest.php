<?php

test('public pages have centered navbar menu', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Destinations');
    $response->assertSee('Our Story');

    // Verify the z-index increase
    $response->assertSee('z-[100]');

    // Verify 3-column flex containers for perfect centering
    $response->assertSee('flex-1 flex justify-start');
    $response->assertSee('flex-1 flex justify-center hidden md:flex items-center gap-10 lg:gap-14');
    $response->assertSee('flex-1 flex items-center justify-end');
});

test('about page has centered navbar menu', function () {
    $response = $this->get(route('about'));

    $response->assertStatus(200);
    $response->assertSee('Destinations');
    $response->assertSee('Our Story');
    $response->assertSee('z-[100]');
});

test('destinations index page has centered navbar menu', function () {
    $response = $this->get(route('destinations.index'));

    $response->assertStatus(200);
    $response->assertSee('Destinations');
    $response->assertSee('Our Story');
    $response->assertSee('z-[100]');
});
