<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Contract;
use App\Models\Holding;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreContractRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContractRequest $request)
    {
        $network = DB::table("networks")->where('id',$request->networkValue)->first();

        if($network->name == 'ERC20'){
            $apiKey = "PV24NRRVMTC4DBF9K6SYQ6Q7SS1328YMUA";
            $contract_response = Http::get("https://api.etherscan.io/api?module=contract&action=getsourcecode&address=".$request->contractAddress."&apikey=".$apiKey);
            $contractData = $contract_response->json();
            if($contractData["result"] == "Invalid Address format" || $contractData["result"][0]["ContractName"] == ''){
                return false;
            }else{
                $asset_response = Http::get("https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=".$request->contractAddress."&address=".$request->walletAddress."&tag=latest&apikey=".$apiKey);
                $assetData = $asset_response->json();
            }
        }else if($network->name == 'BEP20'){
            $apiKey = "MB7I7YDQG7GGEFM6CHFF1IQYPMQV7N5B57";
            $contract_response = Http::get("https://api.bscscan.com/api?module=contract&action=getsourcecode&address=".$request->contractAddress."&apikey=".$apiKey);
            $contractData = $contract_response->json();
            // dd($contractData);
            if($contractData["result"] == "Invalid Address format" || $contractData["result"][0]["ContractName"] == ''){
                return false;
            }else{
                $asset_response = Http::get("https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=".$request->contractAddress."&address=".$request->walletAddress."&tag=latest&apikey=".$apiKey);
                $assetData = $asset_response->json();
            }

        }


        $quantity = number_format(((float)$assetData["result"]/1000000000000000000),2,'.','');

        $contractExist = DB::table('contracts')->where('address',$request->contractAddress)->exists();
        $wallet = DB::table('wallets')->where('address',$request->walletAddress)->first();

        if($contractExist){
            $contract = DB::table('contracts')->where('address',$request->contractAddress)->first();
            $holdingExist = DB::table('holdings')
                ->where('wallet_id',$wallet->id)
                ->where('contract_id',$contract->id)
                ->where('network_id',$network->id)
                ->exists();
            if($holdingExist){
                return "Holding Exist";
            }else{
                $holding = new Holding;
                $holding->wallet_id = $wallet->id;
                $holding->contract_id = $contract->id;
                $holding->quantity = $quantity;
                $holding->network_id = $network->id;
                if($holding->save()){
                    return true;
                }else{
                    return false;
                }
            }

        }else{
            $contractAddress = new Contract;
            $contractAddress->address = $request->contractAddress;
            $contractAddress->name = $contractData["result"][0]['ContractName'];
            $contractAddress->save();
            $contractId = $contractAddress->id;

            $holding = new Holding;
            $holding->wallet_id = $wallet->id;
            $holding->contract_id = $contractId;
            $holding->quantity = $quantity;
            $holding->network_id = $network->id;
            if($holding->save()){
                return true;
            }else{
                return false;
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function show(Contract $contract)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateContractRequest  $request
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContractRequest $request, Contract $contract)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contract $contract)
    {
        //
    }
}
