@extends('layouts.email')

@section('content')


	<!-- Email Body : BEGIN -->
    <table cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#ffffff" width="700" style="margin: auto;" class="email-container">


    	<!-- 1 Column Text : BEGIN -->
        <tr>

        	<td style="padding: 40px; text-align: left; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555;">

        		<h3>Olá </h3>

        		@if( $dadosVendedor['nomeFantasiaCliente'] != '')

    				<p>
	        			Foi aprovado um orçamento para o cliente : {{ $dadosVendedor['nomeFantasiaCliente'] }}
	        		</p>

    			@else

    				<p>
	        			Foi aprovado um orçamento para o cliente : {{ $dadosVendedor['nomeCliente'] }}
	        		</p>

    			@endif


    			<h3>
    				Dados do orçamento :
    			</h3>


    			<table width="100%">
    				<thead>
		                <tr>
		                	<th>Cliente</th>
		                	<th>Id orçamento</th>
		                    <th>Título</th>
		                </tr>
		            </thead>
		            <tbody>

		            	<tr style="text-align: center;">
		            		<td>
			    				@if( $dadosVendedor['nomeFantasiaCliente'] != '')
			    					{{ $dadosVendedor['nomeFantasiaCliente'] }}
			    				@else
			    					{{ $dadosVendedor['nomeCliente'] }}
			    				@endif
			    			</td>
			    			<td>
			    				{{ $dadosVendedor['idOrcamento'] }}
			    			</td>
			    			<td>
			    				{{ $dadosVendedor['tituloOrcamento'] }}
			    			</td>
			    			
		            	</tr>

		            </tbody>

    			</table>

    			<table width="100%">

    				<thead>
		                <tr>
		                    <th>Data fechamento do orçamento</th>
		                    <th>Tipo de pagamento</th>
		                    <th>Valor do pagamento</th>
		                </tr>
		            </thead>
		            <tbody>
		            	<tr style="text-align: center;">

		            		<td>
			    				{{
			    					$dadosVendedor['dataFechamentoOrc']
			    				}}
			    			</td>
			    			<td>
			    				{{ $dadosVendedor['tipoPagamento'] }}
			    			</td>
			    			<td>
			    				R$ {{ number_format($dadosVendedor['valorTotalOrcamento'], 2) }}
			    			</td>

		            	</tr>
		            </tbody>
    			</table>

    			<table width="100%">
    				<thead>
		                <tr>
		                    <th>Id proposta</th>
		                    <th>Título da proposta</th>
		                    <th></th>
		                </tr>
		            </thead>
		            <tbody>
		            	<tr style="text-align: center;">
		            		<td>
			    				{{ $dadosVendedor['idProposta'] }}
			    			</td>
			    			<td>
			    				{{ $dadosVendedor['tituloProposta'] }}
			    			</td>
			    			<td>
			    			</td>
		            	</tr>
		            </tbody>
    			</table>          

    			<hr>

    			@if(!empty($dadosVendedor['dadosParceiro']))


    				<!-- <h3>
    					Dados do parceiro e valor da comissão :
    				</h3> -->


    				<!-- <table width="100%">

    					<thead>
			                <tr>
			                	<th>Nome do parceiro</th>
			                	<th>Email</th>
			                    <th>Data para pagar parceiro</th>
			                </tr>
			            </thead>
			            <tbody>
			            	<tr style="text-align: center;">
			            		<td>

			            			{{ $dadosVendedor['dadosParceiro']['nomeParceiro'] }}

			            		</td>

			            		<td>

			            			{{ $dadosVendedor['dadosParceiro']['emailParceiro'] }}

			            		</td>

			            		<td>
					    			{{ $dadosVendedor['dataVencimento'] }} + 5 dias

					    		</td>

			            	</tr>
			            </tbody>

    				</table> -->

    				<!-- <table width="100%">

    					<thead>
			                <tr>
			                	<th>Banco</th>
			                    <th>Agência</th>
			                    <th>Número</th>
			                    <th>Total comissão para pagar</th>
			                </tr>
			            </thead>
			            <tbody>
			            	<tr style="text-align: center;">

			            		<td>

			            			{{ $dadosVendedor['dadosParceiro']['nome_instituicao_bancaria'] }}

			            		</td>

			            		<td>

			            			{{ $dadosVendedor['dadosParceiro']['agencia'] }}

			            		</td>

			            		<td>

			            			{{ $dadosVendedor['dadosParceiro']['numero_conta'] }}

			            		</td>

			            		<td>

			            			R$ {{ number_format($dadosVendedor['dadosParceiro']['valorCommicao'], 2) }}

			            		</td>
			                </tr>
			            </tbody>

    				</table> -->

    				<!-- <table width="100%">

    					<thead>
			                <tr>
			                	<th></th>
			                    <th></th>
			                    <th>Registrar pagamento</th>
			                </tr>
			            </thead>
			            <tbody>
			            	<tr style="text-align: center;">

			            		<td></td>
			            		<td></td>
			            		<td>
			            			<a href="https://parcerias.eficazsystem.com.br/marcarComoPago/{{ $dadosVendedor['idProposta'] }}" style="background: #222222; border: 15px solid #222222; font-family: sans-serif; font-size: 13px; line-height: 1.1; text-align: center; text-decoration: none; display: block; border-radius: 3px; font-weight: bold;" class="button-a">
			            				&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#ffffff">Registrar pagamento de comissão</span>&nbsp;&nbsp;&nbsp;&nbsp;
			            			</a>
			            		</td>
			            	</tr>
			            </tbody>
    				</table> -->

    				<hr>

    			@endif
			    					
			   	<!-- 

					'idParceiroSistema'         => $parceiroConta['id_parceiro_sistema'],
                    'nomeParceiro'              => $parceiroConta['nome_vendedor'],
                    'emailParceiro'             => $parceiroConta['email_usuario'],
                    'nome_conta'                => $parceiroConta['nome_conta'],
                    'agencia'                   => $parceiroConta['agencia'],
                    'numero_conta'              => $parceiroConta['numero_conta'],
                    'nome_instituicao_bancaria' => $parceiroConta['nome_instituicao_bancaria'],
                    'valorCommicao'             => $valorCommicao

                    dataVencimentoParceiro

			   	-->

               
            </td>



        </tr>


    </table>

@endsection()