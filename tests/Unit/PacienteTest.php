<?php

namespace Tests\Unit;

use App\Models\Exame;
use App\Models\Paciente;
use App\Models\User;
use App\Repository\ComorbidadeRepository;
use App\Repository\ExameRepository;
use App\Repository\NutricionistaRepository;
use App\Repository\PacienteRepository;
use App\Repository\UserRepository;
use App\Services\GeradorCPF;
use App\Services\NutricionistaService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class PacienteTest extends TestCase
{
    use WithFaker;

    public function testCriarPaciente()
    {
        $nutricionistaRepository = new NutricionistaRepository();
        $userRepository = new UserRepository();
        $pacienteRepository = new PacienteRepository();

        $dadosCadastro = [
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(), 'cpf' => GeradorCPF::gerarCPF(true),
            'telefone_1' => '(82)98877-6655', 'telefone_2' => '(82)98877-6655',
            'sexo-select' => 'masculino', 'sexo-input' => null, 'obs' => "observações",
            'password' => '12345678',  'password_confirmation' => '12345678', "tipo_usuario" => 3
        ];

        $usuarioPaciente = $userRepository->save($dadosCadastro);
        $nutricionistaPaciente = $nutricionistaRepository->findByColumn("user_id", 2)->first();

        $dadosCadastro['sexo'] = $dadosCadastro['sexo-select'] ?? $dadosCadastro['sexo-input'];
        $dadosCadastro['user_id'] = $usuarioPaciente->id;
        $dadosCadastro['nutricionista_id'] = $nutricionistaPaciente->id;

        $pacienteRepository->save($dadosCadastro);

        $pacienteUsuario = User::where('cpf', $dadosCadastro['cpf'])->first();

        assertEquals($dadosCadastro['cpf'], $pacienteUsuario->cpf);
    }

    public function testEditarPaciente()
    {
        $nutricionistaRepository = new NutricionistaRepository();
        $userRepository = new UserRepository();
        $pacienteRepository = new PacienteRepository();
        $comorbidadeRepository = new ComorbidadeRepository();
        $exameRepository = new ExameRepository();
        $nutriService = new NutricionistaService($pacienteRepository, $nutricionistaRepository, $userRepository, $comorbidadeRepository, $exameRepository);

        $dados = [
            'nome' => "Joaquina da Silva",
            'email' => $this->faker->unique()->safeEmail(), 'cpf' => GeradorCPF::gerarCPF(true),
            'telefone_1' => '(81)98877-6655', 'telefone_2' => '(81)98877-6600',
            'sexo-select' => 'feminino', 'sexo-input' => null, 'obs' => 'Teste edition',
            'password' => '12345678',  'password_confirmation' => '12345678',
        ];

        $nutriService->editar($dados, 3);

        $usuarioPaciente = $pacienteRepository->find(1);

        assertEquals('Joaquina da Silva', $usuarioPaciente->user->nome);
        assertEquals($dados['cpf'], $usuarioPaciente->user->cpf);
        assertEquals($dados['email'], $usuarioPaciente->user->email);
        assertEquals('(81)98877-6655', $usuarioPaciente->user->telefone_1);
        assertEquals('(81)98877-6600', $usuarioPaciente->user->telefone_2);
        assertEquals('Teste edition', $usuarioPaciente->observacoes);
        assertEquals('feminino', $usuarioPaciente->sexo);
    }

    public function testResetarSenhaPaciente()
    {
        $nutricionistaRepository = new NutricionistaRepository();
        $userRepository = new UserRepository();
        $pacienteRepository = new PacienteRepository();
        $comorbidadeRepository = new ComorbidadeRepository();
        $exameRepository = new ExameRepository();
        $nutriService = new NutricionistaService($pacienteRepository, $nutricionistaRepository, $userRepository, $comorbidadeRepository, $exameRepository);

        $dados = [
            'password' => 'Teste123456',  'password_confirmation' => 'Teste123456',
        ];

        $nutriService->atualizarSenha($dados, 3);

        $usuarioPaciente = $userRepository->find(3);
        assertTrue(Hash::check('Teste123456', $usuarioPaciente->password));
    }

    public function testCadastrarExamePaciente(){
        $nutricionistaRepository = new NutricionistaRepository();
        $userRepository = new UserRepository();
        $pacienteRepository = new PacienteRepository();
        $comorbidadeRepository = new ComorbidadeRepository();
        $exameRepository = new ExameRepository();
        $nutriService = new NutricionistaService($pacienteRepository, $nutricionistaRepository, $userRepository, $comorbidadeRepository, $exameRepository);
        $paciente = Paciente::first();
        $dados = [
            'nome' => 'hemoglobina',  'descricao' => '5%',
            'data_realizacao' =>'2022-05-05', 'paciente_id'=>$paciente->id
        ];

        $exame = $nutriService->salvarExamePaciente($dados);
        assertEquals($dados['nome'], $exame->nome);
        assertEquals($dados['descricao'], $exame->descricao);
        assertEquals($dados['data_realizacao'], $exame->data_realizacao);
        assertEquals($dados['paciente_id'], $exame->paciente_id);

        $exameRepository->delete($exame->id);
    }

    public function testEditarExamePaciente(){
        $nutricionistaRepository = new NutricionistaRepository();
        $userRepository = new UserRepository();
        $pacienteRepository = new PacienteRepository();
        $comorbidadeRepository = new ComorbidadeRepository();
        $exameRepository = new ExameRepository();
        $nutriService = new NutricionistaService($pacienteRepository, $nutricionistaRepository, $userRepository, $comorbidadeRepository, $exameRepository);
        $paciente = Paciente::first();
        $dados = [
            'nome' => 'hemoglobina',  'descricao' => '5%',
            'data_realizacao' =>'2022-05-05', 'paciente_id'=>$paciente->id
        ];

        $exame = $nutriService->salvarExamePaciente($dados);

        $dados_edit = [
            'nome' => 'hemoglobina glicada',  'descricao' => '6%',
            'data_realizacao' =>'2022-06-05', 'paciente_id'=>$paciente->id
        ];
        
        $edition = $nutriService->atualizarExamePaciente($dados_edit, $exame->id);

        $exame = $exameRepository->find($exame->id);

        assertTrue($edition);
        assertEquals($dados_edit['nome'], $exame->nome);
        assertEquals($dados_edit['descricao'], $exame->descricao);
        assertEquals($dados_edit['data_realizacao'], $exame->data_realizacao);
        assertEquals($dados_edit['paciente_id'], $exame->paciente_id);

        $exameRepository->delete($exame->id);
    }

    public function testDeletarExamePaciente(){
        $nutricionistaRepository = new NutricionistaRepository();
        $userRepository = new UserRepository();
        $pacienteRepository = new PacienteRepository();
        $comorbidadeRepository = new ComorbidadeRepository();
        $exameRepository = new ExameRepository();
        $nutriService = new NutricionistaService($pacienteRepository, $nutricionistaRepository, $userRepository, $comorbidadeRepository, $exameRepository);
        $paciente = Paciente::first();
        $dados = [
            'nome' => 'hemoglobina',  'descricao' => '5%',
            'data_realizacao' =>'2022-05-05', 'paciente_id'=>$paciente->id
        ];

        $exame = $nutriService->salvarExamePaciente($dados);

        $delete = $nutriService->deletarExamePaciente($exame->id);

        assertEquals(1, $delete);
    }

    public function testRecuperarExamePaciente(){
        $nutricionistaRepository = new NutricionistaRepository();
        $userRepository = new UserRepository();
        $pacienteRepository = new PacienteRepository();
        $comorbidadeRepository = new ComorbidadeRepository();
        $exameRepository = new ExameRepository();
        $nutriService = new NutricionistaService($pacienteRepository, $nutricionistaRepository, $userRepository, $comorbidadeRepository, $exameRepository);
        $paciente = Paciente::first();
        $dados = [
            'nome' => 'hemoglobina',  'descricao' => '5%',
            'data_realizacao' =>'2022-05-05', 'paciente_id'=>$paciente->id
        ];

        $exame = $nutriService->salvarExamePaciente($dados);

        $exame_recuperado = $nutriService->recuperarExame($exame->id);

        assertEquals($exame_recuperado->id, $exame->id);
        assertEquals($exame_recuperado->nome, $exame->nome);
        assertEquals($exame_recuperado->descricao, $exame->descricao);
        assertEquals($exame_recuperado->data_realizacao, $exame->data_realizacao);
        assertEquals($exame_recuperado->paciente_id, $exame->paciente_id);
        

        $exameRepository->delete($exame->id);
    }
}
