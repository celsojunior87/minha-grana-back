<?php


namespace App\Services;


use App\Helper\Color;
use App\Helper\Number;
use App\Models\ItemMovimentacao;
use App\Models\Status;
use App\Models\TipoGrupo;
use App\Repositories\GrupoRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class GrupoService extends AbstractService
{
    /**
     * @var GrupoRepository
     */
    protected $repository;

    /**
     * @var TipoGrupoService
     */
    protected $tipoGrupoService;

    /**
     * @var ItemService
     */
    public $itemService;

    /**
     * @var ItemMovimentacaoService
     */
    public $itemMovimentacaoService;

    public function __construct(GrupoRepository $repository,
                                TipoGrupoService $tipoGrupoService,
                                ItemService $itemService,
                                ItemMovimentacaoService $itemMovimentacaoService
    )
    {
        $this->repository = $repository;
        $this->tipoGrupoService = $tipoGrupoService;
        $this->itemService = $itemService;
        $this->itemMovimentacaoService = $itemMovimentacaoService;
    }

    public function getAll($params = null, $with = [])
    {
        $with = ['items', 'tipoGrupo', 'items.itemMovimentacao'];
        $grupos = parent::getAll($params, $with)->toArray();
        foreach ($grupos as $key => $grupo) {
            $total_vl_esperado = 0;
            $total_vl_planeje = 0;
            $total_vl_recebido = 0;
            if (isset($grupo['items'])) {
                foreach ($grupo['items'] as $keyItems => $item) {
                    $grupos[$key]['items'][$keyItems]['vl_recebido'] = $this->somatoriaValorRealizadoItem($item);
                    $grupos[$key]['items'][$keyItems]['vl_planeje'] = $this->calculaPlaneje($item);
                    if($grupos[$key]['items'][$keyItems]['vl_planeje'] < 0){
                        $grupos[$key]['items'][$keyItems]['class_vl_planeje'] = 'item_vl valor_negativo';
                    }
                    if($grupos[$key]['items'][$keyItems]['vl_planeje'] == 0){
                        $grupos[$key]['items'][$keyItems]['class_vl_planeje'] ='item_vl valor_normal';
                    }
                    if($grupos[$key]['items'][$keyItems]['vl_planeje'] > 0){
                        $grupos[$key]['items'][$keyItems]['class_vl_planeje'] = 'item_vl valor_positivo';
                    }
                    $grupos[$key]['items'][$keyItems]['vl_gasto'] = $this->somatoriaValorRealizadoItem($item);
                    $total_vl_esperado += $item['vl_esperado'];
                    $total_vl_planeje += $grupos[$key]['items'][$keyItems]['vl_planeje'];
                    $total_vl_recebido += $grupos[$key]['items'][$keyItems]['vl_recebido'];
                }
            }
            $grupos[$key]['total_vl_esperado'] = $total_vl_esperado;
            $grupos[$key]['total_vl_planeje'] = $total_vl_planeje;
            $grupos[$key]['total_vl_recebido'] = $total_vl_recebido;
        };
        return $grupos;
    }

    //; ? 'item_vl valor_negativo' : 'item_vl valor_positivo';
    /**
     *  O valor esperado menos a soma do planejado
     */
    public function calculaPlaneje($item)
    {
        $vl_planeje = 0;
        $vl_planejado = 0;

        foreach ($item['item_movimentacao'] as $movimentacao) {
            $vl_planejado += $movimentacao['vl_planejado'];
        }

        $vl_planeje = $item['vl_esperado'] - $vl_planejado;
        if (empty($item['item_movimentacao'])) {

            $vl_planeje = 0;
        }
        return $vl_planeje;
    }

    public function somatoriaValorRealizadoItem($item)
    {
        $objItem = $this->itemService->getRepository()->find($item['id']);
        $movimentacoes = $objItem->itemMovimentacao()->get()->toArray();

        $vl_recebido = 0;
        foreach ($movimentacoes as $movimentacoe) {
            $vl_recebido += $movimentacoe['vl_realizado'];
        }
        return $vl_recebido;
    }

    public function movimentacao($params)
    {
        $grupos = $this->repository->movimentacao($params);
        return $this->getMovimentacaoByGrupos($grupos);
    }

    public function frases($params)
    {
        $grupos = $this->getAll($params);

        $totalMovimentacaoSaldoEsperado = $this->getMovimentacaoSaldoEsperado($grupos);

        $totalMovimentacaoReceitas = $this->somaTotalReceitaMovimentacao($grupos);
        $totalMovimentacaoDespesa = $this->somaTotalDespesaMovimentacao($grupos);


        $totalReceita = $this->getTotalReceitas($grupos);
        $totalDespesa = $this->getTotalDespesa($grupos);


        if ($totalReceita == 0) {
            return [
                'frase' => 'Comece adicionando todas as suas receitas . ',
                'color' => '#6FCF97',
                'class' => 'frase_inicial'
            ];
        }



        if ($totalReceita != 0 && $totalDespesa == 0) {

            return [
                'frase' => 'Quando terminar de adicionar as suas receitas, comece a adicionar as suas despesas.',
            ];
        }

        if ($totalReceita == $totalDespesa && $totalMovimentacaoReceitas == 0 && $totalMovimentacaoDespesa == 0) {

            return [
                'frase' => "<span>
                    Bom trabalho! Agora adicione todos seus itens à movimentação. Especifique o dia e o valor.  <b style=' font-weight: bold'>Dica</b>: comece pelas receitas.
                    </span>"
            ];
        }

        if($totalReceita == $totalDespesa && $totalMovimentacaoReceitas == 0 && $totalMovimentacaoDespesa > 0){

            return [
                'frase'=>'Se você começar pelas suas despesas, o seu orçamento vai ficar negativo. Que tal começar incluindo as suas receitas?'
            ];
        }

        if($totalReceita == $totalDespesa && $totalMovimentacaoReceitas > 0 && $totalMovimentacaoReceitas
            < $totalReceita && $totalMovimentacaoSaldoEsperado == 0)
        {
            $total = $totalReceita - $totalMovimentacaoReceitas;

            $caminhoCerto = "<span>Você está no caminho certo! Adicione mais receitas à movimentação. <br>Você ainda tem <b style=' font-weight: bold'>" . Number::formatCurrencyBr($total, false, false) . "</b> 
                        para adicionar </span>";
            return [
              'frase' => $caminhoCerto
            ];
        }

        if($totalReceita == $totalDespesa && $totalMovimentacaoReceitas > $totalReceita){

            $total = $totalMovimentacaoReceitas - $totalReceita;
            $retorno = "<span>Cuidado! Você adicionou <b style=' font-weight: bold'>" . Number::formatCurrencyBr($total, false, false) . "</b> 
                        a mais na sua movimentação do que o planejado para as suas receitas. Volte ao Planejamento e procure por valores negativos na coluna Planeje. </span>";
            return [
              'frase' => $retorno
            ];
        }

        if($totalReceita == $totalDespesa && $totalMovimentacaoDespesa > $totalDespesa)
        {
            $total =  $totalMovimentacaoDespesa - $totalDespesa;
            $retorno = "<span>Cuidado! Você adicionou <b style=' font-weight: bold'>" . Number::formatCurrencyBr($total, false, false) . "</b> 
                        a mais na sua movimentação do que o planejado para as suas despesas. Volte ao Planejamento e procure por valores negativos na coluna Planeje. </span>";
            return [
                'frase' => $retorno
                ];
        }

        if ($totalReceita == $totalDespesa && $totalMovimentacaoSaldoEsperado < 0) {
            return [
                'frase' => 'Você adicionou as suas despesas, mas as receitas ainda estão insuficientes. Tente adicionar mais receitas antes de continuar.',
            ];
        }

        if($totalReceita == $totalDespesa && $totalMovimentacaoReceitas > 0
            && $totalMovimentacaoReceitas < $totalReceita && $totalMovimentacaoSaldoEsperado != 0){

            $total = $totalMovimentacaoSaldoEsperado;

            $retorno = "<span>Legal! Você adicionou receitas à sua movimentação.
                Agora especifique como você quer usar esse dinheiro.
                Você tem <b style=' font-weight: bold'>" . Number::formatCurrencyBr($total, false, false) . "</b> 
                para usar.</span>";
            return [
                'frase'=> $retorno
            ];
        }

        if($totalReceita == $totalDespesa && $totalMovimentacaoReceitas == $totalReceita &&
            $totalMovimentacaoDespesa < $totalDespesa)
        {
            $total = $totalMovimentacaoSaldoEsperado;
            $retorno = "<span>Agora que você adicionou todas suas receitas à movimentação, insira também todas as suas despesas..<br> Você tem <b style=' font-weight: bold'>" . Number::formatCurrencyBr($total, false, false) . "</b> 
                       de despesas para adicionar.</span>";
            return [
                'frase' => $retorno
            ];
        }



        if ($totalReceita > $totalDespesa && $totalDespesa > 0) {

            $total = $totalReceita - $totalDespesa;
            $planejar = "<span>Você ainda tem <b style=' font-weight: bold'>" . Number::formatCurrencyBr($total, false, false) . "</b> 
                        para planejar. Continue adicionando despesas. </span>";
            return [
                'frase' => $planejar,

            ];
        }


        if ($totalReceita < $totalDespesa) {
            $total = $totalReceita - $totalDespesa;

            $frase = "<span>Oops! Você cadastrou <span style='color: red; font-weight: bold'>" . Number::formatCurrencyBr($total, false, false) . "</span> a mais do que a sua receita. 
                        Adicione mais receitas ou reduza as suas despesas para que o seu orçamento seja igual a zero </span>";

            return [
                'frase' => $frase,
                'color' => '#F57077',
                'class' => 'frase_ultrapassou',
            ];
        }

        if ($totalMovimentacaoReceitas > $totalReceita) {
            $total = $totalMovimentacaoReceitas - $totalReceita;
            return [
                'total' => $total,
                'frase' => 'Oops, você planejou  a mais! Ajuste suas receitas ou suas despesas até seu orçamento ser igual a zero. ',
            ];
        }


        if ($totalReceita == $totalDespesa && $totalMovimentacaoSaldoEsperado < 0) {
            return [
                'frase' => 'Oops! Você planejou  a mais. Ajuste suas receitas ou suas despesas até seu orçamento ser igual a zero.',
                'total' => $totalMovimentacaoSaldoEsperado * -1,
                'color' => '#F57077',
                'class' => 'frase_ultrapassou'
            ];
        }


        if ($totalReceita == 0) {
            return [
                'frase' => 'Comece adicionando todas as suas receitas . ',
                'color' => '#6FCF97',
                'class' => 'frase_inicial'
            ];
        }



        if ($totalReceita == $totalDespesa && $totalMovimentacaoReceitas == $totalReceita && $totalMovimentacaoDespesa == $totalDespesa && $totalMovimentacaoSaldoEsperado == 0) {
            return [
                'frase' => 'Parabéns, seu orçamento deste mês está completo!',
                'class' => 'frase_parabens'
            ];
        }


    }

    public function getMovimentacaoSaldoEsperado($grupos)
    {
        $movimentacoes = $this->getMovimentacaoByGrupos($grupos);

        if (!empty($movimentacoes)) {
            $ultimoElemento = end($movimentacoes);
            return $ultimoElemento['vl_saldo_esperado'];
        }

        return 0;
    }

    public function somaTotalReceitaMovimentacao($grupos)
    {
        $somaReceitaMovimentacao = 0;
        foreach ($grupos as $grupo) {
            if ($grupo['tipo_grupo']['id'] == TipoGrupo::RECEITAS) {
                foreach ($grupo['items'] as $item) {
                    foreach ($item['item_movimentacao'] as $movimentacao) {
                        $somaReceitaMovimentacao += $movimentacao['vl_planejado'];
                    }
                }
            }
        }
        return $somaReceitaMovimentacao;
    }

    public function somaTotalDespesaMovimentacao($grupos)
    {
        $somaDespesaMovimentacao = 0;
        foreach ($grupos as $grupo) {
            if ($grupo['tipo_grupo']['id'] == TipoGrupo::DESPESAS) {
                foreach ($grupo['items'] as $item) {
                    foreach ($item['item_movimentacao'] as $movimentacao) {
                        $somaDespesaMovimentacao += $movimentacao['vl_planejado'];
                    }
                }
            }
        }
        return $somaDespesaMovimentacao;
    }


    public function getTotalDespesa($grupos)
    {

        $totalDespesa = 0;
        foreach ($grupos as $grupo) {
            if ($grupo['tipo_grupo']['id'] == TipoGrupo::DESPESAS) {
                $totalDespesa += $grupo ['total_vl_esperado'];
            }
        }
        return $totalDespesa;
    }

    public function getTotalReceitas($grupos)
    {
        $totalReceita = 0;
        foreach ($grupos as $grupo) {
            if ($grupo['tipo_grupo']['id'] == TipoGrupo::RECEITAS) {
                $totalReceita += $grupo['total_vl_esperado'];
            }
        }
        return $totalReceita;
    }


    /**
     * Buscar movimentacoes por grupo
     * @param $grupos
     * @return mixed
     * @throws \Exception
     */
    public function getMovimentacaoByGrupos($grupos)
    {
        $arrItemsMovimentacao = $this->buscarGruposComMovimentacoesPorGrupos($grupos);

        /**
         * Ordenacao por movimentacoes
         */
        array_multisort(array_column($arrItemsMovimentacao, "ordenacao"),
            SORT_NUMERIC, $arrItemsMovimentacao);

        $arrItemsMovimentacao = $this->calcularSaldoEsperadoPorGruposComMovimentacoes($arrItemsMovimentacao);
        $arrItemsMovimentacao = $this->calcularStatusPorGruposComMovimentacoes($arrItemsMovimentacao);
        return $arrItemsMovimentacao;
    }

    /**
     * Buscar grupos com movimentacoes por grupo
     * @param $grupos
     */
    public function buscarGruposComMovimentacoesPorGrupos($grupos)
    {
        $arrItemsMovimentacao = [];
        foreach ($grupos as $key => $grupo) {
            foreach ($grupo['items'] as $item) {
                foreach ($item['item_movimentacao'] as $movimentacao) {
                    $arrItem['item_id'] = $item['id'];
                    $arrItem['ordenacao'] = $movimentacao['ordenacao'];
                    $arrItem['data'] = $movimentacao['data'];
                    $arrItem['data_formatted'] = $movimentacao['data_formatted'];
                    $arrItem['movimentacao_id'] = $movimentacao['id'];
                    $arrItem['nome'] = $item['nome'];
                    $arrItem['vl_planejado'] = $movimentacao['vl_planejado'];
                    $arrItem['vl_realizado'] = $movimentacao['vl_realizado'];
                    $arrItemsMovimentacao[] = $arrItem;
                }
            }
        }
        return $arrItemsMovimentacao;
    }

    /**
     * Metodo que manda calcular o saldo esperado
     * @param $arrItemsMovimentacao
     * @return mixed
     * @throws \Exception
     */
    public function calcularSaldoEsperadoPorGruposComMovimentacoes($arrItemsMovimentacao)
    {

        if (is_array($arrItemsMovimentacao)) {
            foreach ($arrItemsMovimentacao as $key => $itemsMovimentacao) {
                $itemMovimentacao = $this->itemMovimentacaoService->find($itemsMovimentacao['movimentacao_id']);
                $itemMovimentacaoAnterior = isset($arrItemsMovimentacao[$key - 1]) ? $arrItemsMovimentacao[$key - 1] : 0;
                $arrItemsMovimentacao[$key]['vl_saldo_esperado'] =
                    $this->calculaSaldoEsperado($itemMovimentacao, $itemMovimentacaoAnterior, $key);
                $item = $this->itemService->find($itemsMovimentacao['item_id'])->toArray();
                $grupo = $this->find($item['grupo_id']);
                $arrItemsMovimentacao[$key]['tipo_grupo_id'] = $grupo->tipo_grupo_id;
            }
        }
        return $arrItemsMovimentacao;
    }

    /**
     * @param $arrItemsMovimentacao
     * @return mixed
     */
    public function calcularStatusPorGruposComMovimentacoes($arrItemsMovimentacao)
    {
        if (is_array($arrItemsMovimentacao)) {
            foreach ($arrItemsMovimentacao as $key => $item) {
                $arrItemsMovimentacao[$key]['status'] = $this->fazerCalculoStatus($item);
            }
        }

        return $arrItemsMovimentacao;
    }

    /**
     * se o valor realizado for igual a 0 o saldo esperado deverá ser igual ao planejado
     */
    public function calculaSaldoEsperado(ItemMovimentacao $itemMovimentacao, $arrItemsMovimentacaoAnterior, $key)
    {
        if ($key == 0) {
            if ($itemMovimentacao->vl_realizado == '0.00') {
                if($itemMovimentacao->item()->first()->grupo()->first()->tipoGrupo()->first()->id == TipoGrupo::RECEITAS) {
                    return $itemMovimentacao->vl_planejado;
                }
                //Se for despesa
                return -abs($itemMovimentacao->vl_planejado);
            }
            if($itemMovimentacao->item()->first()->grupo()->first()->tipoGrupo()->first()->id == TipoGrupo::RECEITAS){
                return $itemMovimentacao->vl_realizado;
            }
            //Se for despesa
            return -abs($itemMovimentacao->vl_realizado);
        }

        if ($itemMovimentacao->item()->first()->grupo()->first()->tipoGrupo()->first()->id == TipoGrupo::RECEITAS) {
            if ($itemMovimentacao->vl_realizado == '0.00') {
                return $arrItemsMovimentacaoAnterior['vl_saldo_esperado'] + $itemMovimentacao->vl_planejado;
            }
            return $arrItemsMovimentacaoAnterior['vl_saldo_esperado'] + $itemMovimentacao->vl_realizado;
        }
        if ($itemMovimentacao->item()->first()->grupo()->first()->tipoGrupo()->first()->id == TipoGrupo::DESPESAS) {
            if ($itemMovimentacao->vl_realizado == '0.00') {
                return $arrItemsMovimentacaoAnterior['vl_saldo_esperado'] - $itemMovimentacao->vl_planejado;
            }
            return $arrItemsMovimentacaoAnterior['vl_saldo_esperado'] - $itemMovimentacao->vl_realizado;
        }

    }

    /**
     *
     *  Cálculo status
     *  STATUS: Seguir as seguintes regras:
     *  SE PLANEJADO <> REALIZADO ENTÃO “Ajuste”
     *  SE PLANEJADO = REALIZADO ENTÃO “Feito”
     *  SE PLANEJADO <> 0 & REALIZADO = ENTÃO “Aguardando”
     *  SE PLANEJADO = 0 & REALIZADO = 0 ENTÃO null
     *
     * @param $item
     * @return array|string[]
     */
    public function fazerCalculoStatus($item)
    {
        $valorRealizado = Arr::get($item, 'vl_realizado');
        $valorPlanejado = Arr::get($item, 'vl_planejado');

        if ($valorRealizado == '0.00' && $valorPlanejado !== $valorRealizado) {
            return [
                'id' => Status::AGUARDANDO,
                'nome' => Status::find(Status::AGUARDANDO)->nome,
                'color' => 'orange',
                'text_color' => 'white'
            ];
        }

        if ($valorPlanejado !== $valorRealizado) {
            return [
                'id' => Status::AJUSTE,
                'nome' => Status::find(Status::AJUSTE)->nome,
                'color' => 'secondary',
                'text_color' => 'white'
            ];
        }

        if ($valorPlanejado === $valorRealizado && $valorRealizado != 0) {
            return [
                'id' => Status::FEITO,
                'nome' => Status::find(Status::FEITO)->nome,
                'color' => '#6FCF97',
                'text_color' => 'white'
            ];
        }
        return ['nome' => '', 'color' => 'default', 'text_color' => ''];
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        $data['user_id'] = Auth::user()->id;
        $data['data'] = (!isset($data['date'])) ? Carbon::now() : $data['date'];
        $data['color'] = $this->definirCor($data);
        return parent::save($data);
    }

    public function definirCor($params)
    {
        $search = [
            'user_id' => $params['user_id'],
            'date' => $params['data']
        ];
        $grupos = $this->getAll($search);
        $key = 0;
        if(count($grupos) > 0) {
            $key = count($grupos) - 1;
            if(count($grupos) > 10) {
                $key = count($grupos) - 11;
            }
        }
        $cor = Color::buscarCores()[$key];
        return $cor;
    }

    /**
     * @return mixed
     */
    public function preRequisite()
    {
        $arr['tipo_grupo'] = generateSelectOption($this->tipoGrupoService->getRepository()->list('id'));
        return $arr;
    }

    /**
     * Cria um mês, quando o mesmo está em branco
     * @param $params
     * @return mixed|void
     * @throws \Exception
     */
    public function criarMes($params)
    {
        if (empty($params['date'])) {
            throw new \Exception('A data é obrigatória');
        }

        $grupos = $this->getAll($params);

        /**
         * Verifica se existe algo no mes atual
         */
        if (empty($grupos)) {

            /**
             * Faz a mesma consulta, baseada no mes anterior
             */
            $date = Arr::get($params, 'date');

            $mesAnterior = Carbon::createFromFormat('Y-m', $date)->subMonth(1)->format('Y-m');

            $gruposMesAnterior = $this->getAll(['date' => $mesAnterior]);

            /**
             * Se não existir nada no mês anterior, então cria um novo
             */
            if (empty($gruposMesAnterior)) {
                $this->criarGruposDefault($date);
            } else {
                /**
                 * Se existir, copia os itens do mês anterior.
                 */
                return $this->criarEspelhoMesAnterior($mesAnterior, $date);
            }
        }
    }

    /**
     * Cria exatamente o espelho do mês anterior para o mês atual
     * @param $mesAnterior
     * @param $mesAtual
     */
    public function criarEspelhoMesAnterior($mesAnterior, $mesAtual)
    {
        $gruposEspelho = $this->getAll(['date' => $mesAnterior]);
        foreach ($gruposEspelho as $grupo) {
            $novoGrupo = [
                'nome' => $grupo['nome'],
                'user_id' => auth()->user()->id,
                'tipo_grupo_id' => $grupo['tipo_grupo_id'],
                'data' => Carbon::createFromFormat('Y - m', $mesAtual)->firstOfMonth()->format('Y-m-d'),
                'color' => $grupo['color']
            ];
            $id = parent::save($novoGrupo)->id;
            if ($grupo['items']) {
                foreach ($grupo['items'] as $item) {
                    $novoItem = [
                        'nome' => $item['nome'],
                        'vl_esperado' => $item['vl_esperado'],
                        'vl_planejado' => $item['vl_planejado'],
                        'vl_recebido' => $item['vl_recebido'],
                        'grupo_id' => $id
                    ];
                    if (!empty($item['vl_saldo_inicial'])) {
                        $novoItem['vl_saldo_inicial'] = ($item['vl_saldo_inicial'] + $item['vl_esperado']) - $item['vl_gasto'];
                    }

                    if (!empty($item['tipo_item_id'])) {
                        $novoItem['tipo_item_id'] = $item['tipo_item_id'];
                    }

                    $this->itemService->save($novoItem);
                }
            }
        }
    }

    /**
     * Cria apenas um grupo pro mês solicitado
     * @param $date
     * @return mixed
     */
    public function criarGruposDefault($date)
    {
        $this->criarReceitas($date);
        $this->criarDoacoes($date);
        $this->criarEconomias($date);
        $this->criarMoradia($date);
        $this->criarTransportes($date);
        $this->criarAlimentacao($date);
        $this->criarPessoal($date);
        $this->criarEstiloDeVida($date);
        $this->criarSaude($date);
        $this->criarSeguros($date);
        $this->criarDividas($date);
    }

    /**
     * Cria receitas
     * @param $date
     */
    public function criarReceitas($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Receitas',
            auth()->user()->id,
            TipoGrupo::RECEITAS,
            $date
        );
        $this->criarItemAbstract(
            'Salário 1',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Salário 2',
            $grupo->id
        );
    }

    /**
     * Criar doações
     * @param $date
     */
    public function criarDoacoes($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Doação',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Igreja',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Caridade',
            $grupo->id
        );
    }

    /**
     * Criar economias
     * @param $date
     */
    public function criarEconomias($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Economias',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Fundo de emergência',
            $grupo->id
        );
    }

    /**
     * Criar Moradir
     * @param $date
     */
    public function criarMoradia($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Moradia',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Financiamento/Aluguel',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Água',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Luz',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Gás',
            $grupo->id
        );
        $this->criarItemAbstract(
            'TV',
            $grupo->id
        );
    }

    /**
     * Criar Transporte
     * @param $date
     */
    public function criarTransportes($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Transporte',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Gasolina',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Manutenção',
            $grupo->id
        );
    }

    /**
     * Criar Alimentação
     * @param $date
     */
    public function criarAlimentacao($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Alimentação',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Mercado',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Restaurantes',
            $grupo->id
        );
    }

    /**
     * Criar Pessoal
     * @param $date
     */
    public function criarPessoal($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Pessoal',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Roupa',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Telefone',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Diversão',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Cabelo/Cosmético',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Mensalidades',
            $grupo->id
        );
    }

    /**
     * Criar Estilo de vida
     * @param $date
     */
    public function criarEstiloDeVida($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Estilo de vida',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Creche',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Entretenimento',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Outros',
            $grupo->id
        );
    }

    /**
     * Criar Saúde
     * @param $date
     */
    public function criarSaude($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Saúde',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Academia',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Remédios/Vitaminas',
            $grupo->id
        );
    }

    /**
     * Criar Saúde
     * @param $date
     */
    public function criarSeguros($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Seguros',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Convênios',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Seguro de vida',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Seguro de carro',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Seguro aluguel',
            $grupo->id
        );
    }

    /**
     * Dívidas
     * @param $date
     */
    public function criarDividas($date)
    {
        $grupo = $this->criarGrupoAbstract(
            'Dívidas',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            $date
        );
        $this->criarItemAbstract(
            'Cartão de crédito',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Financiamento do carro',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Financiamento estudantil',
            $grupo->id
        );
        $this->criarItemAbstract(
            'Empréstimo pessoal',
            $grupo->id
        );
    }

    /**
     * @param $nomeGrupo
     * @param $userId
     * @param $tipoGrupoId
     * @param $date
     */
    public function criarGrupoAbstract($nomeGrupo, $userId, $tipoGrupoId, $date)
    {
        $grupo = [
            'nome' => $nomeGrupo,
            'user_id' => $userId,
            'tipo_grupo_id' => $tipoGrupoId,
            'date' => $date
        ];
        return $this->save($grupo);
    }

    public function criarItemAbstract($nomeItem, $grupoId)
    {
        $item = [
            'nome' => $nomeItem,
            'grupo_id' => $grupoId
        ];
        return $this->itemService->save($item);
    }

    /**
     * Remove um grupo e todos seus itens relacionados
     * @param $date
     */
    public function delete($id)
    {
        $items = $this->find($id)->items()->get();
        foreach ($items as $item) {
            $this->itemService->getRepository()->delete($item->id);
            $this->itemService->reordenarItensOnDelete($item);
        }
        return parent::delete($id);
    }

    /**
     * Limpar o mês inteiro pelo usuário
     * @param $params
     */
    public function limparMes($params)
    {
        $grupos = $this->getAll($params);
        foreach ($grupos as $grupo) {
            $this->delete($grupo['id']);
        }
    }
}
