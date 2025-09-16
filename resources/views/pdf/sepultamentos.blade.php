<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Sepultamentos</title>
</head>
<body>
    <h1>Relatório de Sepultamentos</h1>
    @if ($sepultamentos->isEmpty())
        <p>Nenhum sepultamento encontrado.</p>
    @else
        <ul>
            @foreach ($sepultamentos as $sepultamento)
                <li>{{ $sepultamento->nome_falecido ?? 'Nome não informado' }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>