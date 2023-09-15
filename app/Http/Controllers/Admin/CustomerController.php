<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessCustomer;
use App\Models\Customer;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $allCustomer=Customer::all();

        return view('admin.customer.index', compact( 'allCustomer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $customer=new Customer();
        $customer->name=$request->input('name');
        $customer->phone=$request->input('phone');
        $customer->email=$request->input('email');
        $customer->password= Hash::make($request->input('password'));
        $customer->gender=$request->input('gender');
        $customer->status=1;
        if ($customer->save()){
            $businessCustomer=new BusinessCustomer();
            $businessCustomer->business_id=auth('business')->id();
            $businessCustomer->customer_id=$customer->id;
            $businessCustomer->save();
            return to_route('admin.customer.index')->with('response', [
                'status'=>"success",
                'message'=>"Müşteri Eklendi. Artık bu müşteriler için işlem yapabilirsiniz."
            ]);
        }


    }

    public function delete($id)
    {
        if (BusinessCustomer::find($id)->delete()){
            return response()->json([
                'status'=>"success"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Customer $customer
     * @return Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Customer $customer
     * @return Response
     */
    public function destroy(Customer $customer)
    {
        if ($customer->delete()){
            return response()->json([
                'status' => "success",
                'message' => "Müşteri Kaydı Silindi"
            ]);
        }
    }
}
