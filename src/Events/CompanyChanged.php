<?php
namespace Diagro\Web\Events;

use Diagro\Token\Model\Company;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyChanged
{

    use Dispatchable, InteractsWithSockets, SerializesModels;


    public function __construct(
        public Company $companyFrom,
        public Company $companyTo,
    )
    {
    }


}