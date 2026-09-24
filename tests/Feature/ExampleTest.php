<?php

declare(strict_types=1);

test('the landing page renders', function (): void {
    $this->get(route('home'))->assertOk();
});
