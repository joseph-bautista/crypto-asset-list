@extends('template')

@section('title', 'Xtuality Home')

@section('content')


<div class="row">
	<div class="col-lg-12">
		<h1>Xtuality Assignment</h1>
		<hr class="my-3"></hr>
		<button type="button" class="btn btn-primary" id="connectWallet">Connect Wallet</button>
	</div>
</div>

<script type="text/javascript">

	// const Web3 = require("web3");
	// alert(1);
		{{-- import detectEthereumProvider from '@metamask/detect-provider'; --}}

	$(document).ready(function(){

		// const provider = await detectEthereumProvider();

		// if (provider) {
		//   // From now on, this should always be true:
		//   // provider === window.ethereum
		//   startApp(provider); // initialize your app
		// } else {
		//   console.log('Please install MetaMask!');
		// }

		EThAppDeploy.loadEtherium();

	});

	$("#connectWallet").click(function(){
		// alert(2);
		// EThAppDeploy.loadEtherium();
		// console.log(ethereum);
		// console.log(window.ethereum.isConnected());

		// ethereum.on('chainChanged', (chainId) => {
		//   // Handle the new chain.
		//   // Correctly handling chain changes can be complicated.
		//   // We recommend reloading the page unless you have good reason not to.
		//   window.location.reload();
		// });
	});

	 // function startProcess() {
  //           if ($('#inp_amount').val() > 0) {
  //               // run metamsk functions here
  //               EThAppDeploy.loadEtherium();
  //           } else {
  //               alert('Please Enter Valid Amount');
  //           }
  //       }


        EThAppDeploy = {
            loadEtherium: async () => {
                if (typeof window.ethereum !== 'undefined') {
                    EThAppDeploy.web3Provider = ethereum;
                    EThAppDeploy.requestAccount(ethereum);


                } else {
                    alert(
                        "Not able to locate an Ethereum connection, please install a Metamask wallet"
                    );
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
                    	console.log(resp);
                        //do payments with activated account
                        EThAppDeploy.payNow(ethereum, resp[0]);
                    })
                    .catch((err) => {
                        // Some unexpected error.
                        console.log(err);
                    });
            },
            /***
             *
             * Do Payment
             * */
            // payNow: async (ethereum, from) => {
            //     var amount = $('#inp_amount').val();
            //     ethereum
            //         .request({
            //             method: 'eth_sendTransaction',
            //             params: [{
            //                 from: from,
            //                 to: "{~Your Account Addree~}",
            //                 value: '0x' + ((amount * 1000000000000000000).toString(16)),
            //             }, ],
            //         })
            //         .then((txHash) => {
            //             if (txHash) {
            //                 console.log(txHash);
            //                 //Store Your Transaction Here
            //             } else {
            //                 console.log("Something went wrong. Please try again");
            //             }
            //         })
            //         .catch((error) => {
            //             console.log(error);
            //         });
            // },


            payNow: async (ethereum, from) => {
                var amount = $('#inp_amount').val();
                ethereum
                    .request({
                        method: 'eth_getBalance',
                        params: ["0x29605402D41aD0813eA4354174eE1B841B55Cfa2", "latest"],
                    })
                    .then((txHash) => {
                        if (txHash) {
                            console.log(txHash);
                            //Store Your Transaction Here
                        } else {
                            console.log("Something went wrong. Please try again");
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
        }

</script>

@endsection
