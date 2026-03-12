<?php

namespace App\FrontModule\Presenters;

use App\Presenters\BasePresenter;

abstract class FrontPresenter extends BasePresenter
{
    public function startup(): void
    {
        parent::startup();
        
        // Front module specific startup (assets init, language logic)
        // Like ebcar/app/FrontModule/Presenters/FrontPresenter.php
    }
}
