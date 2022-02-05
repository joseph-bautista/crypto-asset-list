<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Wallet;
use App\Models\Network;
use App\Models\Holding;
use App\Models\Contract;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WalletController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreWalletRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWalletRequest $request)
    {
        $walletExist = Wallet::where('address',$request->walletAddress)->exists();
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
        $holdings = Wallet::where('address', $walletAddress)->first()->holdings;
        $assets=[];
        foreach($holdings as $holding){
            $asset["contract_name"] = $holding->contract->name;
            $asset["quantity"] = $holding->quantity;
            $asset["network_name"] = $holding->network->name;
            array_push($assets, $asset);

        }
        return $assets;
    }
}
