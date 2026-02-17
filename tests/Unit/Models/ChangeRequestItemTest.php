<?php

use App\Models\ChangeRequest;
use App\Models\ChangeRequestItem;

test('change request item belongs to change request', function () {
    $changeRequest = ChangeRequest::factory()->create();
    $item = ChangeRequestItem::factory()->create(['change_request_id' => $changeRequest->id]);

    expect($item->changeRequest)->toBeInstanceOf(ChangeRequest::class)
        ->and($item->changeRequest->id)->toBe($changeRequest->id);
});

test('change request item has field tracking attributes', function () {
    $changeRequest = ChangeRequest::factory()->create();
    $item = ChangeRequestItem::create([
        'change_request_id' => $changeRequest->id,
        'field_name' => 'salary',
        'old_value' => '50000',
        'new_value' => '55000',
    ]);

    expect($item->field_name)->toBe('salary')
        ->and($item->old_value)->toBe('50000')
        ->and($item->new_value)->toBe('55000');
});
