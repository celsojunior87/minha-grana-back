<?php


namespace App\Services;


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
                    $grupos[$key]['items'][$keyItems]['class_vl_planeje'] = ($grupos[$key]['items'][$keyItems]['vl_planeje'] < 0) ? 'item_vl valor_negativo' : 'item_vl valor_positivo';
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

//    public function regraCoresLinhaGrupo($item)
//    {
//        dd($items);
//        foreach ($item as $items) {
//
//
//        }
//    }

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
        $grupos = $this->getMovimentacaoByGrupos($grupos);
        dd($grupos);

        $totalMovimentacaoReceitas = $this->somaTotalReceitaMovimentacao($grupos);
        $totalReceita = $this->getTotalReceitas($grupos);
        $totalDespesa = $this->getTotalDespesa($grupos);


        if ($totalReceita > $totalDespesa) {
            $total = $totalReceita - $totalDespesa;
            return [
                'frase' => ' Está sobrando',
                'total' => $total,
                'color' => '',
                'class' => '',
            ];
        }
        if ($totalReceita < $totalDespesa) {
            $total = $totalReceita - $totalDespesa;
            return [
                'frase' => ' Você Ultrapassou',
                'total' => $total,
                'color' => 'red',
                'class' => 'frase_ultrapassou',
            ];
        }

        if ($totalMovimentacaoReceitas > $totalReceita) {
            $total = $totalMovimentacaoReceitas - $totalReceita;
            return [
                'frase' => ' VOCÊ PLANEJOU RECEBER A MAIS',
                'total' => $total,
            ];
        }

        if ($totalMovimentacaoReceitas < $totalReceita) {

            $total = $totalReceita - $totalMovimentacaoReceitas;
            return [
                'frase' => ' VOCÊ AINDA TEM DISPONIVEL PARA PLANEJAR',
                'total' => $total,
            ];

        }

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
                $arrItemsMovimentacao[$key]['color'] = $this->definirCorPorTipoGrupo($itemMovimentacao);
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

    public function definirCorPorTipoGrupo(ItemMovimentacao $itemMovimentacao)
    {
        $tipoGrupo = $itemMovimentacao->item()->first()->grupo()->first()->tipoGrupo()->first()->id;
        return ($tipoGrupo === TipoGrupo::RECEITAS ? 'green' : 'red');
    }

    /**
     * se o valor realizado for igual a 0 o saldo esperado deverá ser igual ao planejado
     */

    public function calculaSaldoEsperado(ItemMovimentacao $itemMovimentacao, $arrItemsMovimentacaoAnterior, $key)
    {
        if ($key == 0) {
            if ($itemMovimentacao->vl_realizado == '0.00') {
                return $itemMovimentacao->vl_planejado;
            }
            return $itemMovimentacao->vl_realizado;
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
                'color' => 'green',
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
        return parent::save($data);
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
                'data' => Carbon::createFromFormat('Y-m', $mesAtual)->firstOfMonth()->format('Y-m-d')
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
        $this->criarCasa($date);
        $this->criarDividas($date);
    }

    /**
     * Cria receitas
     * @param $date
     */
    public function criarReceitas($date)
    {
        $this->criarGrupoAbstract(
            'Receitas',
            auth()->user()->id,
            TipoGrupo::RECEITAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        );
    }

    /**
     * Criar doações
     * @param $date
     */
    public function criarDoacoes($date)
    {
        $this->criarGrupoAbstract(
            'Doacoes',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        );
    }

    /**
     * Criar economias
     * @param $date
     */
    public function criarEconomias($date)
    {
        $this->criarGrupoAbstract(
            'Economias',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        );
    }

    /**
     * Criar casas
     * @param $date
     */
    public function criarCasa($date)
    {
        $this->criarGrupoAbstract(
            'Casa',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        );
    }

    /**
     * Dívidas
     * @param $date
     */
    public function criarDividas($date)
    {
        $this->criarGrupoAbstract(
            'Dívidas',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
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
        $this->save($grupo);
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
