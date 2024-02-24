<?php

it('can register helpers', function () {
    expect(firstHelper())
        ->toBe('First Helper')
        ->and(secondHelper())
        ->toBe('Second Helper');
});
