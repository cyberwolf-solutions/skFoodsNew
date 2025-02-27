<?php

namespace App\Http\Controllers;

use App\Models\OrderPayment;
use App\Models\Stock;
use App\Models\User;
use App\Models\Booking;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\Purchases;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function user()
    {
        $title = 'User Report';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        $data = User::with('roles')->get();

        return view('reports.Userindex', compact('title', 'breadcrumbs', 'data'));
    }

    public function customer()
    {
        $title = 'Guest Report';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        $data = Customer::all();


        return view('reports.customerindex', compact('title', 'breadcrumbs', 'data'));
    }
    public function employee()
    {
        $title = 'Employees Report';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        $data = Employee::all();


        return view('reports.employeeindex', compact('title', 'breadcrumbs', 'data'));
    }
    public function supplier()
    {
        $title = 'Supplier Report';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        $data = Supplier::all();


        return view('reports.supplierindex', compact('title', 'breadcrumbs', 'data'));
    }
    public function purchase()
    {
        $title = 'Purchase Report';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        $data = Purchases::with(['supplier', 'items.product', 'payments'])->get();


        return view('reports.purchaseindex', compact('title', 'breadcrumbs', 'data'));
    }
    public function product()
    {
        $title = 'Product Report';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        $data = Product::with('category')->get();


        return view('reports.productindex', compact('title', 'breadcrumbs', 'data'));
    }

    // public function booking()
    // {
    //     $title = 'Booking Report';

    //     $breadcrumbs = [
    //         // ['label' => 'First Level', 'url' => '', 'active' => false],
    //         ['label' => $title, 'url' => '', 'active' => true],
    //     ];
    //     $data = Booking::all();
    //     return view('reports.bookingindex', compact('title', 'breadcrumbs', 'data'));
    // }
    public function order()
    {
        $title = 'Sales Report';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        $data = Order::all();
        return view('reports.orderindex', compact('title', 'breadcrumbs', 'data'));
    }
    public function final(Request $request)
    {
        $title = 'Final Report';

        $breadcrumbs = [
            ['label' => $title, 'url' => '', 'active' => true],
        ];

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // $order = Order::whereBetween('created_at', [$fromDate, $toDate])->get();
        // $checkincheckout = checkincheckout::whereBetween('checkin', [$fromDate, $toDate])->get();
        // $ingredients = Purchases::whereBetween('date', [$fromDate, $toDate])->get();
        // $inventory = OtherPurchase::whereBetween('date', [$fromDate, $toDate])->get();
        if ($fromDate && $toDate) {
            $order = Order::whereBetween('created_at', [$fromDate, $toDate])->get();
            // $checkincheckout = checkincheckout::whereBetween('checkin', [$fromDate, $toDate])->get();
            $ingredients = Purchases::whereBetween('date', [$fromDate, $toDate])->get();
            // $inventory = OtherPurchase::whereBetween('date', [$fromDate, $toDate])->get();
        } else {
            $order = Order::all();
            $orderpay = OrderPayment::all();
            // $checkincheckout = checkincheckout::all();
            $ingredients = Purchases::all();
            // $inventory = OtherPurchase::all();
        }

        $ingredientsTotal = $ingredients->sum('total');
        $orderTotal = $orderpay->sum('total');
        // $inventoryTotal = $inventory->sum('total');

        $sumsByCurrency = [
            'USD' => 0,
            'EUR' => 0,
            'LKR' => 0,
        ];


        // foreach ($checkincheckout as $transaction) {
        //     $currency = Customer::find($transaction->customer_id)->currency->name;
        //     switch ($currency) {
        //         case 'USD':
        //             $sumsByCurrency['USD'] += $transaction->total_amount;
        //             break;
        //         case 'EUR':
        //             $sumsByCurrency['EUR'] += $transaction->total_amount;
        //             break;
        //         case 'LKR':
        //             $sumsByCurrency['LKR'] += $transaction->total_amount;
        //             break;
        //         default:

        //             break;
        //     }
        // }

        $sumsByCurrency1 = [
            'USD' => 0,
            'EUR' => 0,
            'LKR' => 0,
        ];




        // foreach ($order as $ord) {
        //     $currency = Customer::find($ord->customer_id)->currency->name;
        //     switch ($currency) {
        //         case 'USD':
        //             $sumsByCurrency1['USD'] += $ord->payment->total;
        //             break;
        //         case 'EUR':
        //             $sumsByCurrency1['EUR'] += $ord->payment->total;
        //             break;
        //         case 'LKR':
        //             $sumsByCurrency1['LKR'] += $ord->payment->total;
        //             break;
        //         default:

        //             break;
        //     }
        // }

        $sumsByCurrency2 = [
            'USD' => 0,
            'EUR' => 0,
            'LKR' => 0,
        ];

        // foreach ($checkincheckout as $transaction) {
        //     $currency = Customer::find($transaction->customer_id)->currency->name;
        //     switch ($currency) {
        //         case 'USD':
        //             $additionalServices = json_decode($transaction->additional_services, true);
        //             if (!empty($additionalServices)) {
        //                 foreach ($additionalServices as $service) {
        //                     $sumsByCurrency2['USD'] += $service['price'];
        //                 }
        //             }
        //             break;
        //         case 'EUR':
        //             $additionalServices = json_decode($transaction->additional_services, true);
        //             if (!empty($additionalServices)) {
        //                 foreach ($additionalServices as $service) {
        //                     $sumsByCurrency2['EUR'] += $service['price'];
        //                 }
        //             }
        //             break;
        //         case 'LKR':
        //             $additionalServices = json_decode($transaction->additional_services, true);
        //             if (!empty($additionalServices)) {
        //                 foreach ($additionalServices as $service) {
        //                     $sumsByCurrency2['LKR'] += $service['price'];
        //                 }
        //             }
        //             break;
        //         default:

        //             break;
        //     }
        // }

        return view('reports.all', compact('title', 'breadcrumbs', 'order', 'orderTotal','ingredients', 'ingredientsTotal'));
    }
    public function stockreport()
    {
        $title = 'Stock Report';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        $data = Stock::all();
        return view('reports.stockindex', compact('title', 'breadcrumbs', 'data'));
    }

    public function dailyorder()
    {
        $title = 'Daily Sales Report';

        $breadcrumbs = [
            ['label' => $title, 'url' => '', 'active' => true],
        ];

        $data = Order::whereDate('created_at', Carbon::today())->get();

        return view('reports.orderindex', compact('title', 'breadcrumbs', 'data'));
    }

    public function searchByType(Request $request)
    {
        $title = 'Order Report';

        $breadcrumbs = [
            // ['label' => 'First Level', 'url' => '', 'active' => false],
            ['label' => $title, 'url' => '', 'active' => true],
        ];
        $type = $request->type;
        if ($type === 'All') {
            $data = Order::all(); // Fetch all orders when "All" is selected
        } else {
            $data = Order::where('type', $type)->get(); // Filter by the selected type
        }
        // $data = Order::where('type', $type)->get();

        // return response()->json($data);
        return view('reports.orderindex', compact('title', 'breadcrumbs', 'data'));
    }
}
