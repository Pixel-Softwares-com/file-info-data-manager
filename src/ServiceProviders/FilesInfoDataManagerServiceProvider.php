<?php

namespace FilesInfoDataManagers\ServiceProviders;

use Illuminate\Support\ServiceProvider;

class FilesInfoDataManagerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes(
            [__DIR__ . "/../../config/file-info-data-manager-config.php" => config_path("file-info-data-manager-config.php") ] ,
            'file-info-data-manager-config'
        );

    }

}
