<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;

use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}
    public function indexVisit(Request $request)
    {

        return response()->json(
            $this->dashboardService->indexVisit(
                $request->query('range', 'week')
            )
        );
    }


    public function lowStock()
    {
        return response()->json(
            $this->dashboardService->lowStock()
        );
    }

    public function getTopCategories(Request $request)
    {
        return response()->json(
            $this->dashboardService->getTopCategories(
                $request->query('filter', 'week')
            )
        );
    }

    public function countOrder()
    {
        return response()->json(
            $this->dashboardService->countOrder()
        );
    }

    public function countUser()
    {
        return response()->json(
            $this->dashboardService->countUser()
        );
    }

    public function countPayment()
    {
        return response()->json(
            $this->dashboardService->countPayment()
        );
    }
}
