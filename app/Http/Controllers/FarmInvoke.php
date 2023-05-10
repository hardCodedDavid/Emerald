<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\FarmlistController as FarmNow;

class FarmInvoke extends Controller
{
    public static function init()
    {
    	FarmNow::checkForFarmStartDateAndMarkFarmAsOpen();
    	FarmNow::checkForFarmCloseDateAndStartInvestmentCountdown();
    }
}
