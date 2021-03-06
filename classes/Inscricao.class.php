<?php
session_start();

require_once "util/Util.php";
$inscricao = new Inscricao();
/**
 * Created by PhpStorm.
 * User: Carina
 * Date: 11/10/2016
 * Time: 21:01
 */
 
 $acao = (isset($_GET['acao'])) ? $_GET['acao'] : 'listar';
 
 switch($acao) {
    case 'inserir': { $inscricao->Insert(); break;}
	default: break;	
}
class Inscricao
{
    private $bd;

    function __construct(){
        $this->bd = new BDConnection();
    }

    public function Insert() {
		if(!isset($_POST['token']) || trim($_POST['token']) == "")
			respostaJsonErro("token não encontrado");

        $token = $_POST['token'];
		
		if(!isset($_SESSION['cod_usuario']) || trim($_SESSION['cod_usuario']) == "") // verifica se o que ta inputando tá de acordo com oopadrão
            respostaJsonErro("Usuario não autenticado!");// respostaJason ( metodo do tarley
		
		$codAluno = $_SESSION['cod_usuario'];
		
        try{
			$conn = $this->bd->getConnection();// faz a conecção
            $sqlConsulta = 'SELECT cod_atividade FROM tb_atividade WHERE token = :token';
           
            $stmConsulta = $conn->prepare($sqlConsulta);
            $stmConsulta->bindParam(':token', $token);
            $stmConsulta->execute();
			
			if($stmConsulta->rowCount() == 0){ // se não retornar nenhuma linha 
                respostaJsonErro("Token não encontrado!");
            }
			
			$row = $stmConsulta->fetch(PDO::FETCH_ASSOC);
			$codatividade = $row['cod_atividade'];     
        
        
            $sql = 'INSERT INTO tb_inscricao (data_inscricao, situacao, cod_aluno, cod_atividade)	
					VALUES (now(), 1, :cod_aluno, :cod_atividade)';

            
            $stm = $conn->prepare($sql); // prepar e checa parametros
            $stm->bindParam(':cod_aluno', $codAluno);
			$stm->bindParam(':cod_atividade', $codatividade);
			
            $stm->execute();// executa

            if($stm->rowCount() > 0){ // se inserir algo maior 	que 1 linha vai dá ok 
                respostaJsonSucesso("Inscrição realizada com sucesso!");
            } else {
                respostaJsonErro("Inscrição não realizada!");
            }
        }catch (PDOException $e){
            respostaJsonExcecao($e);
        }finally {
            $this->bd->close();
        }
	}
	
	
    function EncerrarInscricao($codInscricao){

    }

    /**
     * Retorna os dados de uma inscrição a partir do seu codigo
     */
    function getDados($codInscricao){

    }
}
