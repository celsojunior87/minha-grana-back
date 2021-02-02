@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Redefinição de senha',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

    <p>Redefina sua senha clicando no botão abaixo.</p>

    @include('beautymail::templates.sunny.contentEnd')

    <tr>
        <td class="w50" width="50"></td>
        <td class="w560" width="560">
            <table class="w560" border="0" cellpadding="0" cellspacing="0" width="560">
                <tbody>
                <tr class="large_only"><td class="w560" height="15" width="560"></td></tr>
                <tr>
                    <td class="w560" width="560">
                        <div class="button-content" align="center">
                            <a href="{{ $link }}" class="button" style="background: #00995A !important;">
                                Recuperar senha
                            </a>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td class="w50" width="50"></td>
    </tr>

@stop
