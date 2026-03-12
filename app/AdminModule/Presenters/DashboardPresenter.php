<?php

namespace App\AdminModule\Presenters;

final class DashboardPresenter extends AdminPresenter
{
    public function renderDefault(): void
    {
        $this->template->title = 'Dashboard';
        
        // Mock data for inquiries
        $this->template->inquiries = [
            ['id' => 1, 'name' => 'Jan Novák', 'email' => 'jan@novak.cz', 'subject' => 'Dotaz na členství', 'date' => '2026-03-09'],
            ['id' => 2, 'name' => 'Petra Svobodová', 'email' => 'petra@svobodova.cz', 'subject' => 'Spolupráce', 'date' => '2026-03-08'],
        ];
    }
}
