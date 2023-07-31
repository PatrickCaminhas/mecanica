<?php
require_once 'conexao.php';

     function agendarManutencao($placa, $data_manutencao)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $placa = $_POST["placa"];
            $data_manutencao = $_POST["data_manutencao"];
            $descricao = $_POST["descricao"];
            // Realize o agendamento da manutenção no banco de dados
            $conn = conectarBancoDados();

            // Aqui você pode inserir os dados do agendamento na tabela de manutenções (por exemplo, "manutencoes") 
            // e atualizar o campo "estado_do_veiculo" na tabela de veículos para refletir que o veículo está em manutenção.
            // Exemplo:
            // 1. Inserir o agendamento na tabela "manutencoes": 

            $sql_atualizar_estado = "UPDATE veiculos SET estado_do_veiculo = 'Entrega agendada para o dia: $data_manutencao' WHERE placa = '$placa'";
            $conn->query($sql_atualizar_estado);


            $sql_agendamento = "INSERT INTO manutencoes (placa, data_manutencao, estado_do_veiculo) VALUES ('$placa', '$data_manutencao', 'Entrega agendada para o dia: $data_manutencao')";
            $conn->query($sql_agendamento);

            // 2. Atualizar o campo "estado_do_veiculo" na tabela "veiculos" para refletir que o veículo está em manutenção.


            $conn->close();
            echo '<script>alert("Manutenção agendada com sucesso!"); window.location.href = "../cliente/painel.php";</script>';

        }
    }

    function cancelarAgendamento($placa){

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $placa = $_POST["placa"];
        
            // Realize o agendamento da manutenção no banco de dados
            $conn = conectarBancoDados();
        
            // Atualizar o campo "estado_do_veiculo" na tabela "veiculos" para refletir que o veículo está com proprietário
           
        
            // Consultar a data de agendamento da manutenção
            $sql_consulta_data = "SELECT data_manutencao FROM manutencoes WHERE placa = '$placa' AND estado_do_veiculo LIKE 'Entrega agendada para o dia:%'";
            $resultado_datas = $conn->query($sql_consulta_data);
            
            // Verificar se encontrou uma data
            if ($resultado_datas->num_rows > 0) {
                // Extrair a data da primeira linha do resultado
                $row = $resultado_datas->fetch_assoc();
                $data_manutencao = $row['data_manutencao'];
        
                // Deletar o agendamento da manutenção
                $sql_agendamento = "DELETE FROM manutencoes WHERE placa = '$placa' AND data_manutencao = '$data_manutencao'";
                $conn->query($sql_agendamento);
                $sql_atualizar_estado = "UPDATE veiculos SET estado_do_veiculo = 'Com proprietário' WHERE placa = '$placa'";
                $conn->query($sql_atualizar_estado);
                echo '<script>alert("Agendamento de manutenção cancelada!"); window.location.href = "../cliente/painel.php";</script>';
            } else {
                echo '<script>alert("Não foi encontrado nenhum agendamento para o veículo."); window.location.href = "../cliente/painel.php";</script>';
            }
        
            // Fechar a conexão com o banco de dados
            $conn->close();
        }

    }

 
    function finalizarManutencao($placa){
       
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $placa = $_POST["placa"];
        
            // Realize o agendamento da manutenção no banco de dados
            $conn = conectarBancoDados();
        
            // Atualizar o campo "estado_do_veiculo" na tabela "veiculos" para refletir que o veículo está com proprietário
           
        
            // Consultar a data de agendamento da manutenção
            $sql_consulta_data = "SELECT data_manutencao FROM manutencoes WHERE placa = '$placa' AND estado_do_veiculo = 'Manutenção concluída'";
            $resultado_datas = $conn->query($sql_consulta_data);
            
            // Verificar se encontrou uma data
            if ($resultado_datas->num_rows > 0) {
                // Extrair a data da primeira linha do resultado
                $row = $resultado_datas->fetch_assoc();
                $data_manutencao = $row['data_manutencao'];
        
                // Deletar o agendamento da manutenção
                $sql_agendamento = "UPDATE manutencoes SET estado_do_veiculo = 'Entregue ao proprietario' WHERE placa = '$placa' AND data_manutencao = '$data_manutencao'";
                $conn->query($sql_agendamento);
                $sql_atualizar_estado = "UPDATE veiculos SET estado_do_veiculo = 'Com proprietário' WHERE placa = '$placa'";
                $conn->query($sql_atualizar_estado);
                echo '<script>alert("Veiculo entregue ao cliente!"); window.location.href = "../funcionario/listarVeiculos.php";</script>';

            } else {
                echo '<script>alert("Não foi possivel entregar o veiculo ao cliente!\nContate o suporte"); window.location.href = "../funcionario/listarVeiculos.php";</script>';

            }
        
            // Fechar a conexão com o banco de dados
            $conn->close();
        }

    }


if (isset($_GET["funcao"])) {
    $funcao = $_GET["funcao"];

    switch ($funcao) {
        case "agendaManutencao":
            agendarManutencao($_POST["placa"], $_POST["data_manutencao"]);
            break;
        case "cancelarAgendamento":
            cancelarAgendamento($_POST["placa"]);
            break;
        case "finalizarManutencao":
            finalizarManutencao($_POST["placa"]);
            break;
        // Adicione mais cases para outras funções
        default:
            // Função não encontrada
            echo "Função não encontrada.";
            header("Location: ../cliente/painel.php");
    }
}
?>