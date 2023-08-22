<?php

namespace App\Traits;

use App\Events\TenantDeleted;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;

trait TenantTrait
{
   
    public function registerTenant($fqdn, $name)
    {
        $website        = new Website;
        $website->uuid  = createUuid($name);
        app(WebsiteRepository::class)->create($website);

        $hostname       = new Hostname;
        $hostname->fqdn = $fqdn.'.'.env(key:'APP_DOMAIN');
        $hostname       = app(HostnameRepository::class)->create($hostname);
        app(HostnameRepository::class)->attach($hostname, $website);
    }

    public function deleteTenant($user)
    {
  
        $id         = $user->id;
        $website    = app(WebsiteRepository::class)->findById($id);
        $hostname   = Hostname::where('website_id', $website->id)->first();

        event(new TenantDeleted($website));

        app(WebsiteRepository::class)->delete($website, true);
        app(HostnameRepository::class)->delete($hostname, true);
    }
}