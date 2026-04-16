<?php

test('unauthenticated api returns unauthorized or not found', function () {
    $response = $this->getJson('/api/user');
    
    expect(in_array($response->status(), [401, 404]))->toBeTrue();
});
