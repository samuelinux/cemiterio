<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Sepultamentos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Relatório de Sepultamentos</h1>
    <h2>{{ session('sepultamentos_empresa.nome') ?? 'Empresa não informada' }}</h2>
    <table>
        <thead>
            <tr>
                <th>Nome do Falecido</th>
                
                <th>Quadra</th>
                <th>Fila</th>
                <th>Cova</th>
                <th>OS</th>
                <th>Data de Falecimento</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach (session('sepultamentos_pdf_data', []) as $sepultamento)
                <tr>
                    <td>{{ $sepultamento['nome_falecido'] }}</td>
                   
                    <td>{{ $sepultamento['quadra'] }}</td>
                    <td>{{ $sepultamento['fila'] }}</td>
                    <td>{{ $sepultamento['cova'] }}</td>
                    <td>{{ $sepultamento['ordem_sepultamento'] }}</td>
                    <td>{{ $sepultamento['data_falecimento'] }}</td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>