<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\TenantDeleted;
use Illuminate\Support\Facades\DB;

class DeleteTenantDatabase implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TenantDeleted $event)
    {
        $database = $event->website->uuid;
        DB::statement("DROP DATABASE IF EXISTS `$database`");
    }

}
