@extends('layouts.email')

@section('content')


	<!-- Email Body : BEGIN -->
    <table cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#ffffff" width="700" style="margin: auto;" class="email-container">
	

    	<!-- 1 Column Text : BEGIN -->
        <tr>


        	<td style="padding: 40px; text-align: left; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555;">

        		<h3>Olá </h3>

        		<h3>
    				Segue Relação de orçamentos e a atual situação de cada uma delas:
    			</h3>

    			@if (! empty($dadosOrcamentos))


    				<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">

    					<thead>
				            <tr>
				                <th>Número orçamento</th>
				                <th>Título</th>
				                <th>Status</th>
				                <th>Responsável</th>
				                <th>Data abertura</th>
				                <th>Data Finalizada</th>
				           	</tr>
				        </thead>
				        <tbody>

				        	@foreach($dadosOrcamentos as $orcamento)

				        		<tr>

				        			<td>
				                        <a href="http://areatecnica.eficazsystem.com.br/orcamentos/{{ $orcamento['orcamento']->Workflow_ID }}" class=""> 
				                        	{{ $orcamento['orcamento']->Workflow_ID }} 
				                        </a>
				                    </td>
				                    <td>
				                        <a href="http://areatecnica.eficazsystem.com.br/orcamentos/{{ $orcamento['orcamento']->Workflow_ID }}" class=""> 
				                        	{{ $orcamento['orcamento']->Titulo }} 
				                        </a>
				                    </td>
				                    <td style="background-color:{{ $orcamento['situacaoCor'] }};">
				                        {{ $orcamento['orcamento']->Descr_Tipo }}
				                    </td>
				                    <td>
				                       	{{ $orcamento['orcamento']->Nome }}
				                    </td>

				                    <td>
				                        @php

				                        	$data  = $orcamento['orcamento']->Data_Abertura;
				                        	$teste = explode(' ',$data); 
				                        	echo implode('/',array_reverse(explode('-', $teste[0])));

				                        @endphp

				                    </td>

				                    <td>

				                    	@if (! empty($orcamento['orcamento']->Data_Finalizado))

					                        @php

					                        	$data  = $orcamento['orcamento']->Data_Finalizado;
					                        	$teste = explode(' ',$data); 
					                        	echo implode('/',array_reverse(explode('-', $teste[0])));

					                        @endphp

				                        @else

				                        	<span>Não definido.</span>

				                        @endif

				                    </td>

				        		</tr>

				        		<tr>

				        			<td colspan="2">
				                        			
				                    </td>

				                    <td colspan="2">

				                        <p style="color: blue">
				                        	Faturamento :  
				                        	@if( is_numeric($orcamento['faturamento']))
				                        		<span>R$ {{ number_format($orcamento['faturamento'], 2) }}</span>
				                        	@else
				                        		<span>Não definido</span>
				                        	@endif
				                        				
				                        </p>
				                        			
				                    </td>

				                    <td colspan="2">
				                        <p style="color: red">
				                        	Custos :
				                        	@if( is_numeric($orcamento['gastos']))
				                        		<span>R$ {{ number_format($orcamento['gastos'], 2) }}</span>
				                        	@else
				                        		<span>Não definido</span>
				                        	@endif
				                        </p>
				                        			
				                    </td>

				        		</tr>

				        	@endforeach

				        </tbody>

    				</table>

    			@else

    				<span class="help-block">
                        <strong>Nenhum orçamento localizado.</strong>
                    </span>

    			@endif


        	</td>

        </tr>

    </table>

@endsection()