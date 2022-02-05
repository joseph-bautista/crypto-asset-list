<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Contract;
use App\Models\Holding;
use App\Models\Network;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ContractController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreContractRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContractRequest $request)
    {
        $network = Network::where('id',$request->networkValue)->first();
        if($network->name == 'ERC20'){
            $contract_response = Http::get("https://api.etherscan.io/api?module=contract&action=getsourcecode&address=".$request->contractAddress."&apikey=".$network->api_key);
            $contractData = $contract_response->json();
            if($contractData["result"] == "Invalid Address format" || $contractData["result"][0]["ContractName"] == ''){
                return false;
            }else{
                $asset_response = Http::get("https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=".$request->contractAddress."&address=".$request->walletAddress."&tag=latest&apikey=".$network->api_key);
                $assetData = $asset_response->json();
            }
        }else if($network->name == 'BEP20'){
            $contract_response = Http::get("https://api.bscscan.com/api?module=contract&action=getsourcecode&address=".$request->contractAddress."&apikey=".$network->api_key);
            $contractData = $contract_response->json();
            if($contractData["result"] == "Invalid Address format" || $contractData["result"][0]["ContractName"] == ''){
                return false;
            }else{
                $asset_response = Http::get("https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=".$request->contractAddress."&address=".$request->walletAddress."&tag=latest&apikey=".$network->api_key);
                $assetData = $asset_response->json();
            }
        }
        $quantity = number_format(((float)$assetData["result"]/1000000000000000000),2,'.','');
        $contractExist = Contract::where('address',$request->contractAddress)->exists();
        $wallet = Wallet::where('address',$request->walletAddress)->first();
        if($contractExist){
            $contract = Contract::where('address',$request->contractAddress)->first();
            $holdingExist = Holding::where('wallet_id',$wallet->id)
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
}
