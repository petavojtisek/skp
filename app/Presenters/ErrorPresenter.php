<?php

namespace App\Presenters;

use Nette;
use Nette\Application\Responses\ForwardResponse;

final class ErrorPresenter implements Nette\Application\IPresenter
{
    public function run(Nette\Application\Request $request): Nette\Application\IResponse
    {
        $e = $request->getParameter('exception');

        if ($e instanceof Nette\Application\BadRequestException) {
            // Tady to předáme kolegovi Error4xxPresenter
            return new ForwardResponse($request->setPresenterName('Error4xx'));
        }

        // Pokud je to 500, Tracy si to přebere sama a vykreslí 500.phtml
        return new ForwardResponse($request);
    }
}
