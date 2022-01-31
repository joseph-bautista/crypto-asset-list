<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        //sample of API endpoint that will list token holdings but not free
        // https://api.bscscan.com/api?module=account&action=addresstokenbalance&address=0x99817ce62abf5b17f58e71071e590cf958e5a1bf&page=1&offset=100&apikey=YourApiKeyToken       

        $networks = DB::table('networks')->get();
        return view('home', compact('networks'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreWalletRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWalletRequest $request)
    {
        $walletExist = DB::table('wallets')->where('address',$request->walletAddress)->exists();
        if($walletExist){
            return "Wallet Exist";
        }else{
            $walletAddress = new Wallet;
            $walletAddress->address = $request->walletAddress;
            $walletAddress->save();
            return redirect("/");
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function show_assets(Wallet $wallet, $walletAddress)
    {

        $wallet = DB::table('wallets')->where('address', $walletAddress)->first();

        //Tried different approach as heroku do not read aliases in the query

        // $assets = DB::table('holdings as h')
        //     ->select('c.name as contract_name', 'h.quantity', 'n.name as network_name')
        //     ->join('networks as n', 'n.id', '=', 'h.network_id')
        //     ->join('contracts as c', 'c.id', '=', 'h.contract_id')
        //     ->where('h.wallet_id', $wallet->id)->get();

        // $assets = DB::select("SELECT h.quantity, n.name AS network_name, c.name AS contract_name FROM holdings AS h INNER JOIN networks AS n ON n.id = h.network_id INNER JOIN contracts AS c ON c.id = h.contract_id WHERE h.wallet_id = $wallet->id");

        $holdings = DB::table('holdings')->where('wallet_id', $wallet->id)->get();

        $assets=[];

        foreach($holdings as $holding){
            $network = DB::table('networks')->where('id', $holding->network_id)->first();
            $contract = DB::table('contracts')->where('id', $holding->contract_id)->first();

            $test["contract_name"] = $contract->name;
            $test["quantity"] = $holding->quantity;
            $test["network_name"] = $network->name;

            array_push($assets, $test);

        }

        return $assets;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWalletRequest  $request
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWalletRequest $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wallet $wallet)
    {
        //
    }
}
