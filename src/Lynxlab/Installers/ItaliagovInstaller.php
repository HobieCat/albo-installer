<?php

namespace Lynxlab\Installers;

class ItaliagovInstaller extends BaseInstaller
{
    /** @var array<string, string> */
    protected $locations = array(
        'extrafiles' => 'themes/custom/italiagov/{$name}/',
    );
}
