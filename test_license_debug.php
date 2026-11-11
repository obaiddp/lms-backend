<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'License count: '.App\Models\License::count()."\n";
App\Models\License::orderByDesc('id')->limit(5)->get(['id', 'key', 'vault_user_id', 'user_id'])->each(function ($l) {
    echo json_encode($l->toArray())."\n";
});

$c = App\Models\Course::find(43);
echo 'Course 43: '.($c ? json_encode($c->only(['id', 'title', 'course_type'])) : 'null')."\n";

$license = App\Models\License::whereNotNull('vault_user_id')->first();
if ($license) {
    $pivotIds = $license->courses()->pluck('id')->all();
    $courseIds = $license->courses()->pluck('courses.id')->all();
    echo "License {$license->id} pluck(id): ".json_encode($pivotIds)."\n";
    echo "License {$license->id} pluck(courses.id): ".json_encode($courseIds)."\n";
}
