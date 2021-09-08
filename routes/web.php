<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/teste-boleto', function () {

    // return Storage::url('images/Logo-POM-Escudo.png');

    $beneficiario = new \Eduardokum\LaravelBoleto\Pessoa;

    $beneficiario->setDocumento('50.668.441/0001-34')
        ->setNome('PONTIFÍCIAS OBRAS MISSIONÁRIAS')
        ->setCep('70790-050')
        ->setEndereco('SGAN 905, S/N')
        ->setBairro('Asa Sul')
        ->setUf('DF')
        ->setCidade('Brasília');


    $pagador = new \Eduardokum\LaravelBoleto\Pessoa;
    $pagador->setDocumento('042.248.611-60')
        ->setNome('Darlan Thiago Costa')
        ->setCep('73813-540')
        ->setEndereco('Travessa Benedito Galvão, N29')
        ->setBairro('Formosinha')
        ->setUf('GO')
        ->setCidade('Formosa');


    $unique = date("Y") . date('m')  . random_int(1, 9) . random_int(1, 9);

    $boleto = new Eduardokum\LaravelBoleto\Boleto\Banco\Itau(
        [
            'logo'                   => public_path('storage/images/Webp.net-resizeimage.png'),
            'dataVencimento'         => Carbon::now()->addDays(4),
            'valor'                  => 2.50,
            'multa'                  => false,
            'juros'                  => false,
            'numero'                 => $unique,
            'numeroDocumento'        => $unique,
            'numeroControle'         => $unique,
            'pagador'                => $pagador,
            'beneficiario'           => $beneficiario,
            'carteira'               => 109,
            'agencia'                => '0522',
            'conta'                  => 543008,
            'descricaoDemonstrativo' => [],
            'instrucoes'             => ['Não receber após o vencimento.'],
            'aceite'                 => 'N',
            'quantidade'             => 1,
        ]
    );

    $pdf = new Eduardokum\LaravelBoleto\Boleto\Render\Pdf();

    $pdf->addBoleto($boleto);

    $pdf->gerarBoleto($pdf::OUTPUT_SAVE, base_path() . '/arquivos/' . Str::uuid() . '.pdf');
    // $pdf->gerarBoleto($pdf::OUTPUT_DOWNLOAD, null, Str::uuid());
    // $pdf->gerarBoleto();
});


Route::get('/teste-remessa', function () {

    $beneficiario = new \Eduardokum\LaravelBoleto\Pessoa;

    $beneficiario->setDocumento('50.668.441/0001-34')
        ->setNome('PONTIFÍCIAS OBRAS MISSIONÁRIAS')
        ->setCep('70790-050')
        ->setEndereco('SGAN 905, S/N')
        ->setBairro('Asa Sul')
        ->setUf('DF')
        ->setCidade('Brasília');


    $pagador = new \Eduardokum\LaravelBoleto\Pessoa;
    $pagador->setDocumento('042.248.611-60')
        ->setNome('Darlan Thiago Costa')
        ->setCep('73813-540')
        ->setEndereco('Travessa Benedito Galvão, N29')
        ->setBairro('Formosinha')
        ->setUf('GO')
        ->setCidade('Formosa');


    $unique = str_pad(date("Y") . mt_rand(0, 9999), 8, '0', STR_PAD_RIGHT);

    $boleto = new Eduardokum\LaravelBoleto\Boleto\Banco\Itau(
        [
            'logo'                   => public_path('storage/images/Webp.net-resizeimage.png'),
            'dataVencimento'         => Carbon::now()->addDays(4),
            'valor'                  => 2,
            'multa'                  => false,
            'juros'                  => false,
            'numero'                 => $unique,
            'numeroDocumento'        => $unique,
            'numeroControle'         => $unique,
            'pagador'                => $pagador,
            'beneficiario'           => $beneficiario,
            'carteira'               => 109,
            'agencia'                => '0522',
            'conta'                  => 543008,
            'descricaoDemonstrativo' => [],
            'instrucoes'             => ['Não receber após o vencimento.'],
            'aceite'                 => 'N',
            'quantidade'             => 1,
        ]
    );

    $pdf = new Eduardokum\LaravelBoleto\Boleto\Render\Pdf();

    $pdf->addBoleto($boleto);

    $pdf->gerarBoleto($pdf::OUTPUT_SAVE, base_path() . '/arquivos/' . Str::uuid() . '.pdf');

    $remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Itau(
        [
            'agencia'      => '0522',
            'conta'        => 54300,
            'contaDv'      => 8,
            'carteira'     => 109,
            'beneficiario' => $beneficiario,
        ]
    );

    $remessa->addBoleto($boleto);

    // echo $remessa->save(__DIR__ . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . 'itau.txt');

    // echo $remessa->gerar();
    // $remessa->download();
    $remessa->save(base_path() . '/arquivos/' . Str::uuid() . '.txt');
});

Route::get('/teste-retorno', function () {

    $filePathPOM = 'arquivos/CN25081A.RET';

    // $path = base_path('vendor/eduardokum/laravel-boleto/exemplos/arquivos/itau.ret');
    $path = base_path($filePathPOM);

    $retorno = \Eduardokum\LaravelBoleto\Cnab\Retorno\Factory::make($path);

    // dd($retorno);

    $retorno->processar();

    // echo $retorno->getBancoNome();

    // dd($retorno->getDetalhes());

    // echo $retorno->getCodigoBanco();

    $array = [];

    // // To iterate do:
    foreach ($retorno->getDetalhes() as $object) {

        // echo '<pre>';

        // echo json_encode($object->toArray());

        array_push($array, $object->toArray());

        // echo '</pre>';
    }


    return $array;

    // echo json_encode($retorno->getDetalhes());

});

function randomNossoNumero()
{

    return (int) date("Y") . date('m')  . mt_rand(0, 99);
}
