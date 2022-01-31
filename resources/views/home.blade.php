@extends('template')

@section('title', 'Xtuality Home')

@section('content')


<div class="row">
	<div class="col-lg-12">
		<h1>Xtuality Assignment <button type="button" class="btn btn-primary" id="connectWallet">Connect Wallet</button></h1>
		

        <p id="walletAddressContainer"><strong>Wallet Address:</strong> <span id="walletAddress"></span></p>
        <form id="form-container" class="row" >
            <div class="col-lg-4 offset-lg-4">
                <input type="text" class="form-control" placeholder="Contract Address" id="contract_address" name="contract_address" aria-label="contract_address" aria-describedby="addon-wrapping">
                <small id="alert-contract" class="required">*Please enter contract address.</small>
                <div id="network-radio-container">
                    @foreach ($networks as $network)
                        <div class="form-check">
                          <input class="form-check-input network" type="radio" name="network" data-name="{{$network->name}}" id="network{{$network->id}}" value="{{$network->id}}">
                          <label class="form-check-label" for="network{{$network->id}}">
                            {{$network->name}}
                          </label>
                        </div>

                    @endforeach
                    <input type="hidden" name="network_value" id="network_value">
                    <small id="alert-network" class="required">*Please select network.</small>
                </div>

                <div id="submit-button-container">
                    <button type="button" class="btn btn-primary" id="submitButton">Submit</button>
                </div>
            </div>
            
        </form>
        <hr>

	</div>

    <div class="col-lg-12" id="metamaskAlert">
        <div class="alert alert-danger" role="alert">
          <p>Metamask is not installed. Click <a href="https://chrome.google.com/webstore/detail/metamask/nkbihfbeogaeaoehlefnkodbefgpgknn" target="_blank">here</a> to install metamask extension in your chrome browser.</p>
        </div>
    </div>

    {{-- <div class="col-lg-12"><strong><h2>Asset List</h2></strong></div> --}}
    <div class="col-lg-12">
        
        <div class="table-body">
            <table class="table table-success table-striped">
              <thead>
                <tr>
                  <th scope="col">Coin</th>
                  <th scope="col">Quantity</th>
                  <th scope="col">Network</th>
                </tr>
              </thead>
              <tbody id="assetList">
                
              </tbody>
            </table>    
        </div>

    </div>
</div>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	$(document).ready(function(){
		if (typeof web3 !== 'undefined') {
            // Use Mist/MetaMask's provider.

            $("#metamaskAlert").hide();
		    EThAppDeploy.loadEtherium();
            
           
        } else {
            
            $("#metamaskAlert").show();

            $("#walletAddressContainer").hide();
            $("#form-container").hide();
            $('#connectWallet').hide();

            // Handle the case where the user doesn't have web3. Probably 
            // show them a message telling them to install Metamask in 
            // order to use the app.
        }

        ethereum.on('accountsChanged', (accounts) => {
          // Handle the new accounts, or lack thereof.
          // "accounts" will always be an array, but it can be empty.
          window.location.reload();
        });

        ethereum.on('chainChanged', (chainId) => {
          // Handle the new chain.
          // Correctly handling chain changes can be complicated.
          // We recommend reloading the page unless you have good reason not to.
          window.location.reload();
        });

        $(".required").hide();


	});

	$("#connectWallet").click(function(){
		EThAppDeploy.loadEtherium();
        EThAppDeploy.requestAccount(ethereum);
	});

    $('.network').click(function(){

        $("#network_value").val($(this).val());
        $("#network_value").attr('data-name', $(this).attr('data-name'));
    });

    $("#submitButton").click(function(){
        
        var networkValue = $("#network_value").val();

        var contractAddress = $("#contract_address").val();

        if(contractAddress == ''){
            $("#alert-contract").show();
            return false;
        }else{
            $("#alert-contract").hide();
        }
        if(networkValue == ''){
            $("#alert-network").show();
            return false;
        }else{
            $("#alert-network").hide();
        }

        backEndTransactions.addAsset();
    });

    EThAppDeploy = {
        loadEtherium: async () => {
            if (typeof window.ethereum !== 'undefined') {
                EThAppDeploy.web3Provider = ethereum;
                if(ethereum.selectedAddress !== null){
                    $("#walletAddressContainer").show();
                    $("#form-container").show();
                    EThAppDeploy.requestAccount(ethereum);
                }else{
                    $("#walletAddressContainer").hide();
                    $("#form-container").hide();
                }
                
            } else {
                $('#connectWallet').show();
            }
        },
        /****
         * Request A Account
         * **/
        requestAccount: async (ethereum) => {
            ethereum
                .request({
                    method: 'eth_requestAccounts'
                })
                .then((resp) => {
                	if(resp[0]!==''){
                        $('#connectWallet').hide();
                        $("#walletAddress").text(resp[0]);
                        $('#walletAddressContainer').show();
                        $("#form-container").show();

                        backEndTransactions.saveWalletAddress();
                        backEndTransactions.showAssets();
                    }else{
                        
                    }
                })
                .catch((err) => {
                });
        },
        
    }

    var backEndTransactions = {
        saveWalletAddress: () => {
            var walletAddress = $("#walletAddress").text();
            // console.log(walletAddress);
            $.ajax({
                url : '/add/wallet_address',
                method : 'post',
                data : {walletAddress : walletAddress},
            }).done( data =>{
                console.log(data);
            });

            
        },

        showAssets: () => {
            var walletAddress = $("#walletAddress").text();
            // console.log(walletAddress);
            $.ajax({
                url : '/show/assets/'+walletAddress,
                method : 'get',
            }).done( data =>{
                var assetListhtml = '';
                jQuery.each(data, function(i,v){
                    assetListhtml += "<tr><td>"+v.contract_name+"</th><td>"+v.quantity+"</td><td>"+v.network_name+"</td></tr>";
                });

                $("#assetList").html(assetListhtml);
                console.log(data);
            });
        },

        addAsset: () => {
            var networkValue = $("#network_value").val();

            var contractAddress = $("#contract_address").val();

            var walletAddress = $("#walletAddress").text();
            // var walletAddress = "0x64b9cD0086af02990dB6C775c0375c77BbB30E21";
            $.ajax({
                url : '/add/contract_address',
                method : 'post',
                data : {contractAddress : contractAddress, networkValue:networkValue, walletAddress:walletAddress},
            }).done( data =>{
                console.log(data);
                if(data){
                    // alert(data);
                    window.location.reload();
                }else{
                    alert("Invalid Contract Address")
                }
            });
        },
    }

</script>

@endsection
