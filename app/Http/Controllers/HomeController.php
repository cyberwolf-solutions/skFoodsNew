<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {


        $totalOrders = Order::all()->count();
        

        // Get today's date
        $todayDate = Carbon::now()->toDateString();

        // Get today's orders 
        $todayOrders = Order::whereDate('created_at', $todayDate)->get();

       

        $customers = Customer::all();

        $employees = Employee::all();

        $suppliers = Supplier::all();

        $Products = Product::all();

        $orders1 = Order::orderBy('created_at', 'desc')->simplePaginate(10);
;



        return view('home', compact('totalOrders', 'todayOrders','customers' , 'employees' ,'suppliers' , 'Products','orders1'));
    }
}
